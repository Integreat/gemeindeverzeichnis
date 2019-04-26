<?php

use Integreat\Gemeindeverzeichnis\Container;
use Integreat\Gemeindeverzeichnis\DatabaseConnection;

$conn = Container::getInstance()->get(DatabaseConnection::class);

if(is_numeric($query)) {
    $query = "%{$query}%";
    $sql = "SELECT DISTINCT `m`.`key`, `m`.`name`, `m`.`address_zip`, `m`.`county`, `m`.`state` FROM `municipalities` `m` LEFT JOIN zip_codes z ON `m`.`key` = `z`.`municipality_key` WHERE `z`.`zip` LIKE ?";
}else{
    $query = "%{$query}%";
    $sql = "SELECT `key`, `name`, `address_zip`, `state`, `county` FROM `municipalities` WHERE `name` LIKE ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $query);
$stmt->execute();
$res = $stmt->get_result();

$result = array();
$n = 0;
while($row = $res->fetch_assoc()) {
    $result[$n]['key'] = $row['key'];
    $result[$n]['name'] = $row['name'];
    $result[$n]['zip'] = $row['address_zip'];
    $result[$n]['county'] = $row['county'];
    $result[$n]['state'] = $row['state'];
    $n++;
}

header("Content-Type: application/json");
echo json_encode($result);
