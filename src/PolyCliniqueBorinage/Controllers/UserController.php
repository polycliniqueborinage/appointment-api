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


    // Login.
    $controllers->post('/login', function(Request $request) use ($app) {

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


    // Register.
    $controllers->post('/register', function(Request $request) use ($app) {

      $data = json_decode($request->getContent(), true);
      $request->request->replace(is_array($data) ? $data : array());

      // if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {

      $user = array();
      $user['firstname'] = $request->request->get('firstname');
      $user['lastname'] = $request->request->get('lastname');
      $user['email'] = $request->request->get('email');
      $user['birthdate'] = $request->request->get('birthdate');

      // Validation.

      $user_id = $app['user.service']->create($user);

      $user = $app['user.service']->get($user_id);

      if ($user) {
        // Create token.
        $user['token'] = $app['authentification.service']->createToken($user);
        return new JsonResponse($user);
      }
      else {
        return new JsonResponse('Wrong username and password', 400);
      }

    });

    return $controllers;
  }

}

/*

$app->before(function (Request $request) {
  if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
    $data = json_decode($request->getContent(), true);
    $request->request->replace(is_array($data) ? $data : array());
  }
});*/