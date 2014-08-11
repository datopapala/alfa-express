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
		$callstatus_id		= $_REQUEST['id'];
		$page		= GetPage(Getcall_status($callstatus_id));
		$data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
			
		$rResult = mysql_query("SELECT 	call_status.id,
										call_status.`name`
							    FROM 	call_status
							    WHERE 	call_status.actived=1");

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
	case 'save_callstatus':
		$callstatus_id 		= $_REQUEST['id'];
		$callstatus_name    = $_REQUEST['name'];



		if($callstatus_name != ''){
			if(!Checkcall_statusExist($callstatus_name, $callstatus_id)){
				if ($callstatus_id == '') {
					Addcall_status( $callstatus_id, $callstatus_name);
				}else {
					Savecall_status($callstatus_id, $callstatus_name);
				}

			} else {
				$error = '"' . $callstatus_name . '" უკვე არის სიაში!';

			}
		}

		break;
	case 'disable':
		$callstatus_id	= $_REQUEST['id'];
		Disablecall_status($callstatus_id);

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

function Addcall_status($callstatus_id, $callstatus_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO 	 	`call_status`
	                                    (`name`,`user_id`)
				VALUES 		('$callstatus_name','$user_id')");
}

function Savecall_status($callstatus_id, $callstatus_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("	UPDATE `call_status`
					SET     `name` = '$callstatus_name',
							`user_id`=$user_id
					WHERE	`id` = $callstatus_id");
}

function Disablecall_status($callstatus_id)
{
	mysql_query("	UPDATE `call_status`
					SET    `actived` = 0
					WHERE  `id` = $callstatus_id");
}

function Checkcall_statusExist($callstatus_name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
											FROM   `call_status`
											WHERE  `name` = '$callstatus_name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}


function Getcall_status($callstatus_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT  `id`,
			                                        `name`
											FROM    `call_status`
											WHERE   `id` = $callstatus_id" ));

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
			<input type="hidden" id="callstatus_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
