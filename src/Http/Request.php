<?php

declare(strict_types=1);

namespace Adrii\Http;

use Adrii\CurlHelper;

class Request
{
    public function get(string $url, array $get_params = [], array $headers = []): ?array
    {
        $curl = new CurlHelper();

        $curl->setUrl($url);
        $curl->setGetParams($get_params);

        $curl->setHeaders($headers);

        $curl->setMime("json");

        $curl->setUtf8();

        $curl->execute();

        $response           = $curl->response();
        list($error, $msg)  = $curl->parseCode();

        return array($response, $error, $msg);
    }

    public function post(string $url, array $post_params, array $headers = []): ?array
    {
        $curl  = new CurlHelper();

        $curl->setUrl($url);

        $curl->setPostRaw($post_params);

        $curl->setHeaders($headers);

        $curl->setMime("form");

        $curl->execute();

        $response           = $curl->response();
        list($error, $msg)  = $curl->parseCode();

        return array($response, $error, $msg);
    }

    public function delete(string $url, array $post_params, array $headers = []): ?array
    {
        $curl  = new CurlHelper();

        $curl->setUrl($url);

        $curl->setDeleteParams($post_params);

        $curl->setHeaders($headers);

        $curl->setMime("form");

        $curl->execute();

        $response           = $curl->response();
        list($error, $msg)  = $curl->parseCode();

        return array($response, $error, $msg);
    }
}
