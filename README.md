# appointment-api
Micro Service Appointment API

## Dependencies
  silex/silex: The PHP micro-framework based on the Symfony2 Components

  firebase/php-jwt: A simple library to encode and decode JSON Web Tokens (JWT) in PHP.
  ``https://github.com/firebase/php-jwt``

  symfony/console: Symfony Console Component

  psr/log: Common interface for logging libraries

  doctrine/dbal: Database Abstraction Layer

  nesbot/carbon: A simple API extension for DateTime

  nelmio/api-doc-bundle: Generates documentation for your REST API from annotations
  
  apigen/apigen: https://github.com/ApiGen/ApiGen

  phpunit/phpunit: The PHP Unit Testing framework
  
  namshi/jose: JSON Object Signing and Encryption library for PHP.
  https://github.com/namshi/jose


## Install

``
$ composer install
``

Create a file under config/secure.php with the DB settings:

``
<?php

  //  Fake login and password.
  $secure['db.dbname'] = "poly";
  $secure['db.user'] = "poly";
  $secure['db.password'] = "poly";
``



## RESTful URLs

http://www.sitepoint.com/php-authorization-jwt-json-web-tokens/

http://www.sitepoint.com/introduction-silex-symfony-micro-framework/

http://www.vinaysahni.com/best-practices-for-a-pragmatic-restful-api#restful

https://developer.github.com/v3/gists/#list-gists
https://stripe.com/docs/api#create_customer


## RESTful Resources

GET /v1/specialities - Retrieves a list of specialities

GET /v1/specialities/19 - Retrieves a specific speciality

GET /v1/specialities/19/doctors - Retrieves list of doctors for specialities #12

GET /v1/doctors/38 - Retrieves doctors #38

GET /v1/doctors/38/bookings/2015-06-12 Retrieves doctors available slots on #2015-06-12

POST /v1/doctors/38/bookings/2015-06-12 Add a booking on #2015-06-12

## Database

medecins

specialites

authentification