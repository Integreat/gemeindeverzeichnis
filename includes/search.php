<?php
$query = "%{$query}%";


if(is_numeric($query)) {
    $sql = "SELECT `m`.`key`, `m`.`name`, `z`.`zip`, `m`.`county`, `m`.`state` FROM `municipalities` `m` LEFT JOIN zip_codes z ON `m`.`key` = `z`.`municipality_key` WHERE `z`.`zip` LIKE ? AND `m`.`valid`=1";
}else{
    $sql = "SELECT `key`, `name`, `address_zip`, `state`, `county` FROM `municipalities` WHERE `name` LIKE ? AND valid=1";
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
?>