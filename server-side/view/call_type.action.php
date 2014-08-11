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
		$call_id		= $_REQUEST['id'];
		$page		= GetPage(GetcallType($call_id));

		$data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
	  
		$rResult = mysql_query("SELECT 	call_type.id,
										call_type.`name`
							    FROM 	call_type
							    WHERE 	call_type.actived=1");
								  
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
	case 'save_call_type':
		$call_id 		= $_REQUEST['id'];
		$call_name		= $_REQUEST['name'];

		if($call_name != ''){
			if(!CheckcallTypeExist($call_name, $call_id)){
				if ($call_id == '') {
					AddcallType( $call_id, $call_name);
				}else {
					savecallType($call_id, $call_name);
				}				
			} else {
				$error = '"' . $call_name . '" უკვე არის სიაში!';
			}
		}

		break;
	case 'disable':
		$call_id	= $_REQUEST['id'];
		DisableCallType($call_id);

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

function AddCallType($call_id, $call_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO 	`call_type`
								(`user_id`,`name`)
					VALUES 		('$user_id','$call_name')");
}

function SaveCallType($call_id, $call_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("UPDATE `call_type`
				 SET    `user_id`='$user_id',
				 		`name` = '$call_name'
				 WHERE	`id` = $call_id");
}

function DisableCallType($call_id)
{
	mysql_query("	UPDATE `call_type`
					SET    `actived` = 0
					WHERE	`id` = $call_id");
}

function CheckCallTypeExist($call_name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
											FROM   `call_type`
											WHERE  `name` = '$call_name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}


function GetCallType($call_id)
{
	$res = mysql_fetch_assoc(mysql_query("SELECT `id`,
												`name`
										FROM   `call_type`
										WHERE  `id` = $call_id" ));

	return $res;
}

function GetPage($res = '')
{
	$data = '
	<div id="dialog-form">
	    <fieldset>
	    	<legend>ძირითადი ინფორმაცია</legend>

	    	<table class="dialog-form-table">
				<tr>
					<td style="width: 170px;"><label for="CallType">სახელი</label></td>
					<td>
						<input type="text" id="name" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['name'] . '" />
					</td>
				</tr>
				
			</table>
			<!-- ID -->
			<input type="hidden" id="call_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
