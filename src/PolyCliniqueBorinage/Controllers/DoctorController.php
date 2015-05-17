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

    $controllers->match('/list', function(Request $request) use ($app) {
      $users = $app['doctor.service']->getAll();

      switch ($request->get('format')) {
        case 'html':
          // @todo: injest value into template.
          $templates = array(
            'doctors/default.html',
          );
          return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => 200)), 200);
          break;
        default:
          return new JsonResponse($users);
      }
    });

    return $controllers;
  }

}
