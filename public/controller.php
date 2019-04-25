<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

header("Content-Type: application/json");

$route = trim($_SERVER['REQUEST_URI'], "/");
$route = explode("/", $route);

if(isset($route[2])) {
    $query = urldecode($route[2]);
} else {
    $content = file_get_contents('php://input');
    $query = json_decode($content)[0];
}

if($route[0] == 'api') {
    $path = dirname(__DIR__) . '/controller/api/' . $route[1] . '.php';
    if (preg_match("/^[a-zA-Z0-9]+$/", $route[1]) && file_exists($path)) {
        require $path;
        return;
    }
}

echo json_encode(array("error" => "end point not found"));
