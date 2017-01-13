<?php

include('util.php');

if(!isset($GLOBALS["users"])) {

    if(!file_exists("users.txt")) {
        $file=fopen("users.txt","w");
        $pass = sha1("adminpass");
        fwrite($file, "1234567890,admin,$pass,1,1,021222324252");
        fclose($file);
    }

    if(!file_exists("groups.json")) {
        $file=fopen("groups.json","w");
        $names = array("1", "2", "3", "4", "5", "6");
        fwrite($file, json_encode($names));
        fclose($file);
    }

    $users = array();

    // print "It is not yet set!";

    $fileContents = file("users.txt");

    for($i = 0; $i < count($fileContents); $i++) {
        $userStr = $fileContents[$i];
        $userStr = trim($userStr);
        if(strlen($userStr) > 0) {
            $userArr = explode(",", $userStr);
            $users[$i] = array(
                "id" => isset($userArr[0]) ? $userArr[0] : "",
                "username" => isset($userArr[1]) ? $userArr[1] : "",
                "password" => isset($userArr[2]) ? $userArr[2] : "",
                "admin" => isset($userArr[3]) ? (int)$userArr[3] : 0,
                "folders" => isset($userArr[4]) ? (int)$userArr[4] : 0,
                "permissions" => isset($userArr[5]) ? permissionsHash($userArr[5]) : []
            );
            $count = count($users);
        }
    }

    $GLOBALS["users"] = $users;
}

function getUsers() {
    return $GLOBALS["users"];
}

function getUser($idToGet = "") {
    $userArr = array();
    if(isset($_COOKIE['sb_auth'])) {
        $id = $_COOKIE['sb_auth'];
        $users = getUsers();
        foreach($users as $user) {
            if($user["id"] === $id) {
                $userArr[0] = $user;
                break;
            }
        }
        if((count($userArr) > 0) && (strlen($idToGet) > 0)) {
            foreach($users as $user) {
                if($user["id"] === $idToGet) {
                    $userArr[0] = $user;
                    break;
                }
            }
        }
    }
    return $userArr;
}

function cleanStr($str) {
    $str = trim($str);
    return str_replace(",", "", $str);
}

function makePermissionStr($arr) {
    $permissions = "";
    for($i = 0; $i < count($arr); $i++) {
        $permission = $arr[$i];
        $permissions = $permissions . $i . $permission;
    }
    return $permissions;
}

function addUser($username, $plain_password, $admin, $folders, $permissions_arr) {
    $username = cleanStr($username);
    $plain_password = cleanStr($plain_password);
    $id = uuid();

    $permissions = makePermissionStr($permissions_arr);

    if((strlen($username) === 0) || (strlen($plain_password) === 0)) {
        return false;
    }

    $password = sha1($plain_password);

    $users = getUsers();
    foreach($users as $user) {
        if($user["username"] === $username) {
            return false;
        }
    }

    $admin = (int)$admin;
    $folders = (int)$folders;

    $fileContents = file("users.txt");
    $fileContents[count($fileContents)] = $id . "," . $username . "," . $password . "," . $admin . "," . $folders . "," . $permissions;
    $file = fopen("users.txt", "w");
    foreach($fileContents as $userStr) {
        fwrite($file, trim($userStr) . "\n");
    }
    fclose($file);

    $GLOBALS["users"][count($GLOBALS["users"])] = array(
        "id" => $id,
        "username" => $username,
        "password" => $password,
        "admin" => $admin,
        "folders" => $folders,
        "permissions" => permissionsHash($permissions)
    );

    return true;

}

function editUser($id = "", $username = "", $plain_password = "", $admin = -1, $folders = -1, $permissions_arr = array()) {
    if(!loggedIn()) {
        return false;
    }

    $admin = (int)$admin;
    $folders = (int)$folders;

    $username = cleanStr($username);
    $plain_password = cleanStr($plain_password);
    $id = cleanStr($id);

    if(strlen($id) === 0) {
        return false;
    }

    $userArr = getUser($id);
    if(count($userArr) === 0) {
        return false;
    }

    $user = $userArr[0];

    $username = (strlen($username) > 0) ? $username : $user["username"];
    $password = (strlen($plain_password) > 0) ? sha1($plain_password) : $user["password"];
    $admin = ($admin > -1) ? $admin : $user["admin"];
    $folders = ($folders > -1) ? $folders : $user["folders"];
    $permissions = count($permissions_arr) > 0 ? $permissions_arr : $user["permissions"];

    $users = getUsers();

    foreach($users as $u) {
        if(($u["username"] === $username) && ($u["id"] != $id)) {
            return false;
        }
    }

    for($i = 0; $i < count($users); $i++) {
        $u = $users[$i];
        if($u["id"] === $id) {
            $users[$i]["username"] = $username;
            $users[$i]["password"] = $password;
            $users[$i]["admin"] = $admin;
            $users[$i]["folders"] = $folders;
            $users[$i]["permissions"] = $permissions;
            break;
        }
    }

    $GLOBALS["users"] = $users;
    $file = fopen("users.txt", "w");
    foreach($users as $u) {
        $userStr = $u["id"] . "," . $u["username"] . "," . $u["password"] . "," . $u["admin"] . "," . $u["folders"] . "," . makePermissionStr($u["permissions"]);
        fwrite($file, $userStr . "\n");
    }
    fclose($file);

    return true;

}

function deleteUser($id) {

    if(!loggedIn()) {
        return false;
    }

    $userArr = getUser();
    $currentUser = $userArr[0];

    $users = getUsers();
    $newUsers = array();

    for($i = 0; $i < count($users); $i++) {
        $u = $users[$i];
        if($u["id"] != $id) {
            $newUsers[count($newUsers)] = $u;
        }
    }

    $GLOBALS["users"] = $newUsers;
    $file = fopen("users.txt", "w");
    if(count($newUsers) > 0) {
        foreach($newUsers as $u) {
            $userStr = $u["id"] . "," . $u["username"] . "," . $u["password"];
            fwrite($file, $userStr . "\n");
        }
    } else {
        fwrite($file, "1234567890,admin,adminpass");
    }
    fclose($file);

    return true;

}

function loggedIn() {
    $userArr = getUser();
    if(count($userArr) > 0) {
        return true;
    } else {
        return false;
    }
}

function redirect($path) {
    echo "<script type='text/javascript'>window.location = '" . $path . "';</script>";
    die();
}

function getGroupNames() {
    $file_contents_arr = file("groups.json");
    $file_contents = implode($file_contents_arr);
    $file_contents = trim($file_contents);
    $names = json_decode($file_contents);
    return $names;
}

function saveGroupNames($arr) {

    if(count($arr) != 6) {
        return false;
    }

    for($i = 0; $i < count($arr); $i++) {
        $name = cleanStr($arr[$i]);
        if(strlen($name) === 0) {
            $names[$i] = (string)($i + 1);
        } else {
            $names[$i] = $name;
        }
    }

    $encoded_names = json_encode($names);
    $file = fopen("groups.json", "w");
    fwrite($file, $encoded_names);
    fclose($file);

    return true;
}

?>
