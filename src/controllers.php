<?php

use Symfony\Component\HttpFoundation\Response;
use PolyCliniqueBorinage\Controllers\DoctorController;
use PolyCliniqueBorinage\Controllers\SpecialityController;
use PolyCliniqueBorinage\Controllers\BookingController;

// Front.
$app->get($app["api.version"] . '/', function () {
  return "PolyClinique API";
});

// Doctors.
$app->mount($app["api.version"] . '/doctor', new DoctorController());

// Specialities.
$app->mount($app["api.version"] . '/speciality', new SpecialityController());

// Bookings.
$app->mount($app["api.version"] . '/booking', new BookingController());

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
