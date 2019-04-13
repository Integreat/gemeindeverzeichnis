<?php
$cfg = parse_ini_file("config.ini");
require_once("includes/database.php");

$content = explode("\n", file_get_contents("data-base.csv"));

/**
 *  1. Satzkennzeichen
 *  2. Textkennzeichen
 *  3. Regionalschlüssel - Land
 *  4. Regionalschlüssel - Regierungsbezirk
 *  5. Regionalschlüssel - Kreis
 *  6. Regionalschlüssel - Verwaltungsbezirk
 *  7. Regionalschlüssel - Gemeinde
 *  8. Gemeindename
 *  9. Fläche
 * 10. Bevölkerung - insgemsamt
 * 11. Bevölkerung - männlich
 * 12. Bevölkerung - weiblich
 * 13. Bevölkerung - pro km²
 * 14. PLZ
 * ...
 */

$current_state = '';
$current_county = '';

foreach($content as $row) {
    $columns = explode(";", $row);
    if($columns[0] == '10') { //Bundesland
        $current_state = $columns[7];
    }
    elseif($columns[0] == '40') { //Landkreise
        $current_county = $columns[7];
    }
    elseif($columns[0] == '50') { //Gemeindeverband
        // Nothing to do so far
    }
    elseif($columns[0] == '60') { //Gemeinden
        $rs = $columns[2] . $columns[3] . $columns[4] . $columns[5] . $columns[6];
        $name = $columns[7];
        $zip = $columns[13];

        $stmt = $conn->prepare("INSERT INTO `municipalities` (`key`, `name`, `county`, `state`, `website`, `email`, `ps_street`, `ps_zip`, `ps_city`, `timestramp`, `valid`)
                                VALUES (?, ?, ?, ?, '', '', '', '', '', CURRENT_TIMESTAMP, '0');");
        $stmt->bind_param("ssss", $rs, $name, $current_county, $current_state);
        if($stmt->execute()) {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO zip_codes (`municipality_key`, `zip`) VALUES (?, ?)");
            $stmt->bind_param("ss", $rs, $zip);
            if($stmt->execute()){
                echo "Stored $rs.\n";
            } else {
                echo "Failed to store ZIP code for $rs.\n";
            }
            $stmt->close();
        } else {
            $stmt->close();
            echo "Failed to store $rs.\n";
        }
    }
}

?>