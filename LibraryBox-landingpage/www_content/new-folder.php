<?php

include('globals.php');

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

if($requestMethod === 'POST') {

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
            <form action="/content/new-folder.php?p=<?php echo rawurlencode($path) ?>" method="post">
                <div class="form-group">
                    <label>New Folder Name</label>
                    <input type="text" class="form-control" name="name" required />
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-download"></i> <span>Save New Folder</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php

include('footer.php');
include('foot.php');

?>
