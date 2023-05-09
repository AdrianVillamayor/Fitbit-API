<?php

declare(strict_types=1);

namespace Adrii\User;

use Adrii\User\Config;
use Adrii\OAuth\OAuth;
use Adrii\Http\Request;

class Activity
{
    private $config;
    private $http_request = null;
    private $goals = null;

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

        list($response, $error, $msg) = $this->http_request->get($url, [], $headers);

        if ($error === false) {
            $this->goals = $response['goals'];
        }

        return $this->goals;
    }


    /**
     * @param when  required	Supported: before or after
     * @param date	required	Only yyyy-MM-dd is required.
     * @param sort	required	The sort order of entries by date. Use asc (ascending) when using afterDate. Use desc (descending) when sing before date.
     * @param limit	required	The number of entries returned (maximum = 100).
     * @param offset required	Supported: 0
     */
    public function log_list(string $when = 'before', ?string $date = null, string $sort = 'desc', int $limit = 100, int $offset = 0)
    {
        $query_params = array(
            'sort'      => $sort,
            'limit'     => $limit,
            'offset'    => $offset
        );

        $when = strtolower($when);

        if (!in_array($when, array("before", "after"))) {
            throw new Exception('Error, $when only supports before or after', 1);
        }

        if ($this->config->checkDate($date)) {


            $query_params["{$when}Date"] = $date;
        } else {
            throw new Exception("Error in the format, required yyyy-MM-dd format", 1);
        }

        $url     = $this->config->getApiUri("activities/list.json");
        $bearer  = $this->config->getBearer();
        $headers = ["Authorization" => "Bearer {$bearer}"];

        list($response, $error, $msg) = $this->http_request->get($url, $query_params, $headers);

        return $response;
    }
}
