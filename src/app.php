<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;

// Need to set up timezone.
date_default_timezone_set('Europe/Brussels');

$app = new Application();

// Register Services.
$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider());

// Config services.
$app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
  return $twig;
}));

return $app;
