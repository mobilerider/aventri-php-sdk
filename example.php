<?php

use Mr\AventriSdk\Sdk;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

Sdk::setCredentials($_ENV["ACCOUNT_ID"], $_ENV["ACCOUNT_KEY"], $_ENV["EVENT_ID"], ['debug' => true]);
$srv = Sdk::getRegistrationService();

if (isset($_ENV["ATTENDEE_ID"])) {
    $attendee = $srv->getAttendee($_ENV["ATTENDEE_ID"]);
    mr_dd($attendee);
} else {
    $attendees = $srv->findAttendees();
    mr_dd($attendees);
}
