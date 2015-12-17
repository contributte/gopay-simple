<?php

/**
 * Test: Markette/GopaySimple/GopaySimple (ENDPOINTS)
 */

use Markette\GopaySimple\GopaySimple;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

final class EndpointGopay extends GopaySimple
{
    public function getEndpoint($type)
    {
        return parent::getEndpoint($type);
    }

    public function getEndpointUrl($uri)
    {
        return parent::getEndpointUrl($uri);
    }
}

test(function () {
    $gopay = new EndpointGopay('foo', 'bar');

    $gopay->setMode($gopay::DEV);
    Assert::match('%a%api/oauth2/token', $gopay->getEndpoint($gopay::ENDPOINT_OAUTH));
    Assert::match('%a%sandbox.gopay.com%a%', $gopay->getEndpoint($gopay::ENDPOINT_API));
    Assert::match('%a%gw.sandbox.gopay%a%', $gopay->getEndpointUrl('foobar'));
    Assert::match('%a%gw.sandbox.gopay%a%foobar', $gopay->getEndpointUrl('foobar'));
    Assert::match('%a%gw.sandbox.gopay%a%foobar', $gopay->getEndpointUrl('foobar/'));
    Assert::match('%a%gw.sandbox.gopay.com%a%embed.js', $gopay->getEndpoint($gopay::ENDPOINT_JS));

    $gopay->setMode($gopay::PROD);
    Assert::match('%a%api/oauth2/token', $gopay->getEndpoint($gopay::ENDPOINT_OAUTH));
    Assert::match('%a%gate.gopay.cz%a%', $gopay->getEndpoint($gopay::ENDPOINT_API));
    Assert::match('%a%gate.gopay%a%foobar', $gopay->getEndpointUrl('foobar'));
    Assert::match('%a%gate.gopay%a%foobar', $gopay->getEndpointUrl('foobar/'));
    Assert::match('%a%gate.gopay.cz%a%embed.js', $gopay->getEndpoint($gopay::ENDPOINT_JS));
});
