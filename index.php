<?php

use Mr\AventriSdk\Sdk;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


Sdk::setCredentials($_ENV['ACCOUNT_ID'], $_ENV['KEY']);

//print(var_dump($token));

$srv = Sdk::getRegistrationService();
$token = Sdk::getToken();
$options = array(
    'accestoken'=>$token,
    'eventid' => '517370'

);

$attendees = $srv->findAttendees($options);
print_r(var_dump($attendees));
