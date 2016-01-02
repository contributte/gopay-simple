<?php

/**
 * Test: Markette/GopaySimple/GopaySimple (API)
 */

use Markette\GopaySimple\GopayException;
use Markette\GopaySimple\GopaySimple;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

final class ApiGopay extends GopaySimple
{

    /** @var array */
    private $args = [];

    public function doAuth()
    {
        $this->args = ['type' => 'token'];
        return $this->authenticate(['scope' => 'test']);
    }

    public function doApi($method, $endpoint, array $args = [])
    {
        $this->args = ['type' => strtolower($method)];
        return $this->call($method, $endpoint, $args);
    }

    public function doApiToken($method, $endpoint, array $args = [])
    {
        $this->args = ['type' => strtolower($method)];
        $this->token = (object)['access_token' => 'foobar'];
        return $this->call($method, $endpoint, $args);
    }

    public function doError()
    {
        $this->args = ['type' => 'error'];
        $this->token = (object)['access_token' => 'foobar'];
        return $this->call('GET', 'invalid');
    }

    public function doFail()
    {
        $this->args = ['type' => 'fail'];
        $this->token = (object)['access_token' => 'foobar'];
        return $this->call('GET', 'invalid');
    }

    protected function getEndpoint($type = NULL)
    {
        return PHP_SERVER . '/server.php?' . http_build_query($this->args);
    }

    protected function getEndpointUrl($uri = NULL)
    {
        $this->args['uri'] = $uri;
        return $this->getEndpoint();
    }

}

/**
 * Compare passed data
 */
test(function () {
    $gopay = new ApiGopay('foo', 'bar');
    $response = $gopay->doApiToken('POST', 'payments/payment', ['my' => 'data']);

    Assert::equal('data', $response->my);
});

/**
 * Compare passed URL
 */
test(function () {
    $gopay = new ApiGopay('foo', 'bar');
    $response = $gopay->doApiToken('GET', 'payments/payment/12345');

    Assert::equal('payments/payment/12345', $response->uri);
});

/**
 * Unsupported HTTP method
 */
test(function () {
    Assert::throws(function () {
        $gopay = new ApiGopay('foo', 'bar');
        $gopay->doApiToken('FOOBAR', 'invalid');
    }, GopayException::class);
});

/**
 * Server returns error
 */
test(function () {
    Assert::throws(function () {
        $gopay = new ApiGopay('foo', 'bar');
        $gopay->doError();
    }, GopayException::class);
});

/**
 * Server failed
 */
test(function () {
    Assert::throws(function () {
        $gopay = new ApiGopay('foo', 'bar');
        $gopay->doFail();
    }, GopayException::class, "Request failed (GET+" . PHP_SERVER . "/server.php?type=fail%a%)%a%");
});
