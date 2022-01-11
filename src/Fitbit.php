<?php

declare(strict_types=1);

namespace Adrii;

use Adrii\OAuth\OAuth;
use Adrii\OAuth\Config;

class Fitbit
{
    private $nonUserUrl = 'https://api.fitbit.com/1/';
    private $baseUrl = 'https://api.fitbit.com/1/user/';
    private $v11Url = 'https://api.fitbit.com/1.1/user/';
    private $v12Url = 'https://api.fitbit.com/1.2/user/';
    private $userId = '-';

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $redirectUrl
    ) {
        $this->config = new Config($clientId, $clientSecret, $redirectUrl);
        $this->authorizator = new OAuth($this->config);
    }

    public function getAuthUri()
    {
        return $this->authorizator->getAuthUri();
    }


    public function getAccessToken($code)
    {
        return $this->authorizator->getAccess($code);
    }

}
