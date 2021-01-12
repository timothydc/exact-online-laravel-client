# Exact Online Connector (Laravel package)
This package includes routes for Exact authentication and allows for multithread safe access token refreshes.
## Installation
Add the following line to the require section of your composer.json file:
```
"require": {
    "polaris-dc/exact-online-connector": "dev-master",
    ...
},
```

Add the private repository to the repositories section of your composer.json file:
```
"repositories": [
    {
        "type" : "vcs",
        "url" : "git@bitbucket.org:polaris-dc/exact-online-connector.git"
    }
],
```

*Make sure that either you have SSH-key access to our repositories or add an SSH deploykey to this bitbucket repo.*

Then run:
``` bash
$ composer update
```  

## Configuration
You can publish all resources, or you may choose to publish them separately:
```bash
$ php artisan vendor:publish --tag="exact-online-connector"

$ php artisan vendor:publish --tag="exact-online-connector:config"
$ php artisan vendor:publish --tag="exact-online-connector:migrations"
```

Run your migrations to create the `oauth_tokens` table.

```bash
$ php artisan migrate
```

You can configure your connection variables in your .env file or in the published *config/exact-online.php* file.

## Usage
### Routes
There are 3 routes available

- /exact-online/authorize (this starts up the oauth flow)
- /exact-online/oauth (the exact online callback function)
- /exact-online/disconnect (this disconnects a client)

### Dependency Injection
You can get connection via Dependency Injection.
```php
use PolarisDC\Exact\ExactOnlineConnector\Connection;

...

class Classname
{
    public function myFunction(Connection $connection){
        ...
    }
}
```

Then you can use the Picqer library to CRUD Exact Online records.

## Credits

- [Picqer/exact-php-client](https://github.com/picqer/exact-php-client)
- [PendoNL](https://github.com/PendoNL/laravel-exact-online)