<?php

namespace Markette\GopaySimple;

use stdClass;

class GopaySimple
{

    /** Env modes */
    const PROD = 1;
    const DEV = 2;

    /** Endpoints types */
    const ENDPOINT_OAUTH = 'oauth';
    const ENDPOINT_API = 'api';
    const ENDPOINT_JS = 'js';

    /** Endpoints */
    const URLS = [
        self::PROD => [
            self::ENDPOINT_OAUTH => 'https://gate.gopay.cz/api/oauth2/token',
            self::ENDPOINT_API => 'https://gate.gopay.cz/api',
            self::ENDPOINT_JS => 'https://gate.gopay.cz/gp-gw/js/embed.js',
        ],
        self::DEV => [
            self::ENDPOINT_OAUTH => 'https://gw.sandbox.gopay.com/api/oauth2/token',
            self::ENDPOINT_API => 'https://gw.sandbox.gopay.com/api',
            self::ENDPOINT_JS => 'https://gw.sandbox.gopay.com/gp-gw/js/embed.js',
        ]
    ];

    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /** @var int */
    private $mode = self::PROD;

    /** @var stdClass */
    protected $token;

    /**
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct($clientId, $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * @param int $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode === self::PROD ? self::PROD : self::DEV;
    }

    /**
     * API *********************************************************************
     */

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $args
     * @return mixed
     * @throws GopayException
     */
    public function call($method, $endpoint, $args = [])
    {
        if (!$this->token) {
            $this->authenticate(['scope' => 'payment-all']);
        }

        return $this->makeRequest(strtoupper($method), $endpoint, $args);
    }

    /**
     * AUTH ********************************************************************
     */

    /**
     * @param array $args
     * @return stdClass
     * @throws GopayException
     */
    protected function authenticate($args)
    {
        $data = [
            'grant_type' => 'client_credentials',
            'scope' => $args['scope'],
        ];

        $url = $this->getEndpoint(self::ENDPOINT_OAUTH);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Markette/GopaySimple/1.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERPWD, $this->clientId . ':' . $this->clientSecret);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $response = @json_decode($result);
        curl_close($ch);

        if (!$response) {
            // cURL errors
            throw new GopayException("Authorization failed ($url) [$error]", $errno, NULL, $args);
        }

        if (isset($response->errors)) {
            // GoPay errors
            $error = $response->errors[0];
            throw new GopayException(GopayException::format($error), $error->error_code, NULL, $args);
        }

        return $this->token = $response;
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $args
     * @return bool|mixed
     * @throws GopayException
     */
    protected function makeRequest($method, $endpoint, $args = [])
    {
        if (!$this->token) throw new GopayException('Unknown token');

        $headers = [
            'Authorization:Bearer ' . $this->token->access_token,
            'Accept: application/json'
        ];

        $url = $this->getEndpointUrl($endpoint);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Markette/GopaySimple/1.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        switch ($method) {
            case 'GET':
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
                break;

            case 'POST':
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
                break;

            default:
                throw new GopayException("Unsupported HTTP method ($method)", NULL, $args);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $response = @json_decode($result);
        curl_close($ch);

        if (!$response) {
            // cURL errors
            throw new GopayException("Request failed ($method+$url) [$error]", $errno, NULL, $args);
        } else if (isset($response->errors)) {
            // GoPay errors
            $error = $response->errors[0];
            throw new GopayException(GopayException::format($error), $error->error_code, NULL, $args);
        }

        return $response;
    }

    /**
     * HELPERS *****************************************************************
     */

    /**
     * @param string $type
     * @return string
     */
    protected function getEndpoint($type)
    {
        return self::URLS[$this->mode][$type];
    }

    /**
     * @param string $uri
     * @return string
     */
    protected function getEndpointUrl($uri)
    {
        return $this->getEndpoint(self::ENDPOINT_API) . '/' . trim($uri, '/');
    }

}
