<?php
$cfg = parse_ini_file("config.ini");
require_once("includes/database.php");

$stmt = $conn->prepare("DELETE FROM `municipalities`");
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM `zip_codes`");
$stmt->execute();
$stmt->close();

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
 * 15. Längengrad
 * 16. Breitengrad
 * ...
 */

$types = array();
$types[60] = "Markt";
$types[61] = "Kreisfreie Stadt";
$types[62] = "Stadtkreis";
$types[63] = "Stadt";
$types[64] = "Kreisangehörige Gemeinde";
$types[65] = "gemeindefreies Gebiet, bewohnt";
$types[66] = "gemeindefreies Gebiet, unbewohnt";
$types[67] = "große Kreisstadt";

$state = '';
$county = '';
$district = '';

foreach($content as $row) {
    $columns = explode(";", $row);
    if($columns[0] == '10') { //Bundesland
        $state = $columns[7];
        $county = '';
        $district = '';
    }
    elseif($columns[0] == '20') { //Regierungsbezirk
        $district = $columns[7];
        $county = '';
    }
    elseif($columns[0] == '40') { //Landkreise
        $county = $columns[7];
    }
    elseif($columns[0] == '50') { //Gemeindeverband
        // Nothing to do so far
    }
    elseif($columns[0] == '60') { //Gemeinden
        $rs = $columns[2] . $columns[3] . $columns[4] . $columns[6];
        $name = $columns[7];
        $zip = $columns[13];
        $type = $types[(int)$columns[1]];
        $pop = str_replace(" ", "", $columns[9]);
        $pop_male = str_replace(" ", "", $columns[10]);
        $pop_female = str_replace(" ", "", $columns[11]);
        $longitude = str_replace(",", ".", $columns[14]);
        $latitude = str_replace(",", ".", $columns[15]);
        $stmt = $conn->prepare("INSERT INTO `municipalities`
        (`key`, `name`, `county`, `state`, `district`, `type`, `population`, `population_male`, `population_female`, `longitude` , `latitude`,   `area`     , `valid`) VALUES
        ( ?   ,  ?    ,  ?      ,  ?     ,  ?        ,  ?    ,  ?          ,  ?               ,  ?                 ,  ?          ,  ?        ,    ?         , '0'    )");
        $stmt->bind_param("ssssssssssss",
        $rs   ,  $name, $county , $state , $district , $type , $pop        , $pop_male        , $pop_female        , $longitude  , $latitude, $columns[8]);
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

/**
 *  1. Stand
 *  2. Bundesland
 *  3. Regierungs-Bezirk
 *  4. Kreisname
 *  5. Amtl.Gemeindeschlüssel
 *  6. PLZ Gemeindenamen
 *  7. Gemeindetyp
 *  8. Anschrift der Gemeinde
 *  9. Straße
 * 10. PLZ Ort
 * 11. Fläche km2
 * 12. Einwohner gesamt
 * 13. Einwohner männlich
 * 14. Einwohner weiblich
 * 15. Einwohner je km2
 */

$content = explode("\n", file_get_contents("data-station.csv"));
foreach($content as $row) {
    $columns = explode(";", $row);
    $stmt = $conn->prepare("UPDATE `municipalities` SET `address_recipient`=?, `address_street`=?, `address_zip`=?, `address_city`=?, `valid`=1 WHERE `key` = ?");
    $address_zip = substr($columns[9], 0, 5);
    $address_city = substr($columns[9], 6);
    $stmt->bind_param("sssss", $columns[7], $columns[8], $address_zip, $address_city, $columns[4]);
    if($stmt->execute()) {
        echo "Updated $columns[4].\n";
    }
}
?>