<?php
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
    $result[$n]['address']['website'] = $row['website'];
    $result[$n]['address']['email'] = $row['email'];
    $result[$n]['address']['street'] = $row['address_street'];
    $result[$n]['address']['recipient'] = $row['address_recipient'];
    $result[$n]['address']['city'] = $row['address_city'];
    $n++;
}

header("Content-Type: application/json");
echo json_encode($result);
