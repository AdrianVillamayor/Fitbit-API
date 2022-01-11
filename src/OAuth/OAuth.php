<?php
declare(strict_types=1);

namespace Adrii\OAuth;

use Adrii\OAuth\Config;

class OAuth
{
    const TOKEN_URL     = 'https://api.fitbit.com/oauth2/token';
    const AUTHORIZE_URL = 'https://www.fitbit.com/oauth2/authorize';

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getAuthUri()
    {
        return $this->AUTHORIZE_URL . '?' . http_build_query([
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
