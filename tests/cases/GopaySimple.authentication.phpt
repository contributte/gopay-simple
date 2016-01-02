<?php

/**
 * Test: Markette/GopaySimple/GopaySimple (AUTHENTICATION)
 */

use Markette\GopaySimple\GopayException;
use Markette\GopaySimple\GopaySimple;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

final class AuthGopay extends GopaySimple
{

    /** @var array */
    private $args = [];

    public function doAuth()
    {
        $this->args = ['type' => 'server'];
        return $this->authenticate(['scope' => 'test']);
    }

    public function doError()
    {
        $this->args = ['type' => 'error'];
        return $this->authenticate(['scope' => 'test']);
    }

    public function doFail()
    {
        $this->args = ['type' => 'fail'];
        return $this->authenticate(['scope' => 'test']);
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
 * Compare credentials
 */
test(function () {
    $gopay = new AuthGopay('foo', 'bar');
    $response = $gopay->doAuth();

    Assert::equal('foo', $response->PHP_AUTH_USER);
    Assert::equal('bar', $response->PHP_AUTH_PW);
});

/**
 * Handle error
 */
test(function () {
    Assert::throws(function () {
        $gopay = new AuthGopay('foo', 'bar');
        $gopay->doError();
    }, GopayException::class);
});

/**
 * Handle fail
 */
test(function () {
    Assert::throws(function () {
        $gopay = new AuthGopay('foo', 'bar');
        $gopay->doFail();
    }, GopayException::class, "Authorization failed (" . PHP_SERVER . "/server.php?type=fail)%a%");
});
