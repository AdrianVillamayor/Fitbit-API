<?php

declare(strict_types=1);

namespace Adrii\OAuth;

use Adrii\OAuth\Config;
use Adrii\CurlHelper;

class OAuth
{
    const TOKEN_URL     = 'https://api.fitbit.com/oauth2/token';
    const AUTHORIZE_URL = 'https://www.fitbit.com/oauth2/authorize';
    private $challenge;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->setCodeChallenge();
    }

    public function getAuthUri(): string
    {
        $auth_uri = self::AUTHORIZE_URL . '?' . http_build_query([
            'client_id'             => $this->config->getClientId(),
            'code_challenge'        => $this->getCodeChallenge(),
            'code_challenge_method' => 'S256',
            'scope' => implode(' ', [
                'activity',
                'nutrition',
                'heartrate',
                'location',
                'nutrition',
                'profile',
                'settings',
                'sleep',
                'social',
                'weight',
            ]),
            'response_type'         => 'code',
            'redirect_uri'          => $this->config->getRedirectUrl(),
            'expires_in'            => '604800',
        ]);

        if ($this->config->getStaticParams() !== null) {
            $auth_uri = "{auth_uri}&state={$this->config->getStaticParams()}";
        }

        return $auth_uri;
    }

    private function base64url_encode(string $plainText): string
    {
        $base64 = base64_encode($plainText);
        $base64 = trim($base64, "=");
        $base64url = strtr($base64, '+/', '-_');

        return $base64url;
    }

    private function setCodeChallenge(): void
    {
        $random     = bin2hex(openssl_random_pseudo_bytes(64));
        $verifier   = $this->base64url_encode(pack('H*', $random));
        $challenge  = $this->base64url_encode(pack('H*', hash('sha256', $verifier)));

        $this->callenge = $challenge;
    }

    private function getCodeChallenge(): string
    {
        return $this->challenge;
    }

    public function getAccessToken(string $code): ?array
    {
        $curl  = new CurlHelper();

        $curl->setUrl(self::TOKEN_URL);

        $curl->setPostRaw([
            'client_id'  => $this->config->getClientId(),
            'grant_type' => 'authorization_code',
            'code'       => $code
        ]);

        if ($this->config->getOAuthType() === "server") {
            $curl->setHeaders([
                "Authorization" => "Basic {$this->config->getBasicAuth()}"
            ]);
        }

        $curl->setMime("form");

        $curl->execute();

        $response   = $curl->response();
        $code       = $curl->http_code();

        return $response;
    }

    public function setAuthorizationCode(string $code): void
    {
        $this->config->setCode($code);
    }

    public function isAuthorized(): bool
    {
        return $this->config->hasCode();
    }
}
