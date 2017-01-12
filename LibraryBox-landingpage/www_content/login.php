<?php

include("globals.php");
include("head.php");
include("header.php");

$requestMethod = $_SERVER["REQUEST_METHOD"];

$isValid = true;

if($requestMethod === "POST") {

    $users = getUsers();

    $isValid = false;

    $username = $_POST['username'];
    $password = $_POST['password'];

    foreach($users as $user) {
        if(($user["username"] === $username) && ($user["password"] === $password)) {
            $isValid = true;

            setCookie("sb_auth", $user["id"]);

            break;
        }
    }

    if($isValid) {
        redirect("/");
    }

} else {

    $userArr = getUser();
    if(loggedIn()) {
        redirect("/");
    }

}

?>

  <div class="container">
    <div class="row">
      <div class="col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4">
        <form action='login.php' method='post'>
          <?php
            if(!$isValid) {
              print '<div class="alert alert-danger">Invalid Username or Password</div>';
            }
          ?>

            <div class="form-group">
              <label data-l10n-id="loginFormUsername">Username</label>
              <input type='text' class="form-control" name='username' autofocus></input>
            </div>
            <div class="form-group">
              <label data-l10n-id="loginFormPassword">Password</label>
              <input type='password' class="form-control" name='password'></input>
            </div>
            <div class="form-group">
              <button type='submit' class="btn btn-primary"><span class="glyphicon glyphicon-send"></span> <span data-l10n-id="loginFormSubmit">Submit</span></button>
            </div>
        </form>
      </div>
    </div>
  </div>

<?php
include("footer.php");
include("foot.php");
?>