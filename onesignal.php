<?php 
include_once 'partials/header.php'; 

require __DIR__ . '/vendor/autoload.php';
use Twilio\Rest\Client;

// Your Account SID and Auth Token from twilio.com/console
$account_sid = 'ACe024a6eccd954636f82b60a5447edbd9';
$auth_token = '5a2c3e761b6d8f435100f679c9dbefdb';
// In production, these should be environment variables. E.g.:
// $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]

// A Twilio number you own with SMS capabilities
$twilio_number = "+18599034728";

$client = new Client($account_sid, $auth_token);
$client->messages->create(
    // Where to send a text message (your cell phone?)
    '+8801738631658',
    array(
        'from' => $twilio_number,
        'body' => 'This message from Mofizul'
    )
);

?>
