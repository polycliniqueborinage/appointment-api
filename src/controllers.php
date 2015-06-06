<?php

use Symfony\Component\HttpFoundation\Response;
use PolyCliniqueBorinage\Controllers\DoctorController;
use PolyCliniqueBorinage\Controllers\SpecialityController;
use PolyCliniqueBorinage\Controllers\BookingController;
use PolyCliniqueBorinage\Controllers\CalendarController;

// Front.
$app->get($app["api.version"] . '/', function () {
  return "PolyClinique API";
});

// Specialities.
$app->mount($app["api.version"] . '/specialities', new SpecialityController());

// Doctors.
$app->mount($app["api.version"] . '/doctors', new DoctorController());

// Bookings.
$app->mount($app["api.version"] . '/doctors/{doctorId}/bookings', new BookingController());

// Calendars.
$app->mount($app["api.version"] . '/doctors/{doctorId}/calendars', new CalendarController());

// Holidays.
// $app->mount($app["api.version"] . '/doctors/{doctorId}/holidays', new HolidayController());

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
