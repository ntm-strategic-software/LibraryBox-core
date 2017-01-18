<?php

$VERSION = '1.2';

/*  Lighttpd Enhanced Directory Listing Script
 *  ------------------------------------------
 *  Author: Evan Fosmark
 *  Version: 2008.08.07
 *
 *  Since 1.0 ; Matthias Strubel
 *          Modifications for including a download-count.
 *  Since 1.1 ; Jason Griffey
 *			Modifications for responsive design
 *  Since 1.2 ; Matthias Strubel
 *          Modifications for multi character type support.
 *
 *  GNU License Agreement
 *  ---------------------
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *  http://www.gnu.org/licenses/gpl.txt
 */

/*  Revision by KittyKatt
 *  ---------------------
 *  E-Mail:  kittykatt@archlinux.us
 *  Website: http://www.archerseven.com/kittykatt/
 *  Version:  2010.03.01
 *
 *  Revised original code to include hiding for directories prefixed with a "." (or hidden
 *  directories) as the script was only hiding files prefixed with a "." before. Also included more
 *  file extensions/definitions.
 *
 */

include('/mnt/usb/LibraryBox/Content/globals.php');
$user;
if(loggedIn()) {
	$user = getUser()[0];
} else {
	$user = array();
}

$group_names = getGroupNames();
//print $group_names[0];

function getGroupName($str) {
	$group_names = getGroupNames();
	for($i = 0; $i < count($group_names); $i++) {
		if($str === "group-" . ($i + 1)) {
			return $group_names[$i];
		}
	}
	return $str;
}
// print getGroupName('group-3');

$show_hidden_files = true;
$calculate_folder_size = false;
$display_header = true;
$display_readme = true;
$hide_header = true;
$hide_readme = true;


# Enable a specific dl-file prefix which directs to the counter script
$collect_dl_count = true;
# Display the countend downloads to the overview (reads out of an SQLitedb)
$display_dl_count = true;
$dl_stat_func_file = "dl_statistics.func.php";


$folder_statistics = array();

// Various file type associations
$movie_types = array('mpg','mpeg','avi','asf','mp4','aif','aiff','ram', 'asf','au');
$image_types = array('jpg','jpeg','gif','png','tif','tiff','bmp','ico','svg');
$archive_types = array('zip','cab','7z','gz','tar.bz2','tar.gz','tar','rar',);
$document_types = array('txt','text','abw','rtf','tex','texinfo','odf');
$word_document_types = array('doc','docx','odt');
$spreadsheet_types = array('xls','xlsx','ods');
$drawing_types = array('pub','odg');
$presentation_types = array('ppt','pptx','pps','ppsx','odp');
$pdf_types = array('pdf');
$font_types = array('ttf','otf','abf','afm','bdf','bmf','fnt','fon','mgf','pcf','ttc','tfm','snf','sfd');
$audio_types = array('mp3','ogg','aac','wma','wav','midi','mid','flac');


// Get the path (cut out the query string from the request_uri)
list($path) = explode('?', $_SERVER['REQUEST_URI']);


// Get the path that we're supposed to show.
$path = ltrim(rawurldecode($path), '/');


if(strlen($path) == 0) {
	$path = "./";
}


// Can't call the script directly since REQUEST_URI won't be a directory
if($_SERVER['PHP_SELF'] == '/'.$path) {
	die("Unable to call " . $path . " directly.");
}


// Make sure it is valid.
if(!is_dir($path)) {
	die("<b>" . $path . "</b> is not a valid path.");
}

//Load UTF8 Helper stuff
require_once "uft8-help.func.php";


//
// Get the size in bytes of a folder
//
function foldersize($path) {
	$size = 0;
	if($handle = @opendir($path)){
		while(($file = readdir($handle)) !== false) {
			if(is_file($path."/".$file)){
				$size += filesize($path."/".$file);
			}

			if(is_dir($path."/".$file)){
				if($file != "." && $file != "..") {
					$size += foldersize($path."/".$file);
				}
			}
		}
	}

	return $size;
}


//
// This function returns the file size of a specified $file.
//
function format_bytes($size, $precision=0) {
    $sizes = array('YB', 'ZB', 'EB', 'PB', 'TB', 'GB', 'MB', 'KB', 'B');
    $total = count($sizes);

    while($total-- && $size > 1024) $size /= 1024;
    return sprintf('%.'.$precision.'f', $size).$sizes[$total];
}


