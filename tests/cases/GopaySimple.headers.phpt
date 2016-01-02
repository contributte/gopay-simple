<?php

/**
 * Test: Markette/GopaySimple/GopaySimple (HEADERS)
 */

use Markette\GopaySimple\GopaySimple;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

final class HeadersGopay extends GopaySimple
{
    public function doCall()
    {
        $this->args = ['type' => 'server'];
        $this->token = (object)['access_token' => 'foobar'];
        return $this->call('GET', 'test');
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
 * Compare useragent
 */
test(function () {
    $gopay = new HeadersGopay('foo', 'bar');

    $response = $gopay->doCall();
    Assert::match('PHP+Markette/GopaySimple/%f%', $response->HTTP_USER_AGENT);

    $gopay->useragent = 'FooBar';
    $response = $gopay->doCall();
    Assert::match('FooBar', $response->HTTP_USER_AGENT);

    $gopay->options[CURLOPT_USERAGENT] = 'FooBarFoo';
    $response = $gopay->doCall();
    Assert::match('FooBarFoo', $response->HTTP_USER_AGENT);
});
