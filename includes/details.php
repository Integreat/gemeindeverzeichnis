<?php
$content = file_get_contents('php://input');
$query = json_decode($content)[0];

$stmt = $conn->prepare("SELECT * FROM `municipalities` WHERE `key`=? AND valid=1 ORDER BY `timestamp` DESC LIMIT 1");
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

$result = array();
$n = 0;
while($row = $res_mun->fetch_assoc()) {
    $result[$n]['key'] = $row['key'];
    $result[$n]['name'] = $row['name'];
    $result[$n]['zip'] = $zip_codes;
    $result[$n]['state'] = $row['state'];
    $result[$n]['polling_station']['zip'] = $row['ps_zip'];
    $result[$n]['polling_station']['website'] = $row['website'];
    $result[$n]['polling_station']['email'] = $row['email'];
    $result[$n]['polling_station']['street'] = $row['ps_zip'];
    $result[$n]['polling_station']['name'] = $row['ps_name'];
    $result[$n]['polling_station']['city'] = $row['ps_city'];
    $n++;
}

header("Content-Type: application/json");
echo json_encode($result);
?>