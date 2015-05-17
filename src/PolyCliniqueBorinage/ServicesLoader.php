<?php

namespace PolyCliniqueBorinage;

use Silex\Application;

class ServicesLoader {

  protected $app;

  public function __construct(Application $app) {
    $this->app = $app;
  }

  public function bindServicesIntoContainer() {
    $this->app['doctor.service'] = $this->app->share(function () {
      return new Services\DoctorService($this->app["db"]);
    });
    $this->app['speciality.service'] = $this->app->share(function () {
      return new Services\SpecialityService($this->app["db"]);
    });
  }
}
