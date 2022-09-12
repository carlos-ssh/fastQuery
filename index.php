<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require './vendor/autoload.php';
require './src/config/db.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    ]
]);

// routes: 
require './src/routes/movies.php';
require './src/routes/ga.php';
require './src/routes/AdminGa.php';

$app->run();
