<?php

// ini_set('display_errors',1);
// error_reporting(E_ALL);

// phpinfo();
// die();

include('globals.php');

if(!loggedIn()) {
    redirect('/');
    die();
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

$err_message = '';
$path;
$status = 0;
if(isset($_SERVER['QUERY_STRING'])) {
    $query_arr = array();
    parse_str($_SERVER['QUERY_STRING'], $query_arr);
    if(!isset($query_arr['p'])) {
        http_response_code(400);
        die();
    }
    $path = $query_arr['p'];
} else {
    http_response_code(400);
    die();
}

// echo $path;

if($requestMethod === 'POST') {
    $path = trim($path);
    // echo '<br><br><br>' . '$path is ' . $path . '<br>';
    $path_arr = explode('/', $path);
    $base_path = '/mnt/usb/LibraryBox/';
    // $base_path = '/Users/ryan/projects/scatterbox/temp/';
    // echo '$path_arr[1] is ' . $path_arr[1];
    $full_path = '';

    if($path_arr[1] === 'user-files') {
        $full_path = $base_path . $path;
        $user = getUser()[0];
        $permissions = $user['permissions'];
        $group_num = (int)$path_arr[2][strlen($path_arr[2]) - 1];
        // echo $group_num;
        $group_idx = $group_num - 1;
        if($permissions[$group_idx] < 2) {
            redirect('/');
            die();
        }
    } elseif($path_arr[1] === 'public') {
        $full_path = $base_path . $path;
    } else {
        redirect('/');
        die();
    }
    
    if(!file_exists($full_path)) {
        redirect('/');
        die();
    }

    $new_file_path = $base_path . $path . basename($_FILES['newfile']['name']);

    $success = move_uploaded_file($_FILES['newfile']['tmp_name'], $new_file_path);
    if(!$success) {
        $status = 1;
        $err_message = '<span data-l10n-id="addFileFormError">There was a problem uploading the file.</span>';
    } else {
        redirect('/' . $path);
        die();
    }

}

include('head.php');
include('header.php');

?>

<div class="container">
    <div class="row">
        <div class="col-sm-7 col-md-6 col-lg-5">
            <?php
                if($status === 1) {
                    echo "<div class='alert alert-danger'>$err_message</div>";
                }
            ?>
            <form id="js-fileUploadForm" action="/content/new-file.php?p=<?php echo rawurlencode($path) ?>" method="post" enctype="multipart/form-data">
                <!--input type="hidden" name="MAX_FILE_SIZE" value="1000M" /!-->
                <div class="form-group" style="margin-bottom:30px;">
                    <label style="margin-bottom:15px;" data-l10n-id="addFileFormFileUpload">File Upload</label>
                    <input type="file" name="newfile" id="js-fileInput" required />
                </div>
                <div id="js-overwriteWarning" class='alert alert-warning' style="display:none;" data-l10n-id="addFileFormErrorFileExists">This file already exists in the current location. If you upload, it will overwrite the current file.</div>
                <div class="form-group">
                    <button id="js-submitButton" type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> <span data-l10n-id="addFileFormUploadFile">Upload File</span></button>
                </div>
            </form>
        </div>
    </div>
    <div id="js-uploadingMessage" style="display:none;" data-l10n-id="addFileFormUploading">Uploading</div>
</div>

<?php

include('footer.php');
include('foot.php');

?>
