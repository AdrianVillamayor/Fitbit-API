<?php

declare(strict_types=1);

namespace Adrii\User;

use Adrii\OAuth\OAuth;
use Exception;

class Config
{
    const API_URL     = 'https://api.fitbit.com/1/user/';
    private $authorizator;
    private string $period;

    private array $periods = array(
        'ONE_DAY'       => '1d',
        'SEVEN_DAYS'    => '7d',
        'THIRTY_DAYS'   => '30d',
        'ONE_WEEK'      => '1w',
        'ONE_MONTH'     => '1m',
        'THREE_MONTHS'  => '3m',
        'SIX_MONTHS'    => '6m',
        'ONE_YEAR'      => '1y',
        'MAX_PERIOD'    => 'max',
        'DAILY'         => 'daily',
        'WEEKLY'        => 'weekly'
    );


    public function __construct(OAuth $authorizator)
    {
        $this->authorizator = $authorizator;
    }

    public function getApiUri(string $uri): string
    {
        return self::API_URL . $this->authorizator->getUserId() . "/" . $uri;
    }

    public function getBearer(): string
    {
        return $this->authorizator->getAccessToken();
    }

    public function setPeriod(string $period): void
    {
        $period = strtoupper($period);
        $this->checkValidity($this->periods, $period);
        $this->period = $this->periods[$period];
    }

    public function checkDate(string $date): bool
    {
        return preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date);
    }

    public function getPeriod(): string
    {
        return $this->period;
    }

    private function checkValidity($constants, $value): void
    {
        if (!array_key_exists($value, $constants)) {
            throw new Exception(
                'The value ' . $value . ' is not a valid value'
            );
        }
    }
}
