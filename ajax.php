<?php
header("Content-Type: application/json");
$cfg = parse_ini_file("config.ini");
require_once("includes/database.php");

$route = trim($_SERVER['REQUEST_URI'], "/");
$route = explode("/", $route);

if(isset($route[2])) {
    $query = urldecode($route[2]);
} else {
    $content = file_get_contents('php://input');
    $query = json_decode($content)[0];
}

if($route[0] == 'api' && $route[1] == 'quicksearch') {
    require_once("includes/quicksearch.php");
}
elseif($route[0] == 'api' && $route[1] == 'details') {
    require_once("includes/details.php");
}
elseif($route[0] == 'api' && $route[1] == 'search') {
    require_once("includes/search.php");
}
elseif($route[0] == 'api' && $route[1] == 'searchcounty') {
    require_once("includes/searchcounty.php");
}
else
{
    echo json_encode(array("error" => "end point not found"));
}

?>