<?php
// Include libraries
require_once 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

$serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/fundacrud-7833b-firebase-adminsdk-w1g9d-7a352863dd.json');
$firebase = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://fundacrud-7833b-default-rtdb.firebaseio.com')
    ->create();

$database = $firebase->getDatabase();

// echo "<pre>";
// print_r($database);
// echo "</pre>";