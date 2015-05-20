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

    // http://local.drupal8:8888/index_dev.php/api/v1/booking/11111111111/day/2015-05-18
    $controllers->get('/{id}/day/{date}', function(Request $request, $id, $date) use ($app) {
      // return new Response($app['twig']->resolveTemplate(array('doctors/default.html'))->render(array('code' => 200)), 200);
      return new JsonResponse($app['booking.service']->getAvailableSlotsByDate($id, $date));
    });

    // http://local.drupal8:8888/index_dev.php/api/v1/booking/11111111111/week/2015-05-18
    $controllers->get('/{id}/week/{date}', function(Request $request, $id, $date) use ($app) {
      // return new Response($app['twig']->resolveTemplate(array('doctors/default.html'))->render(array('code' => 200)), 200);
      return new JsonResponse($app['booking.service']->getAvailableSlotsByWeek($id, $date));
    });

    // http://local.drupal8:8888/index_dev.php/api/v1/booking/11111111111/month/2015-05-18
    $controllers->get('/{id}/month/{date}', function(Request $request, $id, $date) use ($app) {
      // return new Response($app['twig']->resolveTemplate(array('doctors/default.html'))->render(array('code' => 200)), 200);
      return new JsonResponse($app['booking.service']->getAvailableSlotsByMonth($id, $date));
    });

    return $controllers;
  }

}
