<?php
require_once('../../includes/classes/core.php');
session_start();
$action		= $_REQUEST['act'];
$error		= '';
$data		= '';
$user_id	= $_SESSION['USERID'];
switch ($action) {
	case 'get_add_page':
		$page		= GetPage();
		$data		= array('page'	=> $page);

		break;
	case 'get_edit_page':
		$task_id		= $_REQUEST['id'];
		$page		= GetPage(Gettask_type($task_id));
		$data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
			
		$rResult = mysql_query("SELECT 	task_type.id,
										task_type.`name`
							    FROM 	task_type
							    WHERE 	task_type.actived=1");

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
	case 'save_task':
		$task_id 		= $_REQUEST['id'];
		$task_name    = $_REQUEST['name'];



		if($task_name != ''){
			if(!Checktask_typeExist($task_name, $task_id)){
				if ($task_id == '') {
					Addtask_type( $task_id, $task_name);
				}else {
					Savetask_type($task_id, $task_name);
				}

			} else {
				$error = '"' . $task_name . '" უკვე არის სიაში!';

			}
		}

		break;
	case 'disable':
		$task_id	= $_REQUEST['id'];
		Disabletask_type($task_id);

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

function Addtask_type($task_id, $task_name, $user_id)
{	
	
	$a =  $_SESSION['USERID'];
mysql_query("INSERT INTO 	`task_type`
						(`user_id`,`name`)
			VALUES 		( $a,'$task_name')");
}

function Savetask_type($task_id, $task_name, $user_id)
{
mysql_query("	UPDATE `task_type`
					SET     `name` = '$task_name',
							`user_id`='$user_id'
					WHERE	`id` = $task_id");
}

function Disabletask_type($task_id)
{
	mysql_query("	UPDATE `task_type`
					SET    `actived` = 0
					WHERE  `id` = $task_id");
}

function Checktask_typeExist($task_name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
											FROM   `task_type`
											WHERE  `name` = '$task_name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}


function Gettask_type($task_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT  `id`,
													`name`
											FROM    `task_type`
											WHERE   `id` = $task_id" ));

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
			<input type="hidden" id="task_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
