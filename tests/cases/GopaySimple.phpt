<?php

/**
 * Test: Markette/GopaySimple/GopaySimple
 */

use Markette\GopaySimple\GopaySimple;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

final class LocalGopay extends GopaySimple
{

    /** @var array */
    private $args = [];

    public function doAuth()
    {
        $this->args = ['type' => 'server'];
        return $this->authenticate(['scope' => 'test']);
    }

    public function doApi($method, $endpoint, array $args = [])
    {
        $this->args = ['type' => strtolower($method)];
        $this->token = (object)['access_token' => 'foobar'];
        return $this->call($method, $endpoint, $args);
    }

    protected function getEndpoint($type = NULL)
    {
        return 'http://localhost:8080/server.php?' . http_build_query($this->args);
    }

    protected function getEndpointUrl($uri = NULL)
    {
        $this->args['uri'] = $uri;
        return $this->getEndpoint();
    }

}

test(function () {
    $gopay = new LocalGopay('foo', 'bar');
    $response = $gopay->doAuth();

    Assert::equal('foo', $response->PHP_AUTH_USER);
    Assert::equal('bar', $response->PHP_AUTH_PW);
});

test(function () {
    $gopay = new LocalGopay('foo', 'bar');
    $response = $gopay->doApi('POST', 'payments/payment', ['my' => 'data']);

    Assert::equal('data', $response->my);
});

test(function () {
    $gopay = new LocalGopay('foo', 'bar');
    $response = $gopay->doApi('GET', 'payments/payment/12345');

    Assert::equal('payments/payment/12345', $response->uri);
});
