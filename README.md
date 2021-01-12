# Exact Online Connector (Laravel package)
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

##Configuration
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

##Credits

- [Gunharth][link-gunharth] (https://github.com/gunharth/laravel-lightspeed-api)