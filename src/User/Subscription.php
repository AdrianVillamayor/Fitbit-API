<?php

declare(strict_types=1);

namespace Adrii\User;

use Adrii\User\Config;
use Adrii\OAuth\OAuth;
use Adrii\Http\Request;

class Subscription
{
    private $config;

    public function __construct(OAuth $authorizator)
    {
        $this->config        = new Config($authorizator);
        $this->http_request  = new Request();
    }

    /**
     * @param collection_path	required	Supported: activities | body | foods | sleep | userRevokedAccess
     */
    public function get(string $collection_path, $subscription_id)
    {
        $url     = $this->config->getApiUri("{$collection_path}/apiSubscriptions/{$subscription_id}.json");
        $bearer  = $this->config->getBearer();
        $headers = ["Authorization" => "Bearer {$bearer}"];

        $response = $this->http_request->get($url, [], $headers);

        return $response;
    }

    /**
     * @param collection_path	required	Supported: activities | body | foods | sleep | userRevokedAccess
     */
    public function post(string $collection_path, $subscription_id)
    {
        $url     = $this->config->getApiUri("{$collection_path}/apiSubscriptions.json");
        $bearer  = $this->config->getBearer();
        $headers = ["Authorization" => "Bearer {$bearer}"];

        $post_params = array(
            'user-id'           => $this->config->getUserId(),
            'collection_path'   => $collection_path,
            'subscription-id'   => $subscription_id
        );

        list($response, $error, $msg) = $this->http_request->post($url, $post_params, $headers);

        return $response;
    }

    /**
     * @param collection_path	required	Supported: activities | body | foods | sleep | userRevokedAccess
     */
    public function delete(string $collection_path, $subscription_id)
    {
        $url     = $this->config->getApiUri("{$collection_path}/apiSubscriptions/{$subscription_id}.json");
        $bearer  = $this->config->getBearer();
        $headers = ["Authorization" => "Bearer {$bearer}"];

        $post_params = array(
            'user-id'           => $this->config->getUserId(),
            'collection_path'   => $collection_path,
            'subscription-id'   => $subscription_id
        );

        list($response, $error, $msg) = $this->http_request->delete($url, $post_params, $headers);

        return $response;
    }
}
