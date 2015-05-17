<?php

namespace PolyCliniqueBorinage\Controllers;

use Silex\ControllerProviderInterface;
use Silex\Application;
use Silex\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class DoctorController implements ControllerProviderInterface{

  public function connect(Application $app) {

    $controllers = $app['controllers_factory'];

    $controllers->get('/', function(Request $request) use ($app) {
      return new JsonResponse($app['doctor.service']->getAll());
    });

    $controllers->get('/{id}', function(Request $request, $id) use ($app) {
      return new JsonResponse($app['doctor.service']->get($id));
    });

    return $controllers;
  }

}