//
// This function returns the mime type of $file.
//
function get_file_type($file) {
	global $image_types, $movie_types, $archive_types, $document_types, $font_types , $audio_types;

	$pos = strrpos($file, ".");
	if ($pos === false) {
		return "Unknown File";
	}

	$ext = rtrim(substr($file, $pos+1), "~");
	if(in_array($ext, $image_types)) {
		$type = "Image File";

	} elseif(in_array($ext, $movie_types)) {
		$type = "Video File";

	} elseif(in_array($ext, $archive_types)) {
		$type = "Compressed Archive";

	} elseif(in_array($ext, $document_types)) {
		$type = "Type Document";

	} elseif(in_array($ext, $font_types)) {
		$type = "Type Font";

	} elseif(in_array($ext, $audio_types)) {
		$type = "Audio File";

	} else {
		$type = "File";
	}

	return(strtoupper($ext) . " " . $type);
}

# returns a small ID which can be used on CSS for pictures
function get_file_type_id($file) {
	global $image_types, $movie_types, $archive_types, $document_types, $word_document_types, $spreadsheet_types, $drawing_types, $presentation_types, $pdf_types, $font_types, $audio_types;

	$pos = strrpos($file, ".");
	if ($pos === false) {
		return "file";
	}

	$ext = rtrim(substr($file, $pos+1), "~");
	$ext = strtolower($ext);
	if(in_array($ext, $image_types)) {
		// $type = "img";
		$type = "file-image-o";
	} elseif(in_array($ext, $movie_types)) {
		// $type = "video";
		$type = "file-video-o";
	} elseif(in_array($ext, $audio_types)) {
		// $type = "audio";
		$type = "file-audio-o";
	} elseif(in_array($ext, $archive_types)) {
		// $type = "archive";
		$type = "file-archive-o";
	} elseif(in_array($ext, $document_types)) {
		// $type = "doc";
		$type = "file-text-o";
	} elseif(in_array($ext, $word_document_types)) {
		// $type = "doc";
		$type = "file-word-o";
	} elseif(in_array($ext, $spreadsheet_types)) {
		// $type = "doc";
		$type = "file-excel-o";
	} elseif(in_array($ext, $drawing_types)) {
		// $type = "doc";
		$type = "file-text-o";
	} elseif(in_array($ext, $presentation_types)) {
		// $type = "doc";
		$type = "file-powerpoint-o";
	} elseif(in_array($ext, $pdf_types)) {
		// $type = "doc";
		$type = "file-pdf-o";
	} elseif(in_array($ext, $font_types)) {
			// $type = "font";
			$type = "file-archive-o";
	} else {
		// $type = "file";
		$type = "file-o";
	}

	return($type);
}


function get_download_count ($filename  ) {
	global $path;
	global $folder_statistics;

	$full_filename = "/$path".$filename ;

	if ( isset ( $folder_statistics[ $full_filename ] ) ) {
		return  $folder_statistics[ $full_filename ][ 'counter'] ;
	} else { 
		return 0;
	}
}

function get_folder_statistics ($my_path, &$folder_statistics) {
	global $dl_stat_func_file;
	include $dl_stat_func_file ;

	$result = dl_read_stat_per_path_only ( "$my_path"  );

	foreach ( $result as $line ) {
		$folder_statistics [ $line [ 'url' ] ] = array  (
			'url' 		=>  $line [ 'url' ] ,
			'counter' 	=>  $line [ 'counter' ] ,
			);
	}
}

// Print the heading stuff
$vpath = ($path != "./")?$path:"";
print '<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Index of /'.$vpath.'</title>
		<link href="/content/css/bootstrap.css" rel="stylesheet">
		<link rel="stylesheet" href="/content/css/font-awesome.css">
		<link rel="stylesheet" href="/content/css/dir_list.css">

		  	<link rel="prefetch" type="application/l10n" href="/content/locales/locales.ini" />
  			<script type="text/javascript" src="/content/js/l10n.js"></script>
	</head>
	<body>
';

if ( $display_dl_count ) {
	get_folder_statistics ( "/".$vpath , $folder_statistics );	
}	

if ($display_header)
{
	if (is_file($path.'/HEADER'))
	{
		print "<pre>";
		print(nl2br(file_get_contents($path.'/HEADER')));
		print "</pre>";
	}

	if (is_file($path.'/HEADER.html'))
	{
		readfile($path.'/HEADER.html');
	}
}

$split_vpath = explode("/", $vpath);
if($split_vpath[1] === 'public') {
	$split_vpath[1] = "<span data-l10n-id='commonPublic'>public</span>";
	if(isset($split_vpath[2])) {
		$inner_folder_name = ucfirst($split_vpath[2]);
		$split_vpath[2] = "<span data-l10n-id='folder$inner_folder_name'></span>";
	}
} else if(isset($split_vpath[2])) {
	$split_vpath[1] = "<span data-l10n-id='folderUserFiles'>user-files</span>";
	$split_vpath[2] = getGroupName($split_vpath[2]);
}
$split_vpath = array_slice($split_vpath, 1);
$vpath = implode($split_vpath, '/');
print "<h2><span data-l10n-id='filedirIndex'>Index of /</span>" . $vpath . "</h2>
	<div class='list'>
	<table>";

