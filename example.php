<?php

use Mr\AventriSdk\Sdk;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::create(__DIR__, '.env');
$dotenv->load();

Sdk::setCredentials(getenv("ACCOUNT_ID"), getenv("ACCOUNT_KEY"), getenv("EVENT_ID"), ['debug' => true]);

$srv = Sdk::getRegistrationService();

if (getenv("ATTENDEE_ID")) {
    $attendee = $srv->getAttendee(getenv("ATTENDEE_ID"));
    mr_dd($attendee);
} else {
    $attendees = $srv->findAttendees(['page' => 2]);
    mr_dd($attendees);
}