<?php

/**
 * Test: Markette/GopaySimple/GopaySimple (API TOKEN)
 */

use Markette\GopaySimple\GopaySimple;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

final class ApiTokenGopay extends GopaySimple
{

    /** @var array */
    private $args = [];

    protected function authenticate($args)
    {
        $this->args = ['type' => 'token'];
        return parent::authenticate($args);
    }

    protected function makeRequest($method, $endpoint, $args = [])
    {
        $this->args = ['type' => strtolower($method)];
        return parent::makeRequest($method, $endpoint, $args);
    }

    public function doApi($method, $endpoint, array $args = [])
    {
        $this->args = ['type' => strtolower($method)];
        return $this->call($method, $endpoint, $args);
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
    $gopay = new ApiTokenGopay('foo', 'bar');
    $response = $gopay->doApi('POST', 'payments/payment', ['my' => 'data']);

    Assert::equal('data', $response->my);
});