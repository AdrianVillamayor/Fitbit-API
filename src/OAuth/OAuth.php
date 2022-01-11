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
    }

    public function getAuthUri()
    {
        return self::AUTHORIZE_URL . '?' . http_build_query([
            'client_id' => $this->config->getClientId(),
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
            'response_type' => 'code',
            'redirect_uri' => $this->config->getRedirectUrl(),
            'expires_in' => '604800',
        ]);
    }

    private function base64url_encode($plainText)
    {
        $base64 = base64_encode($plainText);
        $base64 = trim($base64, "=");
        $base64url = strtr($base64, '+/', '-_');

        return $base64url;
    }

    private function setCodeChallenge()
    {
        $random     = bin2hex(openssl_random_pseudo_bytes(64));
        $verifier   = $this->base64url_encode(pack('H*', $random));
        $challenge  = $this->base64url_encode(pack('H*', hash('sha256', $verifier)));

        $this->callenge = $challenge;
    }

    private function getCodeChallenge()
    {
        return $this->challenge;
    }

    public function getAccess($url, $code)
    {
        $basic = $this->config->getBasicAuth();
        $curl  = new CurlHelper();

        $curl->setUrl($url);

        $curl->setPostRaw([
            'client_id'  => $this->config->getClientId(),
            'grant_type' => 'authorization_code',
            'code'       => $code
        ]);

        $curl->setHeaders([
            "Authorization" => "Basic {$basic}"
        ]);

        $curl->setMime("form");

        $curl->execute();

        $response   = $curl->response();
        $code       = $curl->http_code();

        var_dump($code);

        return $response;
    }


    public function setAuthorizationCode(string $code)
    {
        $this->config->setCode($code);

        return $this;
    }

    public function isAuthorized()
    {
        return $this->config->hasCode();
    }
}
