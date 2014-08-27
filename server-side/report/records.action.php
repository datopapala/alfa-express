<?php
mysql_close();
$conn = mysql_connect('212.72.155.176', 'root', 'Gl-1114');
if (!$conn) {
	$error = 'dgfhg';
}
mysql_select_db('asteriskcdrdb');

$action 	= $_REQUEST['act'];
//$error		= '';
$data		= '';

switch ($action) {
	case 'get_list' :
		$count = 		$_REQUEST['count'];
		$hidden = 		$_REQUEST['hidden'];
	  	$rResult = mysql_query("SELECT cdr.calldate,
									   cdr.calldate,
								       cdr.src,
								       cdr.dst,
								       CONCAT(SUBSTR((cdr.duration / 60), 1, 1), ':', cdr.duration % 60) as `time`,
								       CONCAT('<p onclick=play(', '\'', SUBSTRING(cdr.userfield, 7), '\'',  ')>მოსმენა</p>', '<a download=\"image.jpg\" href=\"http://212.72.155.176:8181/records/', SUBSTRING(cdr.userfield, 7), '\">ჩამოტვირთვა</a>')
								FROM   cdr
							WHERE      cdr.disposition = 'ANSWERED' AND cdr.userfield != '' AND cdr.dcontext = 'ALFA'");
	  
		$data = array(
				"aaData"	=> array()
		);

		while ( $aRow = mysql_fetch_array( $rResult ) )
		{
			$row = array();
			for ( $i = 0 ; $i < $count ; $i++ )
			{
				/* General output */
				$row[] = $aRow[$i];
			}
			$data['aaData'][] = $row;
		}

		break;
		
	default:
		$error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);

?>