<?php

// Configure your app for the production environment.
$app['twig.path'] = array(__DIR__.'/../templates');
$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');
$app['api.version'] = "v1";
$app['api.endpoint'] = "/api";
//  Fake login and password.
$app['db.dbname'] = "poly";
$app['db.user'] = "poly";
$app['db.password'] = "poly";
