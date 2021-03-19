<?php
session_start();
require_once "firebasedb.php";

if (isset($_POST['save_push_data'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phoneno = $_POST['phoneno'];

    $data = [
        'username'  => $username,
        'email'     => $email,
        'phoneno'   => $phoneno
    ];

    $ref = "contact/"; // I can use any table (contact/) name here. This name will automatically generate in Firebase and data will be stored under this table. That means I no need to create this table from Firebase console/dashboard area.

    $postData = $database->getReference($ref)->push($data);

    if ($postData) {
        $_SESSION['status'] = "Data inserted";
        header('location: firebaseinsert.php');
    } else {
        $_SESSION['status'] = "Data NOT inserted";
        header('location: firebaseinsert.php');
    }
    
}
