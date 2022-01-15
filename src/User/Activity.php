<?php

declare(strict_types=1);

namespace Adrii\User;

use Adrii\User\Config;
use Adrii\OAuth\OAuth;
use Adrii\Http\Request;

class Activity
{
    private $config;

    public function __construct(OAuth $authorizator)
    {
        $this->config        = new Config($authorizator);
        $this->http_request  = new Request();
    }

    /**
     * @param period	required	Supported: daily| weekly
     */
    public function goals(string $period)
    {
        $this->config->setPeriod($period);

        $period = $this->config->getPeriod();

        $url     = $this->config->getApiUri("activities/goals/{$period}.json");
        $bearer  = $this->config->getBearer();
        $headers = ["Authorization" => "Bearer {$bearer}"];

        $response = $this->http_request->get($url, [], $headers);

        return $response;
    }

    /**
     * @param beforeDate  optional/required	   Only yyyy-MM-dd is required. Either beforeDate or afterDate must be specified.	($date or $timestamp)
     * @param afterDate	optional/required	Only yyyy-MM-dd is required. Either beforeDate or afterDate must be specified.	($date or $timestamp
     * @param sort	required	The sort order of entries by date. Use asc (ascending) when using afterDate. Use desc (descending) when sing before date.
     * @param limit	required	The number of entries returned (maximum = 100).
     * @param offset required	Supported: 0
     */
    public function log_list(?string $beforeDate = null, ?string $afterDate = null, string $sort = 'desc', int $limit = 100, int $offset = 0)
    {
        $query_params = array(
            'sort'      => $sort,
            'limit'     => $limit,
            'offset'    => $offset
        );

        if (isset($beforeDate)) {
            if ($this->config->checkDate($beforeDate)) {
                $query_params['beforeDate'] = $beforeDate;
            } else {
                throw new Exception("Error in the format, required yyyy-MM-dd format on beforeDate", 1);
            }
        }

        if (isset($afterDate)) {
            if ($this->config->checkDate($afterDate)) {
                $query_params['afterDate'] = $afterDate;
            } else {
                throw new Exception("Error in the format, required yyyy-MM-dd format on afterDate", 1);
            }
        }

        $url     = $this->config->getApiUri("activities/list.json");
        $bearer  = $this->config->getBearer();
        $headers = ["Authorization" => "Bearer {$bearer}"];

        $response = $this->http_request->get($url, $query_params, $headers);

        return $response;
    }
}
