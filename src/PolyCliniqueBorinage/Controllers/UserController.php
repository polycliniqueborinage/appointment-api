<?php

namespace PolyCliniqueBorinage\Controllers;

use Silex\ControllerProviderInterface;
use Silex\Application;
use Silex\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController implements ControllerProviderInterface{

  public function connect(Application $app) {

    $controllers = $app['controllers_factory'];

    $controllers->post('/authenticate', function(Request $request) use ($app) {

      $data = json_decode($request->getContent(), true);

      if(isset($data['username']) && isset($data['password'])) {

        // Encode password.

        $user = $app['user.service']->get($data['username'], $data['password']);
        if ($user) {
          // Create token.
          $token = $app['authentification.service']->createToken($user);
          return new JsonResponse($token);
        }
        else {
          return new JsonResponse('Wrong username and password', 400);
        }
      }
      else {
        return new JsonResponse('You need to provide a username and password', 400);
      }

    });

    //$controllers->get('/authenticate', function(Request $request) use ($app) {
    //  return new JsonResponse($app['user.service']->get('d', 'd'));
    //});

    return $controllers;
  }

}
