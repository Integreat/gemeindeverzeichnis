<?php

$content = file_get_contents("data-homepages.csv");
$content = explode("\n", $content);

foreach($content as $row) {
    $columns = explode(',', $row);
    if($columns[1] == "") {
        continue;
    }
    echo "Searching for ".$columns[0]."\n";
    $key = "";
    $stmt = $conn->prepare("SELECT `key` FROM `municipalities` WHERE `name`=? OR `name`=?");
    $stadt = $columns[0].", Stadt";
    $stmt->bind_param('ss',$columns[0], $stadt);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows == 1) {
        $key = $result->fetch_assoc()['key'];
    }
    $stmt->close();
    if(!$key) {
        $stmt = $conn->prepare("SELECT `key` FROM `municipalities` WHERE `name`LIKE ?");
        $search = "%".$columns[0]."%";
        $stmt->bind_param('s', $search);
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
            echo "Updated $key $columns[0] $columns[1]\n";
        }
        $stmt->close();
    }
}
