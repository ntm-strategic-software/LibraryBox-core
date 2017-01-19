<?php

include("globals.php");

//phpinfo();
//die();

$table_rows = array();
$welcome_message;

if(loggedIn()) { // the user is logged in, so show user files

    $permissions = getUser()[0]['permissions'];

    $group_names = getGroupNames();

    $folders = array();
    for($i = 0; $i < count($permissions); $i++) {
        if($permissions[$i] > 0) {
            $name = $group_names[$i];
            if($name === (string)($i + 1)) {
                $name = "<span data-l10n-id='commonGroup'>Group</span> $name";
            } else if($name === 'public') {
                $name = "<span data-l10n-id='commonPublic'>Public</span>";
            }
            $folders[count($folders)] = array(
                'name' => $name,
                'path' => '/Shared/user-files/group-' . ($i + 1),
                'localizationKey' => '',
                'icon' => 'folder'
            );
        }
    }
    $folders[count($folders)] = array(
        'name' => 'public',
        'path' => '/Shared/public',
        'localizationKey' => 'commonPublic',
        'icon' => 'folder'
    );

    $folder_names = array();
    foreach($folders as $key => $row) {
        $folder_names[$key] = strtolower($row['name']);
    }
    array_multisort($folder_names, SORT_ASC, $folders);

    foreach($folders as $i => $folder) {
        $name = $folder['name'];
        $icon = $folder['icon'];
        $folder_path = $folder['path'];
        $folder_key = $folder['localizationKey'];
        $table_rows[count($table_rows)] = "
            <a href='$folder_path' class='list-group-item' style='font-size:17.5px;line-height:27px;'><span style='display:inline-block;min-width:20px;text-align:center;'><i class='fa fa-$icon' style='color:#f0cb26;'></i></span><span style='padding-left:15px;' data-l10n-id='$folder_key'>$name</span></a>
        ";
    }

    $welcome_message = '<p style="font-size: 16.8px;padding-bottom:15px;" data-l10n-id="homeUserMessage">Welcome back! To begin, please select a folder below.</p>';

} else { // the user is not logged in, so show public files

    $public_folders = array(
        array(
            'name' => 'apps',
            'localizationKey' => 'folderApps',
            'icon' => 'tablet'
        ),
        array(
            'name' => 'audio',
            'localizationKey' => 'folderAudio',
            'icon' => 'bullhorn'
        ),
        array(
            'name' => 'music',
            'localizationKey' => 'folderMusic',
            'icon' => 'music'
        ),
        array(
            'name' => 'pictures',
            'localizationKey' => 'folderPictures',
            'icon' => 'picture-o'
        ),
        array(
            'name' => 'text',
            'localizationKey' => 'folderText',
            'icon' => 'file-text-o'
        ),
        array(
            'name' => 'video',
            'localizationKey' => 'folderVideo',
            'icon' => 'video-camera'
        )
    );

    $folder_names = array();
    foreach($public_folders as $key => $row) {
        $folder_names[$key] = $row['name'];
    }
    array_multisort($folder_names, SORT_ASC, $public_folders);

    foreach($public_folders as $folder) {
        $name = $folder['name'];
        $icon = $folder['icon'];
        $localization_key = $folder['localizationKey'];
        $table_rows[count($table_rows)] = "
            <a href='/Shared/public/$name' class='list-group-item' style='font-size:17.5px;line-height:27px;'><span style='display:inline-block;min-width:20px;text-align:center;'><i class='fa fa-$icon'></i></span><span style='padding-left:15px;' data-l10n-id='$localization_key'>$name</span></a>
        ";
    }

    $welcome_message = '<p style="font-size: 16.8px;padding-bottom:15px;" data-l10n-id="homePublicMessage">Welcome to Scatterbox! To begin browsing all publicly available files, please select a category below.</p>';

}

include("head.php");
include("header.php");

?>

<div>
    <div class="container">
        <div class="lb-content" style="margin-top:0;">
            <div class="row">
                <div class="col-sm-12">
                    <?php echo $welcome_message ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="list-group">
                        <?php echo implode($table_rows) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include("footer.php");
include("foot.php");
?>