<?php

include('util.php');

if(!isset($GLOBALS["users"])) {

    if(!file_exists("users.txt")) {
        $file=fopen("users.txt","w");
        fwrite($file, "1234567890,admin,adminpass,1,021222324252");
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
                "permissions" => isset($userArr[4]) ? permissionsHash($userArr[4]) : []
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

function addUser($username, $password) {
    $username = cleanStr($username);
    $password = cleanStr($password);
    $admin = 0;
    $permissions = "021220314150";
    $id = uuid();

    if((strlen($username) === 0) || (strlen($password) === 0)) {
        return false;
    }

    $users = getUsers();
    foreach($users as $user) {
        if($user["username"] === $username) {
            return false;
        }
    }

    $fileContents = file("users.txt");
    $fileContents[count($fileContents)] = $id . "," . $username . "," . $password . "," . $admin . "," . $permissions;
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
        "permissions" => permissionsHash($permissions)
    );

    return true;

}

function editUser($id = "", $username = "", $password = "") {
    if(!loggedIn()) {
        return false;
    }

    // $currentUser = getUser()[0];

    $username = cleanStr($username);
    $password = cleanStr($password);
    $id = cleanStr($id);
    $admin = 0;
    $permissions = "021220314150";

    if(strlen($id) === 0) {
        return false;
    }

    $userArr = getUser($id);
    if(count($userArr) === 0) {
        return false;
    }

    $user = $userArr[0];

    $username = (strlen($username) > 0) ? $username : $user["username"];
    $password = (strlen($password) > 0) ? $password : $user["password"];

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
            $users[$i]["permissions"] = $permissions;
            break;
        }
    }

    $GLOBALS["users"] = $users;
    $file = fopen("users.txt", "w");
    foreach($users as $u) {
        $userStr = $u["id"] . "," . $u["username"] . "," . $u["password"] . "," . $admin . "," . $permissions;
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

function getFolderNames() {
    $names = array();
    for($i = 0; $i < 6; $i++) {
        $names[$i] = $i + 1;
    }
    return $names;
}

?>
