<?php
declare(strict_types=1);

namespace PolarisDC\Exact\ExactOnlineConnector;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Picqer\Financials\Exact\ApiException;
use PolarisDC\Exact\ExactOnlineConnector\Models\OAuthToken;

class ExactOnlineService
{
    const CACHE_LOCK_DURATION = 40;
    const CACHE_LOCK_TIMEOUT = 30;

    private OAuthToken $oAuthToken;
    private Lock $atomicLock;

    private string $exactClientId;

    public function __construct()
    {
        $this->exactClientId = config('exact-online.client_id');
        $this->oAuthToken = OAuthToken::firstOrCreate(['client_id' => $this->exactClientId]);

        // Creates the atomic cache look to prevent race conditions.
        $this->atomicLock = Cache::lock("exact-online-connector-token-lock-{$this->exactClientId}", self::CACHE_LOCK_DURATION);
    }

    /**
     * Returns an Exact Online Connection
     *
     * @return Connection
     * @throws Exception
     * @throws ApiException
     */
    public function getConnection(): Connection
    {
        $connection = $this->initializeConnection();

        $connection->setAccessToken($this->oAuthToken->access_token);
        $connection->setRefreshToken($this->oAuthToken->refresh_token);
        $connection->setTokenExpires($this->oAuthToken->expires_in);

        if ($connection->needsAuthentication()) {
            throw new Exception('Exact Online Connector: The Exact client is not authenticated.');
        }

        $connection->connect();

        return $connection;
    }


    /**
     * Authorize with Exact Online.
     * Starts an oAuth request by redirecting to the Exact Online Authorization url.
     *
     * @throws ApiException
     */
    public function authorizeClient(): void
    {
        if ($this->clientIsAuthorized()) {
            logger()->error('Exact Online Connector: The client has already been authorized.', ['exactClientId' => $this->exactClientId]);
            return;
        }

        logger()->debug('Exact Online Connector: Starting Exact Online authorization flow.');

        $connection = $this->initializeConnection();

        logger()->debug('Exact Online Connector: Redirecting to Exact Online for authorization.', ['exactClientId' => $this->exactClientId, 'callbackUrl' => $connection->getRedirectUrl()]);

        $connection->redirectForAuthorization();
    }

    /**
     * Authorize with Exact Online.
     * Completes the oAuth request, makes an initial connection and stores the OAuth tokens.
     *
     * @param mixed $authorizationCode
     * @return bool
     */
    public function finishAuthorizationClient($authorizationCode): bool
    {
        if ($this->clientIsAuthorized()) {
            logger()->error('Exact Online Connector: The client has already been authorized.', ['exactClientId' => $this->exactClientId, 'authorizationCode' => $authorizationCode]);
            return true;
        }

        try {
            logger()->debug('Exact Online Connector: Received authorization callback.', ['exactClientId' => $this->exactClientId, 'authorizationCode' => $authorizationCode]);

            $connection = $this->initializeConnection();
            $connection->setAuthorizationCode($authorizationCode);
            $connection->connect();

            // When getting the access token and refresh token for the first time,
            // you need to refresh the token instead of using the access token.
            // Else the refresh token does not work!
            // Thanks @BrunoGoossens https://github.com/picqer/exact-php-client/issues/385#issuecomment-713906247

            $this->oAuthToken->update(['access_token' => null, 'expires_in' => 0]);
            $connection->setAccessToken(null);
            $connection->setTokenExpires(0);
            $connection->connect();

            logger()->debug('Exact Online Connector: Authorization flow completed successfully.', ['exactClientId' => $this->exactClientId, 'authorizationCode' => $authorizationCode]);
            return true;
        } catch (ApiException $exception) {
            logger()->error('Exact Online Connector: Exception during authorization flow.', ['exactClientId' => $this->exactClientId, 'exception' => $exception]);
            return false;
        }
    }

    /**
     * Disconnect the client from Exact online, by removing the OAuth access tokens.
     */
    public function disconnectClient()
    {
        logger('Exact Online Connector: The Exact client is now disconnected.');

        return $this->oAuthToken->update([
            'access_token' => null,
            'refresh_token' => null,
            'expires_in' => 0
        ]);
    }

