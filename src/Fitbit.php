<?php

declare(strict_types=1);

namespace Adrii;

use Adrii\OAuth\OAuth;
use Adrii\OAuth\Config;
use Adrii\User\Activity;

class Fitbit
{
    private $config;
    private $authorizator;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $redirectUrl,
        string $authType,
        $setStaticParams = null
    ) {
        $this->config       = new Config($clientId, $clientSecret, $redirectUrl, $authType, $setStaticParams);
        $this->authorizator = new OAuth($this->config);

        $this->activities   = new Activity($this->authorizator);
    }

    public function getAuthUri(): string
    {
        return $this->authorizator->getAuthUri();
    }

    public function getAccessToken(string $code): array
    {
        $this->config->setCode($code);
        return $this->authorizator->getOAuthTokens($code);
    }

    public function setAuthToken(string $access_token = null, string $refresh_token = null, string $user_id = null): void
    {
        $this->authorizator->setAuth([
            'access_token'  => $access_token,
            'refresh_token' => $refresh_token,
            'user_id'       => $user_id,
        ]);
    }

    public function activities()
    {
        return $this->activities;
    }
}
