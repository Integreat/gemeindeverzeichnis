<?php
$cfg = parse_ini_file("config.ini");
require_once("includes/database.php");

if("/api/quicksearch" == $_SERVER['REQUEST_URI']) {
    require_once("includes/quicksearch.php");
}
elseif("/api/details" == $_SERVER['REQUEST_URI']) {
    require_once("includes/details.php");
}
elseif("/api/results" == $_SERVER['REQUEST_URI']) {
    require_once("includes/results.php");
}
else
{
    json_encode(array("error" => "end point not found"));
}

?>