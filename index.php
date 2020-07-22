<?php

use Mr\AventriSdk\Sdk;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


Sdk::setCredentials('7574', '41cb853962051d9dcc38607153b7cb5fbfc3f021');

//$srv = Sdk::getRegistrationService();

//$attendees = $srv->findAttendees();
//print_r(var_dump($attendees));