    private function initializeConnection(): Connection
    {
        $connection = new Connection();
        $connection->setExactClientId(config('exact-online.client_id'));
        $connection->setExactClientSecret(config('exact-online.client_secret'));
        $connection->setExactWebhookSecret(config('exact-online.client_webhook_secret'));

        $connection->setBaseUrl(config('exact-online.base_url'));
        $connection->setRedirectUrl(route('exact.callback'));

        if (config('exact-online.division') !== '') {
            $connection->setDivision(config('exact-online.division'));
        }
        if (config('exact-online.language_code') !== '') {
            $connection->setCustomDescriptionLanguage(config('exact-online.language_code'));
        }

        $connection->setAcquireAccessTokenLockCallback([$this, 'aquireAccessTokenLock']);
        $connection->setRefreshAccessTokenCallback([$this, 'refreshAccesToken']);
        $connection->setTokenUpdateCallback([$this, 'updateAccessToken']);
        $connection->setAcquireAccessTokenUnlockCallback([$this, 'releaseAccesTokenLock']);

        return $connection;
    }

    /**
     * Checks if we have authenticated / connected our client before, based on the OAuthToken object.
     *
     * @return bool
     */
    private function clientIsAuthorized()
    {
        return ($this->oAuthToken->access_token && $this->oAuthToken->refresh_token && $this->oAuthToken->expires_in);
    }

    /*
     |--------------------------------------------------------------------------
     | Callback functions
     |--------------------------------------------------------------------------
     |
     | These methods will get called from the Picqer connection on token refreshes.
     |
     */

    public function aquireAccessTokenLock(Connection $connection)
    {
        logger('Exact Online Connector: Starting the OAuth access token refresh.', $this->getConnectionInfo($connection));

        try {
            $this->atomicLock->block(self::CACHE_LOCK_TIMEOUT);

            // Lock acquired after waiting maximum of 30 seconds...
            logger('Exact Online Connector: Acquired the atomic refresh lock.', $this->getConnectionInfo($connection));

        } catch (LockTimeoutException $exception) {
            throw new ApiException('Exact Online Connector: Could not acquire the atomic lock to refresh the OAuth tokens, the lock timed out.');
        }
    }

    public function refreshAccesToken(Connection $connection)
    {
        $this->oAuthToken->refresh();

        $connection->setAccessToken($this->oAuthToken->access_token);
        $connection->setRefreshToken($this->oAuthToken->refresh_token);
        $connection->setTokenExpires($this->oAuthToken->expires_in);


        logger('Exact Online Connector: Refreshing connection with up to date tokens from the database.', $this->getConnectionInfo($connection));
    }

    public function updateAccessToken(Connection $connection)
    {
        logger('Exact Online Connector: Saving the new received OAuth access token from Exact Online in the database.', $this->getConnectionInfo($connection));

        $this->oAuthToken->update([
            'access_token' => $connection->getAccessToken(),
            'refresh_token' => $connection->getRefreshToken(),
            'expires_in' => $connection->getTokenExpires()
        ]);
    }

    public function releaseAccesTokenLock(Connection $connection)
    {
        logger('Exact Online Connector: Releasing the atomic refresh lock.', $this->getConnectionInfo($connection));

        optional($this->atomicLock)->release();

        logger('Exact Online Connector: Done with the OAuth acces token refresh.', $this->getConnectionInfo($connection));
    }

    /**
     * Returns the connection info (used for debugging)
     *
     * @param Connection|null $connection
     * @return array
     */
    private function getConnectionInfo(Connection $connection = null)
    {
        return [
            'exact_client_id' => $connection->getExactClientId(),
            'lock_id' => $this->atomicLock->owner(),
            'access_token' => $connection->getAccessToken(),
            'refresh_token' => $connection->getRefreshToken(),
            'expires_in' => $connection->getTokenExpires()
        ];
    }
}