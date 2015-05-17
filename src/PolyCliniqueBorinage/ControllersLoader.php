<?php

namespace PolyCliniqueBorinage;

use Silex\Application;

class ControllersLoader {
  private $app;

  public function __construct(Application $app) {
    $this->app = $app;
  }

  public function instantiateControllers() {
    $this->app['doctor.controller'] = $this->app->share(function () {
      return new Controllers\DoctorController($this->app['doctor.service']);
    });
  }

}
