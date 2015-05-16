<?php
require_once '../vendor/autoload.php';

// Init Silex app.
$app = new Silex\Application();

// define route for /countries
$app->get('/countries', function () {
  return "countries list";
});

// define route for /countries/{id}
$app->get('/countries/{id}', function ($id) {
  return "country's cities list for id: $id";
// id must be digital
})->assert('id', '\d+');

// default route
$app->get('/', function () {
  return "List of avaiable methods:
  - /countries - returns list of existing countries;
  - /countries/{id} - returns list of country's cities by id;";
});

$app->run();
