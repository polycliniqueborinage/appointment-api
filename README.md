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

http://www.sitepoint.com/introduction-silex-symfony-micro-framework/

http://www.vinaysahni.com/best-practices-for-a-pragmatic-restful-api#restful

https://developer.github.com/v3/gists/#list-gists
https://stripe.com/docs/api#create_customer

JSON APIs use snake_case. 

GET /v1/specialities - Retrieves a list of specialities
GET /specialities/12 - Retrieves a specific speciality
POST /specialities - Creates a new speciality
PUT /specialities/12 - Updates speciality #12
PATCH /specialities/12 - Partially updates speciality #12
DELETE /specialities/12 - Deletes speciality #12

GET /specialities/12/doctors - Retrieves list of doctors for specialities #12
GET /specialities/12/doctors/5 - Retrieves doctors #5 for specialities #12


## Database

medecins
specialites