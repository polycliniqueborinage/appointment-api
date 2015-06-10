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

    // http://local.drupal8:8888/v1/doctor
    $controllers->get('/', function(Request $request) use ($app) {
      return new JsonResponse($app['doctor.service']->getAll());
    });

    // http://local.drupal8:8888/v1/doctor/1
    $controllers->get('/{id}', function(Request $request, $id) use ($app) {
      return new JsonResponse($app['doctor.service']->get($id));
    });

    return $controllers;
  }

}
