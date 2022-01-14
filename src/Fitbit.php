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

    public function activities()
    {
        return $this->activities;
    }
}
