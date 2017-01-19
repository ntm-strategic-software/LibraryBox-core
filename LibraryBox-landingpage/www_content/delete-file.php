<?php

include('globals.php');

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    return rmdir($dirPath);
}

if(!loggedIn()) {
    redirect('/');
    die();
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod !== 'POST') {
    http_response_code(400);
    die();
}

$path;
if(isset($_SERVER['QUERY_STRING'])) {
    $query_arr = array();
    parse_str($_SERVER['QUERY_STRING'], $query_arr);
    if(!isset($query_arr['p'])) {
        http_response_code(400);
        die();
    }
    $path = $query_arr['p'];
} else {
    http_response_code(400);
    die();
}

$path = trim($path);
$path = ($path[0] === '/') ? substr($path, 1) : $path;
$path_arr = explode('/', $path);
$base_path = '/mnt/usb/LibraryBox/';
$full_path = '';

if($path_arr[1] === 'user-files') {
    $full_path = $base_path . $path;
    $user = getUser()[0];
    $permissions = $user['permissions'];
    $group_num = (int)$path_arr[2][strlen($path_arr[2]) - 1];
    // echo $group_num;
    $group_idx = $group_num - 1;
    if($permissions[$group_idx] < 2) {
        redirect('/');
        die();
    }
} elseif($path_arr[1] === 'public') {
    $full_path = $base_path . $path;
} else {
    redirect('/');
    die();
}

if(!file_exists($full_path)) {
    http_response_code(400);
    die();
}

$success;
if(is_dir($full_path)) {
    $success = deleteDir($full_path);
} else {
    $success = unlink($full_path);
}

if(!$success) {
    http_response_code(400);
    die();
}

?>