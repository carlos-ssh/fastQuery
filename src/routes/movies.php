<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app = new \Slim\App;

$app->get('/api/movies/test-one', function(Request $request, Response $response){
  $sql = '
    SELECT a.id AS "ID", CONCAT(a.first_name, " ", a.last_name) AS "Actor Name", a.rating, m.title AS "Title Movie"
      FROM actor_movie AS am
      JOIN actors AS a
        ON a.id = am.actor_id
      JOIN movies AS m
        ON m.id = am.movie_id
     WHERE m.id = 1 AND a.rating > 6
  ';
  try{
    $db = new db();
    $db = $db->connectBD();
    $resultado = $db->query($sql);
    $respuesta = $resultado->fetchAll(PDO::FETCH_OBJ);
    echo json_encode($respuesta);
  }catch(PDOException $e){
    echo '"error" : {"con la descripcion: " : '.$e->getMessage().'}';
  } 
});

$app->get('/api/movies/test-two', function(Request $request, Response $response){
  $sql = '
      SELECT movies.title, first_name, last_name
      FROM actor_movie
      JOIN actors
        ON actors.id = actor_movie.id
      JOIN movies
        ON movies.id = actor_movie.id
      WHERE actors.favorite_movie_id <> 5
  ';
  try{
    $db = new db();
    $db = $db->connectBD();
    $resultado = $db->query($sql);
    $respuesta = $resultado->fetchAll(PDO::FETCH_OBJ);
    echo json_encode($respuesta);
  }catch(PDOException $e){
    echo '"error" : {"con la descripcion: " : '.$e->getMessage().'}';
  } 
});