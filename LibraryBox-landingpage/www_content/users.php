<?php
    include("globals.php");

    $requestMethod = $_SERVER["REQUEST_METHOD"];

    if($requestMethod === "POST") {
        $username = $_POST['newusername'];
        $password = $_POST['newpassword'];
        $is_admin = $_POST['admin'];
        $can_create_folders = $_POST['folders'];

        $permissions = array();
        for($i = 0; $i < 6; $i++) {
            $permissions[$i] = $_POST["folder-" . $i];
        }

        $success = addUser($username, $password, $is_admin, $can_create_folders, $permissions);

        if($success) {
            redirect("/content/users.php");
        }

    }

    $users = getUsers();
    $userListItems = array();
    foreach($users as $u) {
        $idx = count($userListItems);
        $adminData = ($u["admin"] === 0) ? "<td class='text-danger' data-l10n-id='commonNo'>No</td>" : "<td class='text-success' data-l10n-id='commonYes'>Yes</td>";
        $folders_data = ($u["folders"] === 0) ? "<td class='text-danger' data-l10n-id='commonNo'>No</td>" : "<td class='text-success' data-l10n-id='commonYes'>Yes</td>";
        $userListItems[$idx] = "<tr><td><a href='edituser.php?id=" . $u["id"] . "'>" . $u["username"] . "</a></td>" . $adminData . "</td></td>" . $folders_data . "</tr>";
    }

    $status = 0;

    $permission_selects = array();
    $folders = getGroupNames();
    for($i = 0; $i < count($folders); $i++) {
        $name = $folders[$i];
        $inputName = "folder-" . $i;
        $permission_selects[$i] = "<div class='form-group'>
                <label>$name</label>
                <select class='form-control input-sm' name='$inputName'>
                    <option value='0' data-l10n-id='usersFormGroupPermissionsNone' selected>None</option>
                    <option value='1' data-l10n-id='usersFormGroupPermissionsRead'>Read</option>
                    <option value='2' data-l10n-id='usersFormGroupPermissionsWrite'>Write</option>
                </select>
            </div>";
    }

    include("head.php");
    include("header.php");
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h2 data-l10n-id='usersUserManagement'>User Management</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th data-l10n-id='usersUserTableUsername'>Username</th>
                        <th data-l10n-id='usersUserTableAdmin'>Admin</th>
                        <th data-l10n-id='usersUserTableEditFolders'>Edit Folders</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        print implode($userListItems);
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7 col-lg-5">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title" style='font-family:"Helvetiva Neue", Helvetica, Arial, sans-serif;' data-l10n-id='usersFormAddNewuser'>Add New User</h3>
                </div>
                <div class="panel-body">
                    <form action='/content/users.php' method='post'>
                        <?php 
                            if($status === 1) {
                                print "<p>There was a problem adding the user.</p>";
                            }
                        ?>
                        <div class="form-group">
                            <label data-l10n-id="commonUsername">Username</label>
                            <input class="form-control" type='text' name='newusername' required></input>
                        </div>
                        <div class="form-group">
                            <label data-l10n-id="commonPassword">Password</label>
                            <input class="form-control" type='password' name='newpassword' required></input>
                        </div>
                        <div class="form-group">
                            <label data-l10n-id="usersFormAdmin">Admin?</label>
                            <select class="form-control" name="admin">
                                <option data-l10n-id="commonYes" value="1">Yes</option>
                                <option data-l10n-id="commonNo" value="0" selected>No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label data-l10n-id="usersFormEditFolders">Can create & delete folders?</label>
                            <select class="form-control" name="folders">
                                <option data-l10n-id="commonYes" value="1">Yes</option>
                                <option data-l10n-id="commonNo" value="0" selected>No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label data-l10n-id="usersFormGroupPermissions">Group Permissions</label>
                            <div class="well well-sm">
                                <?php
                                    foreach($permission_selects as $permission_select) {
                                        print $permission_select;
                                    }
                                ?>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-primary" type='submit'><i class="fa fa-plus"></i> <span  data-l10n-id='usersFormAddNewuser'>Add New User</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include("footer.php");
include("foot.php");
?>