<?php

include("globals.php");
include("head.php");
include("header.php");

$requestMethod = $_SERVER["REQUEST_METHOD"];

$status = 0;

if($requestMethod === "POST") {
    $folders = getPublicFolders();
    $new_names = array();
    foreach($folders as $name => $val) {
        // echo '$name is ' . $name;
        $custom = isset($_POST[$name]) ? $_POST[$name] : '';
        // echo 'custom is ' . $custom;
        $new_names[$name] = $custom;
    }
    $success = savePublicFolderNames($new_names);
    if($success) {
        $status = 2;
    } else {
        $status = 1;
    }
}

$name_inputs = "";
$public_folders = getPublicFolders();
foreach($public_folders as $key => $val) {
    $name = $val['name'];
    $localization_key = $val['localizationKey'];
    $icon = $val['icon'];
    if(isset($val['custom'])) {
        $custom_name = htmlspecialchars($val['custom'], ENT_QUOTES);
        $name_inputs .= "<div class='form-group'>
            <label><i class='fa fa-$icon'></i> <span data-l10n-id='$localization_key'>$name</span></label>
            <input class='form-control' type='text' name='$name' value='$custom_name' />
        </div>";
    } else {
        $name_inputs .= "<div class='form-group'>
            <label><i class='fa fa-$icon'></i> <span data-l10n-id='$localization_key'>$name</span></label>
            <input class='form-control' type='text' name='$name' value='' />
        </div>";
    }
}

?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h2 data-l10n-id='publicFoldersFormFolderNames'>Public Folder Names</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7 col-lg-5">
            <form action='/content/public-folders.php' method='post'>
                <?php
                    if($status === 1) {
                        print '<div class="alert alert-danger" data-l10n-id="publicFoldersFormError">There was a problem updating the folder names.</div>';
                    } else if($status === 2) {
                        print '<div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                            <span  data-l10n-id="publicFoldersFormSuccess">Folder names successfully updated.</span>
                        </div>';
                    }
                    print $name_inputs;
                ?>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i> <span data-l10n-id='commonSaveChanges'>Save Changes</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include("footer.php");
include("foot.php");
?>