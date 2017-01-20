<?php

include('globals.php');

$id = isset($_GET["id"]) ? $_GET['id'] : '';
if(strlen($id) === 0) {
    redirect('/');
    die();
}
if(!loggedIn() || getUser()[0]['admin'] < 1) {
    redirect('/');
    die();
}

$requestMethod = $_SERVER["REQUEST_METHOD"];
if($requestMethod === "POST") {

    deleteUser($id);
    redirect('users.php');
    die();

} else {
    redirect('/');
    die();
}

?>
