<?php

use Symfony\Component\HttpFoundation\Response;
use PolyCliniqueBorinage\Controllers\DoctorController;
use PolyCliniqueBorinage\Controllers\SpecialityController;

// Front.
$app->get('/', function () {
  return "Welcome To ReSTful API";
});

// Doctors.
$app->mount('/doctor', new DoctorController());

// Specialities.
$app->mount('/speciality', new SpecialityController());

// Catch errors.
$app->error(function (\Exception $e, $code) use ($app) {
  if ($app['debug']) {
    return;
  }

  // 404.html, or 40x.html, or 4xx.html, or error.html.
  $templates = array(
    'errors/'.$code.'.html',
    'errors/'.substr($code, 0, 2).'x.html',
    'errors/'.substr($code, 0, 1).'xx.html',
    'errors/default.html',
  );

  return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
