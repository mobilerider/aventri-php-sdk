<?php

use Mr\AventriSdk\Sdk;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::create(__DIR__, '.env');
$dotenv->load();

Sdk::setCredentials(getenv("ACCOUNT_ID"), getenv("USER"), getenv("PASS"));

$srv = Sdk::getRegistrationService();

$attendees = $srv->findAttendees();

mr_dd($attendees);
