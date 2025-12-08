![](https://heatbadger.now.sh/github/readme/contributte/gopay-simple/?deprecated=1)

<p align=center>
    <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
    <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
    <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
    Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

## Disclaimer

| :warning: | This project is no longer being maintained. Please use [contributte/gopay-inline](https://github.com/contributte/gopay-inline).
|---|---|

| Composer | [`markette/gopay-simple`](https://packagist.org/packages/markette/gopay-simple) |
|---| --- |
| Version | ![](https://badgen.net/packagist/v/markette/gopay-simple) |
| PHP | ![](https://badgen.net/packagist/php/markette/gopay-simple) |
| License | ![](https://badgen.net/github/license/contributte/gopay-simple) |

## Documentation

### Goal

One class rule-them-all, best way is `$gopay->call()`.

### Install

```bash
composer require markette/gopay-simple
```

### Requirements

You need **GoID**, **ClientID** and **ClientSecret**.

* Webpage ([https://www.gopaygate.com](https://www.gopaygate.com))
* Offical resources in EN ([https://doc.gopay.com/en/](https://doc.gopay.com/en/))
* Offical resources in CZ ([https://doc.gopay.com/cs/](https://doc.gopay.com/cs/))

### Usage

This super simple class provides a few methods, 2 public and 4 protected, for easy extending / prototyping.

#### Public

- `call(string $method, string $endpoint, array $args = [])` : `stdClass`
- `setMode(int $gopay::DEV/PROD)` : `void`
- `$useragent` : `PHP+Markette/GopaySimple/{VERSION}`
- `$options` : `[]` (cURL options)

#### Protected

- `authenticate(array $args)` : `stdClass` (token)
- `makeRequest(string $method, string $endpoint, array $args = [])` : `string`
- `getEndpoint(string $type)` : `string`
- `getEndpointUrl(string $uri)` : `string`

#### Common part

```php
use Markette\GopaySimple\GopaySimple;

$gopay = new GopaySimple($clientId, $clientSecret);

# For testing purpose
$gopay->setMode($gopay::DEV);
```

#### Authorization (Oauth)

Auth process is very simple and automatic. So, you do not have to do anything.

If you really need override authorization, you have to extend `GopaySimple` and call `authenticate($args)` directly.

#### Payments

##### `POST+payments/payment`

```php
$response = $gopay->call('POST', 'payments/payment', [
	'payer' => [
		'default_payment_instrument' => 'BANK_ACCOUNT',
		'allowed_payment_instruments' => ['BANK_ACCOUNT'],
		'default_swift' => 'FIOBCZPP',
		'allowed_swifts' => ['FIOBCZPP', 'BREXCZPP'],
		'contact' => [
			'first_name' => 'Zbynek',
			'last_name' => 'Zak',
			'email' => 'zbynek.zak@gopay.cz',
			'phone_number' => '+420777456123',
			'city' => 'C.Budejovice',
			'street' => 'Plana 67',
			'postal_code' => '373 01',
			'country_code' => 'CZE',
		],
	],
	'target': ['type' => 'ACCOUNT', 'goid' => '_YOUR_GO_ID_',
	'amount' => 150,
	'currency' => 'CZK',
	'order_number' => '001',
	'order_description' => 'pojisteni01',
	'items' => [
		['name' => 'item01', 'amount' => 50],
		['name' => 'item02', 'amount' => 100],
	],
	'additional_params' => [
		array('name' => 'invoicenumber', 'value' => '20160001')
	],
	'return_url' => 'http://www.your-url.tld/return',
	'notify_url' => 'http://www.your-url.tld/notify',
	'lang' => 'cs',
]);
```

##### `GET+payments/payment/{id}`

```php
$response = $gopay->call('GET', 'payments/payment/{id}');
```

#### Best practice

You should inject `GopaySimple` into your service layer. And configure `$args` before creating payment for **target**.

Example of [GopayService](https://github.com/Markette/GopaySimple/blob/master/examples/GopayService.php).

### Testing

1. Start build-in server at `tests/buildin/run.sh`
2. Run tester at `tests/tester`


## Development

This package was maintained by these authors.

<a href="https://github.com/f3l1x">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners.html) **contributte** development team.
Also thank you for using this package.
