<?php
session_start();
unset($_SESSION['email']);
unset($_SESSION['loggedInUserID']);
unset($_SESSION['logged_in']);
unset($_SESSION['message']);
header('location: login.php');