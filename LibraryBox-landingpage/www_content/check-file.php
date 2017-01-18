<?php

$query_arr = array();
parse_str($_SERVER['QUERY_STRING'], $query_arr);

if(!isset($query_arr['p'])) {
    http_response_code(400);
    die();
}

$path = $query_arr['p'];
// $base_path = '/Users/ryan/projects/scatterbox/temp/';
$base_path = '/mnt/usb/LibraryBox/';

$full_path = $base_path . $path;

if(file_exists($full_path)) {
    echo 'true';
} else {
    echo 'false';
}

?>