<?php

declare(strict_types=1);

namespace TimothyDC\ExactOnline\LaravelClient;

use Illuminate\Support\Carbon;
use TimothyDC\ExactOnline\BaseClient\Authentication\AccessToken;
use TimothyDC\ExactOnline\BaseClient\Interfaces\AccessTokenInterface;
use TimothyDC\ExactOnline\BaseClient\Interfaces\TokenVaultInterface;
use TimothyDC\ExactOnline\LaravelClient\Models\OAuthToken;

class TokenVault implements TokenVaultInterface
{
    protected string $clientId;

    public function store(AccessTokenInterface $accessToken): void
    {
        OAuthToken::updateOrCreate([
            'client_id' => $this->clientId,
        ], [
            'access_token' => $accessToken->getAccessToken(),
            'refresh_token' => $accessToken->getRefreshToken(),
            'expires_at' => Carbon::createFromTimestamp($accessToken->getExpiresAt(), config('app.timezone')),
        ]);
    }

    public function retrieve(): AccessTokenInterface
    {
        $oauthToken = OAuthToken::whereClientId($this->clientId)->first();

        return $oauthToken
            ? $this->makeToken($oauthToken->access_token, $oauthToken->refresh_token, optional($oauthToken->expires_at)->timestamp ?? 0)
            : $this->makeToken(null, null, 0);
    }

    public function remove(string $accessToken): void
    {
        OAuthToken::whereClientId($this->clientId)->delete();
    }

    public function setClientId($clientId): self
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function makeToken(?string $accesToken, ?string $refreshToken, int $expiresAt): AccessTokenInterface
    {
        return new AccessToken($accesToken, $refreshToken, $expiresAt);
    }

    public function clear(): void
    {
        OAuthToken::whereClientId($this->clientId)->delete();
    }
}
