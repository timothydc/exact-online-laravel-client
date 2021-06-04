# Exact Online Base Client for Laravel

This package includes routes for Exact authentication and allows for multithread safe access token refreshes.

## Installation

Add these lines to your `composer.json`.

```bash
"repositories": [
    {
        "type" : "composer",
        "url" : "https://packages.polaris-dc.app"
    }
]
```

Install the package via composer
```bash
composer require polaris-dc/exact-online-laravel-client
```
Add the private repository to the repositories section of your composer.json file:

## Configuration

You can publish all resources, or you may choose to publish them separately:

```bash
php artisan vendor:publish --tag="exact-online-client"

php artisan vendor:publish --tag="exact-online-client:config"
php artisan vendor:publish --tag="exact-online-client:migrations"
```

Run your migrations to create the `oauth_tokens` table.

```bash
php artisan migrate
```

You can configure your connection variables in your `.env` file or in the published `config/exact-online.php` file.

## Authentication

Initiate the authentication. Make sure you configured `/exact-online/oauth/complete` as your "redirect URL" in the Exact app center. 
```
https://foo.bar/exact-online/oauth/start
```

Check if the API connection was successful
```
https://foo.bar/exact-online/oauth/test
```

Disconnect the EOL connection
```
https://foo.bar/exact-online/oauth/disconnect
```


Then you can use the Picqer library to CRUD Exact Online records.

```php
use Picqer\Financials\Exact\Item;
use PolarisDC\ExactOnline\BaseClient\ExactOnlineClient;

$exactOnlineClient = resolve(ExactOnlineClient::class);

// create a new product in EOL
$item = new Item($exactOnlineClient->getConnection());
$item->Code = 'foo-bar';
$item->CostPriceStandard = 1.23;
$item->Description = 'lorem ipsum';
$item->IsSalesItem = true;
$item->SalesVatCode = 'VH';
$item->save();
```