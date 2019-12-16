# A simple REST-API with the micro framework Slim-PHP

## what is where

### JWT Settings and Database

__JWT Settings:__
> data/config.php 

__Database:__
> data/database.sql

You can put the folder __data/__ outside www-root. Please adjust the references on index.php.
For this, php should have read access outside of the www root


### Controllers and Middleware

see the folder __controllers/__
there are the endpoints for login, register and todos

you can easily protect your future routes with middleware "$checkAuthorization"
the same applies to cors or your own response header
```php
$app->get('/api/wahtever', function (Request $request, Response $response, array $args) {
  ...
})->add($enableCors)->add($checkAuthorization)->add($returnJsonHeader);
```

...
