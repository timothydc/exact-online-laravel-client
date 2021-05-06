<?php

declare(strict_types=1);

namespace PolarisDC\Laravel\ExactOnlineConnector;

use Illuminate\Support\Carbon;
use PolarisDC\ExactOnlineConnector\Interfaces\TokenVaultInterface;
use PolarisDC\Laravel\ExactOnlineConnector\Models\OAuthToken;

class TokenVault implements TokenVaultInterface
{
    protected string $clientId;

    public function store(string $accesToken, string $refreshToken, int $expiresAt): void
    {
        OAuthToken::updateOrCreate([
            'client_id' => $this->clientId,
        ], [
            'access_token' => $accesToken,
            'refresh_token' => $refreshToken,
            'expires_at' => Carbon::createFromTimestamp($expiresAt),
        ]);
    }

    public function retrieve(): array
    {
        $oauthToken = OAuthToken::whereClientId($this->clientId)->first();

        return $oauthToken
            ? ['accessToken' => $oauthToken->access_token, 'refreshToken' => $oauthToken->refresh_token, 'expiresAt' => $oauthToken->expires_at->timestamp,]
            : [];
    }

    public function remove(): void
    {
        OAuthToken::whereClientId($this->clientId)->delete();
    }

    public function setClientId($clientId): self
    {
        $this->clientId = $clientId;
        return $this;
    }
}