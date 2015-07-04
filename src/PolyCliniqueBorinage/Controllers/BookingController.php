<?php

namespace PolyCliniqueBorinage\Controllers;

use Silex\ControllerProviderInterface;
use Silex\Application;
use Silex\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class BookingController implements ControllerProviderInterface{

  public function connect(Application $app) {

    $controllers = $app['controllers_factory'];

    // http://local.drupal8:8888/index_dev.php/v1/doctors/2/bookings/2015-05-18?interval=day
    $controllers->get('/{date}', function(Request $request, $date) use ($app) {

      // Get the doctor.
      $doctor = $app['doctor.service']->get($request->get('doctorId'));

      switch ($request->get('interval')) {
        case 'week':
          return new JsonResponse($app['booking.service']->getAvailableSlotsByWeek($doctor['inami'], $date));
          break;
        case 'month':
          return new JsonResponse($app['booking.service']->getAvailableSlotsByMonth($doctor['inami'], $date));
          break;
        default:
          return new JsonResponse($app['booking.service']->getAvailableSlotsByDate($doctor['inami'], $date));
      }

    });

    $controllers->post('/{date}', function(Request $request, $date) use ($app) {

      if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
      }

      // Get the doctor.
      $doctor = $app['doctor.service']->get($request->get('doctorId'));

      // Get start date
      $start = $request->request->get('start');

      // Get end date
      $end = $request->request->get('end');

      // Save the slot.
      $response = $app['booking.service']->save($doctor['inami'], $start, $end);

      if ($response) {
        return new JsonResponse($response);
      }
      else {
        return new JsonResponse(FALSE, 400);
      }

    });

    return $controllers;
  }

}
