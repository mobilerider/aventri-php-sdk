<?php

use Mr\AventriSdk\Sdk;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


Sdk::setCredentials($_ENV['ACCOUNT_ID'], $_ENV['KEY']);

$srv = Sdk::getRegistrationService();

$attendees = $srv->findAttendees();
print_r(var_dump($attendees));
