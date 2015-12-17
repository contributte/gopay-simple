<?php

/**
 * Test: Markette/GopaySimple/GopaySimple (AUTHENTICATION)
 */

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

test(function () {
    $gopay = new AuthGopay('foo', 'bar');
    $response = $gopay->doAuth();

    Assert::equal('foo', $response->PHP_AUTH_USER);
    Assert::equal('bar', $response->PHP_AUTH_PW);
});
