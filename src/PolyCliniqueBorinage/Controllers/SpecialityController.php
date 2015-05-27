<?php

namespace PolyCliniqueBorinage\Controllers;

use Silex\ControllerProviderInterface;
use Silex\Application;
use Silex\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class SpecialityController implements ControllerProviderInterface{

  public function connect(Application $app) {

    $controllers = $app['controllers_factory'];

    // http://local.drupal8:8888/v1/speciality
    $controllers->get('/', function(Request $request) use ($app) {
      return new JsonResponse($app['speciality.service']->getAll());
    });

    // http://local.drupal8:8888/v1/speciality/1
    $controllers->get('/{id}', function(Request $request, $id) use ($app) {
      return new JsonResponse($app['speciality.service']->get($id));
    });

    // http://local.drupal8:8888/v1/speciality/1/doctor
    $controllers->get('/{id}/doctors', function(Request $request, $id) use ($app) {
      return new JsonResponse($app['speciality.service']->getDoctors($id));
    });

    return $controllers;
  }

}
