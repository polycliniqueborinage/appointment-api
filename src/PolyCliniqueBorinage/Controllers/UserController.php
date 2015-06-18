<?php

namespace PolyCliniqueBorinage\Controllers;

use Silex\ControllerProviderInterface;
use Silex\Application;
use Silex\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController implements ControllerProviderInterface{

  public function connect(Application $app) {

    $controllers = $app['controllers_factory'];

    $controllers->post('/authenticate', function(Request $request) use ($app) {

      if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);

        return new JsonResponse($data);
      }

    });


    $controllers->get('/authenticate', function(Request $request) use ($app) {

      return new JsonResponse('true');

    });

    return $controllers;
  }

}
