<?php

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

if($requestMethod === 'POST') {
    $path = trim($path);
    // echo '<br><br><br>' . '$path is ' . $path . '<br>';
    $path_arr = explode('/', $path);
    $base_path = '/mnt/usb/LibraryBox/';
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
    if(!isset($_POST['name'])) {
        redirect('/');
        die();
    } elseif(strlen(trim($_POST['name'])) === 0) {
        $status = 1;
        $err_message = 'You must enter a folder name.';
    } else {
        $new_folder_name = trim($_POST['name']);
		$new_folder_name = preg_replace('/\s/', '_', $new_folder_name);
		$new_folder_name = preg_replace('/\W/', '', $new_folder_name);
        $new_folder_path = $full_path . $new_folder_name;
        if(file_exists($new_folder_path)) {
            $status = 1;
            $err_message = 'That folder name already exists.';
        } else {
            mkdir($new_folder_path);
            redirect('/' . $path);
        }
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
          <form action="/content/new-folder.php?p=<?php echo rawurlencode($path) ?>" method="post">
            <div class="form-group">
              <label>New Folder Name</label>
              <input type="text" class="form-control" name="name" required autofocus />
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