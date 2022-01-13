<?php

declare(strict_types=1);

namespace Adrii;

use Adrii\OAuth\OAuth;
use Adrii\OAuth\Config;

class Fitbit
{
    private $userId     = '-';

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $redirectUrl,
        string $authType,
        string $setStaticParams = null
    ) {
        $this->config       = new Config($clientId, $clientSecret, $redirectUrl, $authType, $setStaticParams);
        $this->authorizator = new OAuth($this->config);
    }

    public function getAuthUri(): string
    {
        return $this->authorizator->getAuthUri();
    }

    public function getAccessToken(string $code): array
    {
        return $this->authorizator->getAccessToken($code);
    }
}
