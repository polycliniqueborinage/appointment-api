<?php

namespace PolyCliniqueBorinage\Controllers;

use Silex\ControllerProviderInterface;
use Silex\Application;
use Silex\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class CalendarController implements ControllerProviderInterface{

  public function connect(Application $app) {

    $controllers = $app['controllers_factory'];

    // http://local.drupal8:8888/index_dev.php/v1/doctors/20/calendars
    $controllers->get('/', function(Request $request) use ($app) {

      // Get the doctor.
      $doctor = $app['doctor.service']->get($request->get('doctorId'));

      return new JsonResponse($app['booking.service']->getWeeklyCalendar($doctor['inami']));

    });

    return $controllers;
  }

}
