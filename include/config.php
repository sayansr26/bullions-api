<?php


$host = "localhost"; // change this with your host
$user = "root"; // change this with your username
$password = ""; // change this with your password
$database = "bullions"; // chnage this with your db name

$connect = new mysqli($host, $user, $password, $database);

if (!$connect) {
    die("connection failed :" . mysqli_connect_error());
} else {
    $connect->set_charset('utf8');
}

$GLOBALS['config'] = $connect;

$ENABLE_RTL_MODE = 'false';