/*
function isTopLevel($p) {                                                                                                                             
        if(count(explode("/", $path)) === 2) {                                                                                                        
        	return true;                                                                                                                          
        } else {                                                                                                                                      
                return false;                                                                                                                         
        }                                                                                                                                             
}                                                                                                                                                     
                                                                                                                                                                                                              
$is_top_level = isTopLevel($path);
*/

// Get all of the folders and files.
$folderlist = array();
$filelist = array();
if($handle = @opendir($path)) {
	while(($item = readdir($handle)) !== false) {
		if(is_dir($path.'/'.$item) and $item != '.' and $item != '..') {
			if( $show_hidden_files == "false" ) {
				if(substr($item, 0, 1) == "." or substr($item, -1) == "~") {
				  continue;
				}
			}
			$folderlist[] = array(
				'name' => $item,
				'size' => (($calculate_folder_size)?foldersize($path.'/'.$item):0),
				'modtime'=> filemtime($path.'/'.$item),
				'file_type' => "Directory"
			);
		}

		elseif(is_file($path.'/'.$item)) {
			if ($item === basename($_SERVER['SCRIPT_NAME']))
			{
				continue;
			}
			if ($hide_header)
			{
				if ($item === 'HEADER' || $item === 'HEADER.html')
				{
					continue;
				}
			}
			if ($hide_readme)
			{
				if ($item === 'README' || $item === 'README.html')
				{
					continue;
				}
			}
			if( $show_hidden_files == "false" ) {
				if(substr($item, 0, 1) == "." or substr($item, -1) == "~") {
				  continue;
				}
			}
			$filelist[] = array(
				'name'=> $item,
				'size'=> filesize($path.'/'.$item),
				'modtime'=> filemtime($path.'/'.$item),
				'file_type' => get_file_type($path.'/'.$item),
				'counter'   => get_download_count ($item), ## addslashes needed??
				'img_id'    => get_file_type_id($path.'/'.$item)
			);
		}
	}
	closedir($handle);
}


if(!isset($_GET['sort'])) {
	$_GET['sort'] = 'name';
}

// Figure out what to sort files by
$file_order_by = array();
foreach ($filelist as $key=>$row) {
    $file_order_by[$key]  = $row[$_GET['sort']];
}

// Figure out what to sort folders by
$folder_order_by = array();
foreach ($folderlist as $key=>$row) {
    $folder_order_by[$key]  = $row[$_GET['sort']];
}

// Order the files and folders
if(isset($_GET['order'])) {
	array_multisort($folder_order_by, SORT_DESC, $folderlist);
	array_multisort($file_order_by, SORT_DESC, $filelist);
} else {
	array_multisort($folder_order_by, SORT_ASC, $folderlist);
	array_multisort($file_order_by, SORT_ASC, $filelist);
	$order = "&amp;order=desc";
}


// Show sort methods
print "<thead><tr>";

$sort_methods = array();
$sort_methods['name'] = "<div data-l10n-id='filedirName'>Name</div>";
//$sort_methods['modtime'] = "Last Modified";
$sort_methods['size'] = "<div data-l10n-id='filedirSize' class='s hidden-sm hidden-xs'>Size</div>";
$sort_methods['file_type'] = "<div data-l10n-id='filedirType' class='t hidden-sm hidden-xs'>Type</div>";

if ( $display_dl_count ) {
	$sort_methods['counter'] = "<div data-l10n-id='filedirDownloads' class='c hidden-xs'>Downloads</div>";
}

foreach($sort_methods as $key=>$item) {
	if($_GET['sort'] == $key) {
		print "<th><a href='?sort=$key$order'>$item</a></th>";
	} else {
		print "<th><a href='?sort=$key'>$item</a></th>";
	}
}
print "</tr></thead><tbody>";

function isTopLevel($op) {
	$p = explode('/', $op);
	$is_logged_in = loggedIn();
	if($p[1] === 'public') { // they are in the public directory
		if($is_logged_in && count($p) === 3) {
			return true;
		} else if(!$is_logged_in && count($p) === 4) {
			return true;
		} else {
			return false;
		}
	} else { // they are in the user-files directory
		if(count($p) === 4) {
			return true;
		} else {
			return false;
		}
	}
}

$is_top_level = isTopLevel($path);

function isAllowed($str) {
	//print 'checking ' . $str;
	$group_names = getGroupNames();
	$idx = -1;
	for($ii = 0; $ii < count($group_names); $ii++) {
		//print '$ii is ' . $ii;
		//print $group_names[$ii];
		if($group_names[$ii] === $str) {
			$idx = $ii;
			break;
		}
	}
	//print 'idx is ' . $idx . '<br>';
	if($idx === -1) {
		return true;
	} elseif(!loggedIn()) {
		return false;
	} else {
		$user = getUser()[0];
		return $user['permissions'][$idx] > 0 ? true : false;
	}
	//print 'down here' . '<br>';
}

