<?php

declare(strict_types=1);

namespace Adrii\User;

use Adrii\User\Config;
use Adrii\OAuth\OAuth;
use Adrii\CurlHelper;

class Activity
{
    private $config;
   
    public function __construct(OAuth $authorizator)
    {
        $this->config = new Config($authorizator);
    }

    public function goals(string $period)
    {
        $this->config->setPeriod($period);

        $period = $this->config->getPeriod();
        $url    = $this->config->getApiUri("activities/goals/{$period}.json");
        $bearer = $this->config->getBearer();

        $curl = new CurlHelper();

        $curl->setUrl($url);

        $curl->setHeaders([
            "Authorization" => "Bearer {$bearer}"
        ]);

        $curl->setMime("json");

        $curl->setUtf8();

        $curl->execute();

        $response   = $curl->response();

        return $response;
    }
}
