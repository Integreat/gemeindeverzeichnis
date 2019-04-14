<?php
echo "Updating details from file.\n";
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