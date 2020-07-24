<?php

use Mr\AventriSdk\Sdk;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::create(__DIR__, '.env');
$dotenv->load();

Sdk::setCredentials(getenv("ACCOUNT_ID"), getenv("ACCOUNT_KEY"), getenv("EVENT_ID"), ['debug' => true]);

$srv = Sdk::getRegistrationService();

$attendees = $srv->findAttendees();

mr_dd($attendees);
