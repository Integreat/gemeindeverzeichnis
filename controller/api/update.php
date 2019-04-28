<?php

use Integreat\Gemeindeverzeichnis\Container;
use Integreat\Gemeindeverzeichnis\DatabaseConnection;
use Integreat\Gemeindeverzeichnis\Config;

$conn = Container::getInstance()->get(DatabaseConnection::class);
$config = Container::getInstance()->get(Config::class);

header("Content-Type: application/json");

if($config['allow_updates'] !== "1") {
    echo json_encode(array("error" => "denied"));
    return;
}

/**
 * Sanitize input
 */
$key = strip_tags($query->key);
$slug = strip_tags($query->slug);
$name = strip_tags($query->name);
$address_street = strip_tags($query->address_street);
$address_zip = strip_tags($query->address_zip);
$address_city = strip_tags($query->address_city);
$opening_hours = strip_tags($query->opening_hours);
$email_default = strip_tags($query->email_default);
$website_default = strip_tags($query->website_default);
$email_poll = strip_tags($query->email_poll);
$website_poll = strip_tags($query->website_poll);

$stmt = $conn->prepare("INSERT INTO `polling_station_web_queue`
(`key`, `slug`, `name`, `address_street`, `address_zip`, `address_city`, `opening_hours`, `email_default`, `website_default`, `email_poll`, `website_poll`) VALUES
( ?   ,  ?    ,  ?    ,  ?              ,  ?           ,  ?            ,  ?             ,  ?             ,  ?               ,  ?          ,  ?            )");
$stmt->bind_param('ssssssssss',
 $key,  $slug , $name , $address_street , $address_zip , $address_city , $opening_hours , $email_default , $website_default , $email_poll , $website_poll);
if($stmt->execute()) {
    $result = array("status" => "success");
} else {
    $result = array("error" => "failed");
}
$stmt->close();

echo json_encode($result);
