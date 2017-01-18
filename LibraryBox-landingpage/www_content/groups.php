<?php

include("globals.php");
include("head.php");
include("header.php");

$requestMethod = $_SERVER["REQUEST_METHOD"];

$status = 0;

if($requestMethod === "POST") {
    $new_names = array();
    for($i = 0; $i < 6; $i++) {
        $key = "group-" . $i;
        $new_names[$i] = isset($_POST[$key]) ? $_POST[$key] : "";
    }
    $success = saveGroupNames($new_names);
    if($success) {
        $status = 2;
    } else {
        $status = 1;
    }
}

$name_inputs = "";
$names = getGroupNames();
for($i = 0; $i < count($names); $i++) {
    $name = $names[$i];
    $group_number = $i + 1;
    $group_label = "Group " . $group_number;
    if($name == $group_number) {
        $name_inputs = $name_inputs . "<div class='form-group'>
                <label>$group_label</label>
                <input class='form-control' type='text' name='group-$i' value=''></input>
            </div>";
    } else {
        $name_inputs = $name_inputs . "<div class='form-group'>
                <label>$group_label</label>
                <input class='form-control' type='text' name='group-$i' value='$name'></input>
            </div>";
    }
}

list($path) = explode('?', $_SERVER['REQUEST_URI']);
$path = ltrim(rawurldecode($path), '/');

?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h2>Group Names</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7 col-lg-5">
            <form action='/content/groups.php' method='post'>
                <?php
                    if($status === 1) {
                        print '<div class="alert alert-danger">There was a problem updating the group names.</div>';
                    } else if($status === 2) {
                        print '<div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <span >Group names successfully updated.</span>
                        </div>';
                    }
                    print $name_inputs;
                ?>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include("footer.php");
include("foot.php");
?>