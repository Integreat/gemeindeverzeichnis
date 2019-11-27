<?php

use Integreat\Gemeindeverzeichnis\Container;
use Integreat\Gemeindeverzeichnis\DatabaseConnection;

$conn = Container::getInstance()->get(DatabaseConnection::class);

if( is_numeric($query) ) {
  $sql = "SELECT `key`, `name`, `address_zip`, `county`, `state` FROM `municipalities` WHERE `key`=? and type_category=40";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('i', $query);
} else {
  $query = "%{$query}%";
  $sql = "SELECT `key`, `name`, `address_zip`, `county`, `state` FROM `municipalities` WHERE `name` LIKE ? AND type_category=40";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('s', $query);
}
$stmt->execute();
$res = $stmt->get_result();

$result = array();
$n = 0;
while($row = $res->fetch_assoc()) {
    $result[$n]['key'] = $row['key'];
    $result[$n]['name'] = $row['name'];
    $result[$n]['state'] = $row['state'];

    $sql = "SELECT `key`, `name`, `address_zip`, `county`, `state` FROM `municipalities` WHERE `parent_key`=? AND type_category=60";

    $stmt = $conn->prepare($sql);
    $filter_key = $row['key'];
    $stmt->bind_param('s', $filter_key);
    $stmt->execute();
    $res_children = $stmt->get_result();
    $i = 0;
    while($row_children = $res_children->fetch_assoc()) {
        $result[$n]['children'][$i]['key'] = $row_children['key'];
        $result[$n]['children'][$i]['name'] = $row_children['name'];
        $i++;
    }
    $n++;
}

header("Content-Type: application/json");
echo json_encode($result);
