<?php
$content = file_get_contents('php://input');
$query = json_decode($content)[0];
$query = "%{$query}%";


if(is_numeric($query)) {
    $sql = "SELECT `m`.`key`, `m`.`name`, `z`.`zip`, `m`.`state` FROM `municipalities` `m` LEFT JOIN zip_codes z ON `m`.`key` = `z`.`municipality_key` WHERE `z`.`zip` LIKE ? AND `m`.`valid`=1";
}else{
    $sql = "SELECT `key`, `name`, `ps_zip`, `state` FROM `municipalities` WHERE `name` LIKE ? AND valid=1";
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
    $result[$n]['zip'] = $row['zip'];
    $result[$n]['state'] = $row['state'];
}

header("Content-Type: application/json");
echo json_encode($result);
?>