![](https://banners.beyondco.de/Laravel-Wazirx-API.png?theme=light&packageManager=composer+require&packageName=techtailor%2Flaravel-wazirx-api&pattern=architect&style=style_2&description=A+laravel+wrapper+for+the+WazirX+API.&md=1&showWatermark=0&fontSize=100px&images=server)

[![GitHub release](https://img.shields.io/github/release/techtailor/laravel-wazirx-api.svg?style=for-the-badge&&colorB=7E57C2)](https://packagist.org/packages/techtailor/laravel-wazirx-api)
[![GitHub issues](https://img.shields.io/github/issues/TechTailor/Laravel-Wazirx-Api.svg?style=for-the-badge)](https://github.com/TechTailor/Laravel-Wazirx-Api/issues)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=for-the-badge&&colorB=F27E40)](license.md)
[![Total Downloads](https://img.shields.io/packagist/dt/techtailor/laravel-wazirx-api.svg?style=for-the-badge)](https://packagist.org/packages/techtailor/laravel-wazirx-api)

This package provides a Laravel Wrapper for the [WazirX API](https://docs.wazirx.com/) and allows you to easily communicate with it.

 ---
#### Important Note
This package is in early development stage. It is not advisable to use it in a production app until **`v1.0`** is released. Feel free to open a PR to contribute to this project and help me reach a production ready build.

---

### Installation

You can install the package via composer:

```bash
composer require techtailor/laravel-wazirx-api
```

You can publish the config file with:
```bash
php artisan vendor:publish --tag="wazirx-api-config"
```

Open your `.env` file and add the following (replace ``YOUR_API_KEY`` and ``YOUR_SECRET`` with the API Key & Secret you received from [Wazirx](https://wazirx.com/settings/keys)) -
```php
WAZIRX_KEY=YOUR_API_KEY
WAZIRX_SECRET=YOUR_SECRET
```
Or

Open the published config file available at `config/wazirx.php` and add your API and Secret Keys:

```php
return [
    'auth' => [
        'key'        => env('WAZIRX_KEY', 'YOUR_API_KEY'),
        'secret'     => env('WAZIRX_SECRET', 'YOUR_SECRET')
    ],
];
```

### Usage

Using this package is very simple. Just initialize the Api and call one of the available methods: 
```php
use TechTailor\Wazirx\WazirxAPI;

$wazirx = new WazirxApi();

$time = $wazirx->getTime();
```

You can also set an API & Secret for a user by passing it after initalization (useful when you need to isolate api keys for individual users):

```php
$wazirx = new WazirxApi();

$wazirx->setApi($apiKey, $secretKey);

$accountInfo = $wazirx->getAccountInfo();
```

### Available Methods

Available Public Methods (Security Type : `NONE`) **[API Keys Not Required]**
```
- getTime()
- getServerStatus()
- getExchangeInfo()
- getTickers()
- getTicker($symbol)
```
Available Private Methods (Security Type : `USER_DATA`) **[API Keys Required]**
```
- getAccountInfo()
- getFunds()
- getAllOrders($symbol)
- getOpenOrders()
- getOrderStatus($orderId)
```

### TODO

List of features or additional functionality we are working on (in no particular order) -

```bash
- Improve exception handling.
- Add rate limiting to API Calls.
- Add response for API ban/blacklisting response.
- Improve ReadMe.
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

### Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

### Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

### Credits

- [Moinuddin S. Khaja](https://github.com/TechTailor)
- [All Contributors](../../contributors)

### License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