// Parent directory link
if($path != "./") {
	$parent_dir_path = $is_top_level ? '/content' : '..';
	print "<tr><td><a href='$parent_dir_path'><span class='folder-icon'><i class='fa fa-folder'></i></span> <span data-l10n-id='filedirParDir'></span></a>/</td>";
	//print "<td class='m'> </td>";
	print "<td class='s hidden-sm hidden-xs'> </td>";
	print "<td class='t hidden-sm hidden-xs'>Directory</td></tr>\n";
}

// print $path . '<br>';
// print count(explode("/", $path));

if(count(explode('/', $path)) > 2) {
	$path_arr = explode("/", $path);
	//print($path_arr[1]);
	if(!isAllowed(getGroupName($path_arr[2]))) {
		print 'not allowed';
		redirect('/');
		die();
	}
}

$in_public = false;
if(count(explode('/', $path)) > 1) {
	$path_arr = explode("/", $path);
	if($path_arr[1] === 'public') {
		$in_public = true;
	}
}

// Print folder information
foreach($folderlist as $folder) {
	$utf_name = get_utf8_encoded($folder['name']);
	$name;
	if(loggedIn() && $in_public && $is_top_level) {
		$inner_folder_name = ucfirst($utf_name);
		$name = "<span data-l10n-id='folder$inner_folder_name'>$utf_name</span>";
	} else {
		$name = $is_top_level ? getGroupName($utf_name) : $utf_name;
	}
	if((!$is_top_level) || ($is_top_level && isAllowed($name))) {
		print "<tr><td style='padding-left:30px;'><a href='" . rawurlencode( $folder['name'] ) . "'><span class='folder-icon'><i class='fa fa-folder'></i></span> " . $name . "</a>/</td>";
		//print "<td class='m'>" . date('Y-M-d H:i:s', $folder['modtime']) . "</td>";
		print "<td class='s hidden-sm hidden-xs'>" . (($calculate_folder_size)?format_bytes($folder['size'], 2):'--') . " </td>";
		print "<td class='t hidden-sm hidden-xs'>" . $folder['file_type']                    . "</td></tr>\n";
	}
}



// This simply creates an extra line for file/folder seperation
// print "<tr><td colspan='4' style='height:7px;background-color:#fff;'></td></tr>\n";



// Print file information
foreach($filelist as $file) {

	global $collect_dl_count;

	$file_link_prefix="";

	if ( $collect_dl_count ) {
		$file_link_prefix="/dl_statistics_counter.php?DL_URL=/" . rawurlencode($path);
	}

	$icon_name = $file['img_id'];
	print "<tr><td style='padding-left:30px;'><a href='$file_link_prefix" . rawurlencode($file['name']). "'><span class='file-icon'><i class='fa fa-$icon_name'></i></span> " .get_utf8_encoded($file['name']). "</a></td>";
	// print "<td class='m'>" . date('Y-M-d H:i:s', $file['modtime'])   . "</td>";
	print "<td class='s hidden-sm hidden-xs'>" . format_bytes($file['size'],2)           . " </td>";
	print "<td class='t hidden-sm hidden-xs'>" . $file['file_type']                      . "</td>";
	if ( $display_dl_count ) {
		print "<td class='c hidden-sm hidden-xs'>" . $file['counter'] . "</td>";
	}
	print "</tr>\n";
}



// Print ending stuff
print "</tbody>
	</table>
	</div>";

if ($display_readme)
{
	if (is_file($path.'/README'))
	{
		print "<pre>";
		print(nl2br(file_get_contents($path.'/README')));
		print "</pre>";
	}

	if (is_file($path.'/README.html'))
	{
		readfile($path.'/README.html');
	}
}
?>

<div class="upload-outer-container">
    <div class="new-file-folder-outer-container">
        <div class="new-file-folder-container" id="js-newFileFolderButtonContainer" style="display:none;opacity:0;">
            <a href="/content/new-file.php?p=<?php print rawurlencode($path) ?>">
                <div id="js-newFileButton" class="round-upload-button new-file-button">
                    <i class="fa fa-file-text"></i>
                </div>
            </a>
            <a href="/content/new-folder.php?p=<?php echo rawurlencode($path) ?>">
                <div id="js-newFolderButton" class="round-upload-button new-folder-button">
                    <i class="fa fa-folder"></i>
                </div>
            </a>
        </div>
    </div>
    <a href="#" id="js-showUploadOptions">
        <div class="round-upload-button show-upload-buttons">
            <i class="fa fa-plus"></i>
        </div>
    </a>
</div>

<?php
print "<div class='foot'>Scatterbox </div>
	</body>
	</html>";
?>
