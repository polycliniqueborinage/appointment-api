<?php

namespace PolyCliniqueBorinage\Controller;

use Silex\ControllerProviderInterface;
use Silex\Application;
use Silex\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

class DoctorController implements ControllerProviderInterface{

  public function connect(Application $app) {

    $controllers = $app['controllers_factory'];

    $controllers->match('/', function(Request $request) use ($app) {
      return 'DoctorController';
    });

    return $controllers;
  }

}
