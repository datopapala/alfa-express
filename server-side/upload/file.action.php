<?php
/* ******************************
 *	File Upload aJax actions
 * ******************************
 */

$action = $_REQUEST['act'];
$error	= '';
$data	= '';

switch ($action) {
	case 'upload_file':
		$element	= 'choose_file';
		$file_name	= $_REQUEST['file_name'];
		$type		= $_REQUEST['type'];
		$path		= $_REQUEST['path'];
		$path		= $path . $file_name . '.' . $type;
		
		if (! empty ( $_FILES [$element] ['error'] )) {
			switch ($_FILES [$element] ['error']) {
				case '1' :
					$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
					break;
				case '2' :
					$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
					break;
				case '3' :
					$error = 'The uploaded file was only partially uploaded';
					break;
				case '4' :
					$error = 'No file was uploaded.';
					break;
				case '6' :
					$error = 'Missing a temporary folder';
					break;
				case '7' :
					$error = 'Failed to write file to disk';
					break;
				case '8' :
					$error = 'File upload stopped by extension';
					break;
				case '999' :
				default :
					$error = 'No error code avaiable';
			}
		} elseif (empty ( $_FILES [$element] ['tmp_name'] ) || $_FILES [$element] ['tmp_name'] == 'none') {
			$error = 'No file was uploaded..';
		} else {
			if (file_exists($path)) {
				unlink($path);
			}
			move_uploaded_file ( $_FILES [$element] ['tmp_name'], $path);
		
			// for security reason, we force to remove all uploaded file
			@unlink ( $_FILES [$element] );
		}

		break;
		case 'upload_filee':
			$element	= 'choose_filee';
			$file_name	= $_REQUEST['file_name'];
			$type		= $_REQUEST['type'];
			$path		= $_REQUEST['path'];
			$path		= $path . $file_name . '.' . $type;
		
			if (! empty ( $_FILES [$element] ['error'] )) {
				switch ($_FILES [$element] ['error']) {
					case '1' :
						$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
						break;
					case '2' :
						$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
						break;
					case '3' :
						$error = 'The uploaded file was only partially uploaded';
						break;
					case '4' :
						$error = 'No file was uploaded.';
						break;
					case '6' :
						$error = 'Missing a temporary folder';
						break;
					case '7' :
						$error = 'Failed to write file to disk';
						break;
					case '8' :
						$error = 'File upload stopped by extension';
						break;
					case '999' :
					default :
						$error = 'No error code avaiable';
				}
			} elseif (empty ( $_FILES [$element] ['tmp_name'] ) || $_FILES [$element] ['tmp_name'] == 'none') {
				$error = 'No file was uploaded..';
			} else {
				if (file_exists($path)) {
					unlink($path);
				}
				move_uploaded_file ( $_FILES [$element] ['tmp_name'], $path);
		
				// for security reason, we force to remove all uploaded file
				@unlink ( $_FILES [$element] );
			}
		
			break;
    case 'delete_file':
		$file_name	= $_REQUEST['file_name'];
		$path		= $_REQUEST['path'];
		$path		= $path . $file_name;
		
		if (file_exists($path)) {
			unlink($path);
		}
		
        break;
    case 'get_file_list':
		$path		= $_REQUEST['path'];		
		$file_list	= directoryToArray($path, false);
		$data		= array('file_list' => json_encode($file_list));
		
        break;
    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	File Upload Functions
 * ******************************
 */

function directoryToArray($directory, $recursive) {
	$array_items = array();
	if ($handle = opendir($directory)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if (is_dir($directory. "/" . $file)) {
					if($recursive) {
						$array_items = array_merge($array_items, directoryToArray($directory. "/" . $file, $recursive));
					}
					$file = $file;
					$array_items[] = preg_replace("/\/\//si", "/", $file);
				} else {
					$file = $file;
					$array_items[] = preg_replace("/\/\//si", "/", $file);
				}
			}
		}
		closedir($handle);
	}
	return $array_items;
}

?>