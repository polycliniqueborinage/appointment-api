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

    // http://local.drupal8:8888/index_dev.php/api/v1/booking/day/11111111111/2015-05-18
    // http://local.drupal8:8888/api/v1/booking/day/11111111111/2015-05-19
    $controllers->get('/day/{id}/{date}', function(Request $request, $id, $date) use ($app) {
      // return new Response($app['twig']->resolveTemplate(array('doctors/default.html'))->render(array('code' => 200)), 200);
      return new JsonResponse($app['booking.service']->getAvailableSlotByDate($id, $date));
    });

    return $controllers;
  }

}
