<?php

declare(strict_types=1);

namespace Adrii\OAuth;

use Adrii\OAuth\Config;
use Adrii\Http\Request;
use Exception;

class OAuth
{
    const TOKEN_URL      = 'https://api.fitbit.com/oauth2/token';
    const REVOKE_URL     = 'https://api.fitbit.com/oauth2/revoke';
    const INTROSPECT_URL = 'https://api.fitbit.com/oauth2/introspect';
    const AUTHORIZE_URL  = 'https://www.fitbit.com/oauth2/authorize';

    private $access_token = null;
    private $refresh_token = null;
    private $user_id = null;
    private $config;
    private $http_request;

    public function __construct(Config $config)
    {
        $this->config       = $config;
        $this->http_request = new Request();
    }

    public function getAuthUri(): string
    {
        $auth_uri = self::AUTHORIZE_URL . '?' . http_build_query([
            'client_id'             => $this->config->getClientId(),
            'scope' => implode(' ', [
                'activity',
                'nutrition',
                'heartrate',
                'location',
                'nutrition',
                'profile',
                'settings',
                'sleep',
                'social',
                'weight',
            ]),
            'response_type'         => 'code',
            'redirect_uri'          => $this->config->getRedirectUrl(),
            'expires_in'            => '604800',
        ]);

        if ($this->config->getStaticParams() !== null) {
            $auth_uri = "{$auth_uri}&state={$this->config->getStaticParams()}";
        }

        return $auth_uri;
    }

    public function getOAuthTokens(): ?array
    {
        if ($this->checkAuthorized("code")) {
            $post_params = array(
                'client_id'     => $this->config->getClientId(),
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => $this->config->getRedirectUrl(),
                'code'          => $this->config->getCode()
            );

            $headers =  ($this->config->getOAuthType() === "server") ? ["Authorization" => "Basic {$this->config->getBasicAuth()}"] : [];

            list($response, $error, $msg) = $this->http_request->post(self::TOKEN_URL, $post_params, $headers);

            if ($error === false) {
                $this->setAuth($response);
                return $response;
            }
        }

        return null;
    }

    public function checkOAuthTokens(): ?array
    {
        if ($this->checkAuthorized("access_token")) {
            $post_params = array(
                'client_id'     => $this->config->getClientId(),
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => $this->config->getRedirectUrl(),
                'code'          => $this->config->getCode()
            );

            $headers = ["Authorization" => "Bearer {$this->getAccessToken()}"];

            list($response, $error, $msg) = $this->http_request->post(self::INTROSPECT_URL, $post_params, $headers);

            if ($error === false) {
                $state = $response['active'];

                if ($state === false) {
                    $response = $this->refreshToken();
                }

                $response['state'] = $state;
                return $response;
            }
        }

        return null;
    }

    public function refreshToken(): ?array
    {
        if ($this->checkAuthorized("refresh_token")) {
            $post_params = array(
                'grant_type'     => 'refresh_token',
                'refresh_token'  => $this->getRefreshToken(),
            );

            $headers = ["Authorization" => "Basic {$this->config->getBasicAuth()}"];

            list($response, $error, $msg) = $this->http_request->post(self::TOKEN_URL, $post_params, $headers);

            if ($error === false) {
                $this->setAuth($response);
                return $response;
            }
        }

        return null;
    }

    public function revokeToken(): ?array
    {
        if ($this->checkAuthorized("access_token")) {
            $post_params = array(
                'token'  => $this->getAccessToken()
            );

            $headers = ["Authorization" => "Basic {$this->config->getBasicAuth()}"];

            list($response, $error, $msg) = $this->http_request->post(self::REVOKE_URL, $post_params, $headers);

            if ($error === false) {
                return $response;
            }
        }

        return null;
    }

    public function setAuthorizationCode(string $code): void
    {
        $this->config->setCode($code);
    }

    public function setAccessToken(string $access_token): void
    {
        $this->access_token = $access_token;
    }

    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    public function getRefreshToken(): string
    {
        return $this->refresh_token;
    }

    public function setRefreshToken(string $refresh_token): void
    {
        $this->refresh_token = $refresh_token;
    }

    public function setUserId(string $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function setAuth(array $response): void
    {
        $this->setAccessToken($response['access_token']);
        $this->setRefreshToken($response['refresh_token']);
        $this->setUserId($response['user_id']);
    }

    /**
     * @param auth_check required Supported: access_token | refresh_token | user_id | code
     */
    public function checkAuthorized(string $auth_check): bool
    {
        switch ($auth_check) {
            case 'access_token':
                if (is_null($this->access_token)) {
                    throw new Exception('No access_token');
                }
                break;
            case 'refresh_token':
                if (is_null($this->refresh_token)) {
                    throw new Exception('No arefresh_token');
                }
                break;
            case 'user_id':
                if (is_null($this->user_id)) {
                    throw new Exception('No user_id');
                }
                break;
            case 'code':
                if (!$this->config->hasCode()) {
                    throw new Exception('No code');
                }
                break;
        }

        return true;
    }
}
