<?php

use Mr\AventriSdk\Sdk;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


Sdk::setCredentials($_ENV['ACCOUNT_ID'], $_ENV['KEY'], $_ENV['EVENT_ID'], ['debug' => true]);

//print(var_dump($token));

$srv = Sdk::getRegistrationService();


$lolo = array(
    'attendeeid' => 48050294
   );

//$attendees = $srv->findAttendees($lolo);
$attendees = $srv->getAttendee(48050294);

print_r(var_dump($attendees));
