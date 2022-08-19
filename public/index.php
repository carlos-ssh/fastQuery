<?php


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
require '../src/config/db.php';

$app = new \slim\App;

// routes: 
require '..src/routes/movies.php';

$app->run();
