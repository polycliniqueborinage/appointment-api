<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use PolyCliniqueBorinage\ServicesLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Need to set up timezone.
date_default_timezone_set('Europe/Brussels');

$app = new Application();

// Register Services.
$app->register(new UrlGeneratorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider());

// Config DB.
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
  'dbs.options' => array (
    'mysql_read' => array(
      'driver'    => 'pdo_mysql',
      'host'      => 'localhost',
      'dbname'    => $secure['db.dbname'],
      'user'      => $secure['db.user'],
      'password'  => $secure['db.password'],
      'charset'   => 'utf8',
    )
  ),
));

// Config twig.
$app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
  return $twig;
}));

// Load services.
$servicesLoader = new ServicesLoader($app);
$servicesLoader->bindServicesIntoContainer();

// Cors.
/*$eventDispatcher = $app['dispatcher'];
$corsDefaults = array(
  'allow_credentials' => false,
  'allow_origin' => array('*'),
  'allow_headers' => array(''),
  'allow_methods' => array('GET', 'POST', 'DELETE'),
  'expose_headers' => array(),
  'max_age' => 0
);
$paths = array('^/' => array());
$corsEventSubscriber = new PolyCliniqueBorinage\EventListener\CorsEventSubscriber($eventDispatcher, $paths, $corsDefaults);
$eventDispatcher->addSubscriber($corsEventSubscriber);*/

$app->after(function (Request $request, Response $response) {
  $response->headers->set('Access-Control-Allow-Origin', '*');
  // $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
  $response->headers->set('Access-Control-Allow-Methods', array('GET', 'POST', 'DELETE'));
});

return $app;
