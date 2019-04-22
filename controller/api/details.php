<?php

use Integreat\Gemeindeverzeichnis\Container;
use Integreat\Gemeindeverzeichnis\DatabaseConnection;

$conn = Container::getInstance()->get(DatabaseConnection::class);

$stmt = $conn->prepare("SELECT * FROM `municipalities` WHERE `key`=? LIMIT 1");
$stmt->bind_param('s', $query);
$stmt->execute();
$res_mun = $stmt->get_result();
$stmt->close();

$stmt = $conn->prepare("SELECT `zip` FROM `zip_codes` WHERE `municipality_key`=?");
$stmt->bind_param('s', $query);
$stmt->execute();
$res_zip = $stmt->get_result();
$stmt->close();

$zip_codes = array();
while($row = $res_zip->fetch_assoc()) {
    $zip_codes[] = $row['zip'];
}

$stmt = $conn->prepare("SELECT * FROM `polling_stations` WHERE `key`=?");
$stmt->bind_param('s', $query);
$stmt->execute();
$res_polls = $stmt->get_result();
$stmt->close();

$polling_stations = array();
$n = 0;
while($row = $res_polls->fetch_assoc()) {
    $polling_stations[$n]['slug'] = $row['slug'];
    $polling_stations[$n]['name'] = $row['name'];
    $polling_stations[$n]['street'] = $row['address_street'];
    $polling_stations[$n]['zip'] = $row['address_zip'];
    $polling_stations[$n]['city'] = $row['address_city'];
    $polling_stations[$n]['opening_hours'] = $row['opening_hours'];
    $n++;
}

$result = array();
$n = 0;
while($row = $res_mun->fetch_assoc()) {
    $result[$n]['key'] = $row['key'];
    $result[$n]['name'] = $row['name'];
    $result[$n]['zip_codes'] = $zip_codes;
    $result[$n]['state'] = $row['state'];
    $result[$n]['district'] = $row['district'];
    $result[$n]['county'] = $row['county'];
    $result[$n]['type'] = $row['type'];
    $result[$n]['population'] = $row['population'];
    $result[$n]['population_male'] = $row['population_male'];
    $result[$n]['population_female'] = $row['population_female'];
    $result[$n]['longitude'] = $row['longitude'];
    $result[$n]['latitude'] = $row['latitude'];
    $result[$n]['area'] = $row['area'];
    $result[$n]['address']['zip'] = $row['address_zip'];
    $result[$n]['address']['website_default'] = $row['website_default'];
    $result[$n]['address']['email_default'] = $row['email_default'];
    $result[$n]['address']['website_poll'] = $row['website_poll'];
    $result[$n]['address']['email_poll'] = $row['email_poll'];
    $result[$n]['address']['street'] = $row['address_street'];
    $result[$n]['address']['recipient'] = $row['address_recipient'];
    $result[$n]['address']['city'] = $row['address_city'];
    $result[$n]['polling_stations'] = $polling_stations;
    $n++;
}

header("Content-Type: application/json");
echo json_encode($result);
