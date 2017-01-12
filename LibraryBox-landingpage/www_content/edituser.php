<?php
include("globals.php");

$status = 0;

$id = isset($_GET["id"]) ? $_GET['id'] : '';
if(strlen($id) === 0) {
    http_response_code(404);
    die();
}

$current_user = getUser()[0];
// print $current_user;

$requestMethod = $_SERVER["REQUEST_METHOD"];

$username;
$password;
$admin;
$edit_folders;
$permissions;

if($requestMethod === "POST") {
    $posted_username = $_POST['editedusername'];
    $posted_password = $_POST['editedpassword'];

    // print count($posted_permissions);
    // foreach($posted_permissions as $p) {
    //     print $p;
    // }
    
    if($current_user["admin"] === 1) {
        $is_admin = $_POST['admin'];
        $can_create_folders = $_POST['folders'];

        $posted_permissions = array();
        for($i = 0; $i < 6; $i++) {
            $posted_permissions[$i] = $_POST["folder-" . $i];
        }
        $success = editUser($id, $posted_username, $posted_password, $is_admin, $can_create_folders, $posted_permissions);
    } else {
        $success = editUser($id, $posted_username, $posted_password);
    }
    
    
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
    $admin = $user[0]['admin'];
    $edit_folders = $user[0]['folders'];
    $permissions = $user[0]['permissions'];
} else {
    http_response_code(404);
    die();
}

$permission_selects = array();
$folders = getFolderNames();
for($i = 0; $i < count($folders); $i++) {
    $name = $folders[$i];
    $inputName = "folder-" . $i;
    $permission = $permissions[$i];
    // print $permission;
    $options = "";
    if($permission === 2) {
        $options = "<option value='0'>None</option>
            <option value='1'>Read</option>
            <option value='2' selected>Write</option>";
    } else if($permission === 1) {
        $options = "<option value='0'>None</option>
            <option value='1' selected>Read</option>
            <option value='2'>Write</option>";
    } else {
        $options = "<option value='0' selected>None</option>
            <option value='1'>Read</option>
            <option value='2'>Write</option>";
    }
    $permission_selects[$i] = "<div class='form-group'>
            <label>$name</label>
            <select class='form-control input-sm' name='$inputName'>
                $options
            </select>
        </div>";
}

include("head.php");
include("header.php");

?>

<div class="container">
    <div class="row">
        <div class="col-sm-7 col-lg-5">
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
                <?php
                    if($current_user["admin"] === 1) {

                        $admin_options;
                        if($admin === 1) {
                            $admin_options = '<option value="1" selected>Yes</option>' . '<option value="0">No</option>';
                        } else {
                            $admin_options = '<option value="1">Yes</option>' . '<option value="0" selected>No</option>';
                        }
                        print "<div class='form-group'>
                            <label>Admin?</label>
                                <select class='form-control' name='admin'>
                                    $admin_options
                                </select>
                            </div>";
                        
                        $folder_options;
                        if($edit_folders === 1) {
                            $folder_options = '<option value="1" selected>Yes</option>' . '<option value="0">No</option>';
                        } else {
                            $folder_options = '<option value="1">Yes</option>' . '<option value="0" selected>No</option>';
                        }
                        print "<div class='form-group'>
                                <label>Can create & delete folders?</label>
                                <select class='form-control' name='folders'>
                                    $folder_options
                                </select>
                            </div>";
                        
                        print "<div class='form-group'>
                            <label>Group Permissions</label>
                            <div class='well well-sm'>";
                        foreach($permission_selects as $permission_select) {
                            print $permission_select;
                        }
                        print "</div></div>";
                    }
                ?>
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
