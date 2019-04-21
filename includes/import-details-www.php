<?php
echo "Updating details from WWW.\n";
/**
 * curl 'https://www.statistikportal.de/de/produkte/gemeindeverzeichnis?ajax_form=1&_wrapper_format=drupal_ajax' \
 * -H 'Accept: application/json' -H 'Application/x-www-form-urlencoded; charset=UTF-8' \
 * --data 'mi_search=01051064&form_id=municipality_index_search'
 */
$stmt = $conn->prepare("SELECT `key` FROM `municipalities_core`");
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) {
    $headers = array (
        'Accept: application/json',
        'Application/x-www-form-urlencoded; charset=UTF-8'
    );
    $fields = "mi_search=".$row['key']."&form_id=municipality_index_search";
    $url = "https://www.statistikportal.de/de/produkte/gemeindeverzeichnis?ajax_form=1&_wrapper_format=drupal_ajax";

    echo "Update with search ".$row['key']."\n";

    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_POST, true );
    curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );
    $result = curl_exec ( $ch );
    curl_close ( $ch );
    $xml = json_decode($result)[0]->data;
    $xml = preg_replace('/\s+/', ' ',$xml);
    $p = xml_parser_create();
    xml_parse_into_struct($p, $xml, $vals, $index);
    $data = array();
    $multiple_results = false;
    $update_first = false;
    foreach($vals as $item) {
        if(!array_key_exists('value', $item)){
            continue;
        }
        $value = trim($item['value']);
        if($item['level'] == 5 && strlen($value) > 0 ) {
            if($value == 'Stand') {
                $next_key = "updated";
                if($update_first == false) {
                    $update_first = true;
                } else {
                    $multiple_results = true;
                }
            } elseif($value == 'Bundesland') {
                $next_key = "state";
            } elseif($value == 'Regierungsbezirk') {
                $next_key = "district";
            } elseif($value == 'Kreis') {
                $next_key = "county";
            } elseif($value == 'Amtl. Gemeindeschlüssel') {
                $next_key = "key";
            } elseif($value == 'Gemeindetyp') {
                $next_key = "type";
            } elseif($value == 'Postleitzahl') {
                $next_key = "zip";
            } elseif($value == 'Anschrift der Gemeinde') {
                $next_key = "address_recipient";
            } elseif($value == 'Straße') {
                $next_key = "address_street";
            } elseif($value == 'Ort') {
                $next_key = "address_city";
            } elseif($value == 'Fläche in km²') {
                $next_key = "area";
            } elseif($value == 'Einwohner') {
                $next_key = "population";
            } elseif($value == 'männlich') {
                $next_key = "population_male";
            } elseif($value == 'weiblich') {
                $next_key = "population_female";
            } elseif($value == 'je km²') {
                $next_key = "population_area";
            } else {
                $data[$next_key] = $value;
                $next_key = null;
            }
        }
    }
    if(!array_key_exists('state', $data)){
        continue;
    }
    if($multiple_results) {
        $data['address_recipient'] = "";
        $data['address_street'] = "";
        $address_zip = "";
        $address_city = "";
    } else {
        $address_zip = substr($data['address_city'], 0, 5);
        $address_city = substr($data['address_city'], 6);
    }
    $data['population'] = str_replace(".", "", $data['population']);
    $data['population_male'] = str_replace(".", "", $data['population_male']);
    $data['population_female'] = str_replace(".", "", $data['population_female']);
    $data['area'] = str_replace(",", ".", $data['area']);
    $stmt = $conn->prepare("UPDATE `municipalities_core` SET `county` = ?, `state` = ?, `district` = ?, `type` = ?, `population` = ?, `population_male` = ?, `population_female` = ?, `area` = ?, `address_recipient` = ?, `address_street` = ?, `address_zip` = ?, `address_city` = ? WHERE `key` = ?");
    $stmt->bind_param("sssssssssssss", $data['county'], $data['state'], $data['district'], $data['type'], $data['population'], $data['population_male'], $data['population_female'], $data['area'], $data['address_recipient'], $data['address_street'], $address_zip, $address_city, $row['key']);
    if($stmt->execute()) {
        echo "Updated ".$row['key']."\n";
    }
    $stmt->close();
}