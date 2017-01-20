<?php

include('util.php');

$content_path = (getenv('scatterbox_content') != null) ? getenv('scatterbox_content') : '/mnt/usb/LibraryBox/Content';
$shared_path = (getenv('scatterbox_shared') != null) ? getenv('scatterbox_shared') : '/mnt/usb/LibraryBox/Shared';
$public_path = (getenv('scatterbox_public') != null) ? getenv('scatterbox_public') : '/mnt/usb/LibraryBox/Shared/public';
$user_files_path = (getenv('scatterbox_user_files') != null) ? getenv('scatterbox_user_files') : '/mnt/usb/LibraryBox/Shared/user-files';

if(!file_exists($shared_path)) {
    mkdir($shared_path);
}
if(!file_exists($public_path)) {
    mkdir($public_path);
    $folders = array('apps', 'audio', 'music', 'pictures', 'text', 'video');
    foreach($folders as $folder) {
        mkdir("$public_path/$folder");
    }
}
if(!file_exists($user_files_path)) {
    mkdir($user_files_path);
    $folders = array();
    for($i = 1; $i < 7; $i++) {
        $folders[count($folders)] = "group-$i";
    }
    foreach($folders as $folder) {
        mkdir("$user_files_path/$folder");
    }
}

if(!isset($GLOBALS['groups'])) {
    $groups_json_path = "$content_path/groups.json";
    // echo $groups_json_path;
    if(!file_exists($groups_json_path)) {
    	//print 'file does not exist!';
        $file=fopen($groups_json_path,"w");
        $names = array("1", "2", "3", "4", "5", "6");
        fwrite($file, json_encode($names));
        fclose($file);
    } else {
    	//print 'file does exist!';
    }
    $file_contents_arr = file($groups_json_path);
    $file_contents = implode($file_contents_arr);
    // print $file_contents;
    $file_contents = trim($file_contents);
    $names = json_decode($file_contents);
    $GLOBALS['groups'] = $names;
}

if(!isset($GLOBALS['public_folders'])) {
    $folders_json_path = "$content_path/public_folders.json";
    if(!file_exists($folders_json_path)) {

        $public_folder_names = array(
            'apps' => array(
                'name' => 'apps',
                'custom' => '',
                'localizationKey' => 'folderApps',
                'icon' => 'tablet'
            ),
            'audio' => array(
                'name' => 'audio',
                'custom' => '',
                'localizationKey' => 'folderAudio',
                'icon' => 'bullhorn'
            ),
            'music' => array(
                'name' => 'music',
                'custom' => '',
                'localizationKey' => 'folderMusic',
                'icon' => 'music'
            ),
            'pictures' => array(
                'name' => 'pictures',
                'custom' => '',
                'localizationKey' => 'folderPictures',
                'icon' => 'picture-o'
            ),
            'text' => array(
                'name' => 'text',
                'custom' => '',
                'localizationKey' => 'folderText',
                'icon' => 'file-text-o'
            ),
            'video' => array(
                'name' => 'video',
                'custom' => '',
                'localizationKey' => 'folderVideo',
                'icon' => 'video-camera'
            )
        );
        
        $file=fopen($folders_json_path,"w");
        fwrite($file, json_encode($public_folder_names));
        fclose($file);
    } else {
    	//print 'file does exist!';
    }
    $file_contents_arr = file($folders_json_path);
    $file_contents = implode($file_contents_arr);
    // print $file_contents;
    $file_contents = trim($file_contents);
    $names = json_decode($file_contents, true);
    $GLOBALS['public_folders'] = $names;
}

if(!isset($GLOBALS["users"])) {
    $users_path = "$content_path/users.txt";
    if(!file_exists($users_path)) {
        $file=fopen($users_path,"w");
        $pass = sha1("adminpass");
        fwrite($file, "1234567890,admin,$pass,1,1,021222324252");
        fclose($file);
    }

    $users = array();

    // print "It is not yet set!";

    $fileContents = file($users_path);

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
            // $userStr = $u["id"] . "," . $u["username"] . "," . $u["password"];
            $userStr = $u["id"] . "," . $u["username"] . "," . $u["password"] . "," . $u["admin"] . "," . $u["folders"] . "," . makePermissionStr($u["permissions"]);
            fwrite($file, $userStr . "\n");
        }
    } else {
        $pass = sha1("adminpass");
        fwrite($file, "1234567890,admin,$pass,1,1,021222324252");
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
    //foreach($GLOBALS['groups'] as $name) {
    //	print $name . ',';
    //}
    return $GLOBALS['groups'];
}

function saveGroupNames($arr) {

    if(count($arr) != 6) {
        return false;
    }
    
    $names = array();

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
    $GLOBALS['groups'] = $names;

    return true;
}

function getPublicFolders() {
    return $GLOBALS['public_folders'];
}

function savePublicFolderNames($arr) {

    if(count($arr) != 6) {
        return false;
    }

    $folders = getPublicFolders();
    foreach($folders as $name => $custom) {
        if(isset($arr[$name])) {
            $folders[$name]['custom'] = cleanStr($arr[$name]);
        }
    }

    $encoded_folders = json_encode($folders);
    $file = fopen("public_folders.json", "w");
    fwrite($file, $encoded_folders);
    fclose($file);
    $GLOBALS['public_folders'] = $folders;

    return true;
}

?>
