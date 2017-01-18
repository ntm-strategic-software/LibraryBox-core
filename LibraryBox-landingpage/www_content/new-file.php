<?php

include('globals.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

$status = 0;

if($requestMethod === 'POST') {

    $query_arr = array();
    parse_str($_SERVER['QUERY_STRING'], $query_arr);
    if(!isset($query_arr['p'])) {
        $status = 1;
    }
    print $query_arr['p'];
    
}

include('head.php');
include('header.php');

?>

<div class="container">
    <div class="row">
        <div class="col-sm-7 col-md-6 col-lg-5">
            <form action="/content/new-file.php" method="post">
                <div class="form-group">
                    <label>File Upload</label>
                    <input type="file" class="form-control" required />
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> <span>Upload File</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php

include('footer.php');
include('foot.php');

?>
