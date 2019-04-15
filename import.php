<?php
$cfg = parse_ini_file("config.ini");
require_once("includes/database.php");

if(in_array('--base', $argv) or in_array('--all', $argv)) {
    require_once("includes/import-base.php");
}

if(in_array('--file', $argv) or in_array('--all', $argv)) {
    //import file from https://github.com/digineo/gemeindeverzeichnis
    require_once("includes/import-details-file.php");
}

if(in_array('--webupdate', $argv) or in_array('--all', $argv)) {
    //Crawl data from destatis
    require_once("includes/import-details-www.php");
}

if(in_array('--homepage', $argv) or in_array('--all', $argv)) {
    //Crawl data from destatis
    require_once("includes/import-homepages.php");
}

?>