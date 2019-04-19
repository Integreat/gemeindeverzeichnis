<?php
/**
 * Import data from Wikidata query:
 * SELECT DISTINCT ?itemLabel ?website ?zip
 * WHERE {
 * ?item wdt:P31/wdt:P279* wd:Q262166
 * OPTIONAL { ?item wdt:P856 ?website }
 * OPTIONAL { ?item wdt:P281 ?zip }
 * SERVICE wikibase:label { bd:serviceParam wikibase:language "de" }
 * }
 * ORDER BY ?itemLabel
 */
if (($handle = fopen("data-homepages.csv", "r")) !== FALSE) {
    $row = 0;
    while (($columns = fgetcsv($handle, 1000, ",", '"')) !== FALSE) {
        $row++;
        if($columns[1] == "") {
            continue;
        }
        echo "Searching for ".$columns[0]."\n";
        $key = "";
        $stmt = $conn->prepare("SELECT `key` FROM `municipalities_core` WHERE `address_zip`=?");
        $zip = substr($columns[2],0,5);
        $stmt->bind_param('s',$zip);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 1) {
            $key = $result->fetch_assoc()['key'];
        }
        $stmt->close();
        if(!$key) {
            $stmt = $conn->prepare("SELECT `key` FROM `municipalities_core` WHERE `name`LIKE ?");
            $search = "%".$columns[0]."%";
            $stmt->bind_param('s', $search);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows == 1) {
                $key = $result->fetch_assoc()['key'];
            }
            $stmt->close();
        }
        if($key) {
            $stmt = $conn->prepare("INSERT INTO web_info_crawler (key, website_default) VALUES (?, ?) ON DUPLICATE KEY UPDATE website_default = ?");
            $stmt->bind_param("sss", $key, $columns[1], $columns[1]);
            if($stmt->execute()) {
                echo "Updated $key $columns[0] $columns[1]\n";
            }
            $stmt->close();
        }
    }
}
fclose($handle);