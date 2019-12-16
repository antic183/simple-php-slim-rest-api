<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('UTC');

$config = [
    'settings' => [
        'displayErrorDetails' => false
    ],
];

// own 404 message
$config['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('<h1>Page not found</h1>');
    };
};


$app = new \Slim\App($config);

$container = $app->getContainer();
$container['dbConnection'] = function ($container) {
  $db = new PDO('sqlite:./data/database.sqlite');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
  return $db;
};

require_once 'data/config.php';
require_once 'controllers/middleware.php';
require_once 'controllers/auth.php';
require_once 'controllers/todo.php';
require_once 'controllers/reset-password.php';

$app->run();