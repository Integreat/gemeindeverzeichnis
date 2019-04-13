<?php
$content = file_get_contents('php://input');
$query = json_decode($content)[0];

if(is_numeric($query)) {
    $stmt = $conn->prepare("SELECT zip FROM zip_codes WHERE zip LIKE ? LIMIT 8");
    $stmt->bind_param('s', "%{$query}%");
    $field = "zip";
}else{
    $stmt = $conn->prepare("SELECT name FROM municipalities WHERE name LIKE ? LIMIT 8");
    $stmt->bind_param('s', "%{$query}%");
    $field = "name";
}
$stmt->execute();
$res = $stmt->get_result();

$result = array();
while($row = $res->fetch_assoc()) {
    $result[] = $row[$field];
}

header("Content-Type: application/json");
echo json_encode($result);
?>