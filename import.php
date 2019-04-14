<?php
$cfg = parse_ini_file("config.ini");
require_once("includes/database.php");

if(in_array('--drop', $argv)) {
    require_once("includes/import-base.php")
}

if(in_array('--file', $argv)) {
    //import file from https://github.com/digineo/gemeindeverzeichnis
    require_once("includes/import-details-file.php");
} else {
    //Crawl data from destatis
    require_once("includes/import-details-www.php");
}
?>