<?php
include("globals.php");

$status = 0;

$id = isset($_GET["id"]) ? $_GET['id'] : '';
if(strlen($id) === 0) {
    http_response_code(404);
    die();
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

$username;
$password;

if($requestMethod === "POST") {
    $username = $_POST['editedusername'];
    $password = $_POST['editedpassword'];
    
    print "id: " . $id;
    
    $success = editUser($id, $username, $password);
    
    if($success) {
        $status = 2;
    } else {
        $status = 1;
    }
    
}

$user = getUser($id);
    if(count($user > 0) && ($user[0]["id"] === $id)) {
        $username = $user[0]['username'];
        $password = $user[0]['password'];
    } else {
        http_response_code(404);
        die();
    }

include("head.php");
include("header.php");

?>

<div class="container">
    <div class="row">
        <div class="col-sm-6 col-lg-4">
            <form action='<?php print "edituser.php?id=" . $id; ?>' method='post'>
                <?php
                    if($status === 1) {
                        print '<div class="alert alert-danger" data-l10n-id="editUserProblem">There was a problem editing the user data.</div>';
                    } else if($status === 2) {
                        print '<div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <span data-l10n-id="editUserSuccess">User data successfully updated.</span>
                        </div>';
                    }
                ?>
                <div class="form-group">
                    <label data-l10n-id="loginFormUsername">Username</label>
                    <input class="form-control" type='text' name='editedusername' value="<?php print $username ?>"></input>
                </div>
                <div class="form-group">
                    <label data-l10n-id="loginFormPassword">Password</label>
                    <input class="form-control" type='password' name='editedpassword' value="" placeholder="enter new password"></input>
                </div>
                <div class="form-group">
                    <button type='submit' class="btn btn-primary"><i class="fa fa-refresh"></i> <span data-l10n-id="editUserSaveChanges">Save Changes</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include("footer.php");
include("foot.php");
?>
