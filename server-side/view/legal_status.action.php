<?php
require_once('../../includes/classes/core.php');
$action	= $_REQUEST['act'];
$error	= '';
$data	= '';

switch ($action) {
	case 'get_add_page':
		$page		= GetPage();
		$data		= array('page'	=> $page);

		break;
	case 'get_edit_page':
		$legal_status_id		= $_REQUEST['id'];
		$page		= GetPage(Getlegal_status($legal_status_id));
		$data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
			
		$rResult = mysql_query("SELECT 	legal_status.id,
										legal_status.`name`
							    FROM 	legal_status");

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
				if($i == ($count - 1)){
					$row[] = '<input type="checkbox" name="check_' . $aRow[$hidden] . '" class="check" value="' . $aRow[$hidden] . '" />';
				}
			}
			$data['aaData'][] = $row;
		}

		break;
	case 'save_legal_status':
		$legal_status_id 		= $_REQUEST['id'];
		$legal_status_name    = $_REQUEST['name'];



		if($legal_status_name != ''){
			if(!Checklegal_statusExist($legal_status_name, $legal_status_id)){
				if ($legal_status_id == '') {
					Addlegal_status( $legal_status_id, $legal_status_name);
				}else {
					Savelegal_status($legal_status_id, $legal_status_name);
				}

			} else {
				$error = '"' . $legal_status_name . '" უკვე არის სიაში!';

			}
		}

		break;
	case 'disable':
		$legal_status_id	= $_REQUEST['id'];
		Disablelegal_status($legal_status_id);

		break;
	default:
		$error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Category Functions
* ******************************
*/

function Addlegal_status($legal_status_id, $legal_status_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO 	 `legal_status`
								(`user_id`,`name`)
					VALUES 		('$user_id','$legal_status_name')");
}

function Savelegal_status($legal_status_id, $legal_status_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("	UPDATE `legal_status`
					SET    `user_id`='$user_id',
							 `name` = '$legal_status_name'
					WHERE	`id` = $legal_status_id");
}

function Disablelegal_status($legal_status_id)
{
	mysql_query("	UPDATE `legal_status`
					SET    `actived` = 0
					WHERE  `id` = $legal_status_id");
}

function Checklegal_statusExist($legal_status_name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
											FROM   `legal_status`
											WHERE  `name` = '$legal_status_name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}


function Getlegal_status($legal_status_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT  `id`,
													`name`
											FROM    `legal_status`
											WHERE   `id` = $legal_status_id" ));

	return $res;
}

function GetPage($res = '')
{
	$data = '
	<div id="dialog-form">
	    <fieldset>
	    	<legend>იურიდიული ინფორმაცია</legend>

	    	<table class="dialog-form-table">
				<tr>
					<td style="width: 170px;"><label for="CallType">სახელი</label></td>
					<td>
						<input type="text" id="name" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['name'] . '" />
					</td>
				</tr>

			</table>
			<!-- ID -->
			<input type="hidden" id="legal_status_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
