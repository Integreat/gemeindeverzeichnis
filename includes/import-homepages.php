<?php

$content = file_get_contents("data-homepages.csv");
$content = explode("\n", $content);

foreach($content as $row) {
    $columns = explode(',', $row);
    if($columns[1] == "") {
        continue;
    }
    $key = "";
    $stmt = $conn->prepare("SELECT `key` FROM `municipalities` WHERE `name`=?");
    $stmt->bind_param('s', $row[0]);
    $stmt->execute();
    $result = $stmt->get_result();
    if($stmt->num_rows == 1) {
        $key = $result->fetch_assoc()['key'];
    }
    $stmt->close();
    if(!$key) {
        $stmt = $conn->prepare("SELECT `key` FROM `municipalities` WHERE `name`LIKE ?");
        $serach = "%".$columns[0]."%";
        $stmt->bind_param('s', );
        $stmt->execute();
        $result = $stmt->get_result();
        if($stmt->num_rows == 1) {
            $key = $result->fetch_assoc()['key'];
        }
        $stmt->close();
    }
    if($key) {
        $stmt = $conn->prepare("UPDATE `municipalities` SET `website`=? WHERE `key` = ?");
        $stmt->bind_param("ss", $columns[1], $key);
        if($stmt->execute()) {
            echo "Updated $key\n";
        }
    }
}