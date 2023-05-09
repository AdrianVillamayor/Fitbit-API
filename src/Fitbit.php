<?php

declare(strict_types=1);

namespace Adrii;

use Adrii\OAuth\OAuth;
use Adrii\OAuth\Config;
use Adrii\User\Activity;
use Adrii\User\Subscription;

class Fitbit
{
    private $config;
    private $authorizator;
    private $activities;
    private $subscription;

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
        $this->subscription = new Subscription($this->authorizator);
    }

    public function getAuthUri(): string
    {
        return $this->authorizator->getAuthUri();
    }

    public function getAccessToken(string $code): ?array
    {
        $this->config->setCode($code);
        return $this->authorizator->getOAuthTokens($code);
    }

    public function revokeAccess(): ?array
    {
        return $this->authorizator->revokeToken();
    }

    public function setUserTokens(string $access_token = null, string $refresh_token = null, string $user_id = null): ?array
    {
        $this->authorizator->setAuth([
            'access_token'  => $access_token,
            'refresh_token' => $refresh_token,
            'user_id'       => $user_id,
        ]);

        $check = $this->authorizator->checkOAuthTokens();

        return $check;
    }

    public function activities()
    {
        return $this->activities;
    }
   
    public function subscription()
    {
        return $this->subscription;
    }
}
