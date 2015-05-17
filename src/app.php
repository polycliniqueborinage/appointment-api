<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use PolyCliniqueBorinage\ServicesLoader;
use PolyCliniqueBorinage\ControllersLoader;

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
      'dbname'    => 'poly',
      'user'      => 'poly',
      'password'  => 'poly',
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

// Load controllers.
$controllersloader = new ControllersLoader($app);
$controllersloader->instantiateControllers();

return $app;
