<?php

declare(strict_types=1);

namespace Adrii\OAuth;

class Config
{
    private $clientId;
    private $clientSecret;
    private $redirectUrl;
    private $staticParams;
    private $code;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $redirectUrl,
        string $authType,
        string $staticParams = null,
        string $code = null
    ) {
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl  = $redirectUrl;
        $this->authType     = strtolower($authType);
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function getOAuthType(): string
    {
        return $this->authType;
    }

    public function getBasicAuth(): string
    {
        return base64_encode($this->clientId . ":" . $this->clientSecret);
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function hasCode(): bool
    {
        return !is_null($this->code);
    }

    public function getStaticParams(): ?string
    {
        return $this->staticParams;
    }

    /** 
     * @param mixed $raw
    */
    public function setStaticParams($raw): void
    {
        if (is_array($raw)) {
            $raw = http_build_query($raw);
        }

        $this->staticParams = $raw;
    }
}
