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
		$priority_id		= $_REQUEST['id'];
		$page		= GetPage(Getpriority($priority_id));
		$data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
			
		$rResult = mysql_query("SELECT 	priority.id,
										priority.`name`
							    FROM 	priority
							    WHERE 	priority.actived=1");

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
	case 'save_priority':
		$priority_id 	= $_REQUEST['id'];
		$priority_name  = $_REQUEST['name'];
			
		if($priority_name != ''){
			if(!CheckpriorityExist($priority_name, $priority_id)){
				if ($priority_id == '') {
					//$error = 'ghxtfjhu';
					Addpriority( $priority_id, $priority_name);
				}else {
					Savepriority($priority_id, $priority_name);
				}				
			}else{
				$error = '"' . $priority_name . '" უკვე არის სიაში!';	
			}
		}
		

		break;
	case 'disable':
		$priority_id	= $_REQUEST['id'];
		Disablepriority($priority_id);

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

function Addpriority($priority_id, $priority_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO 	 	`priority`
									(`user_id`,`name`)
					VALUES 		('$user_id','$priority_name')");
}

function Savepriority($priority_id, $priority_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("	UPDATE `priority`
					SET     `user_id`='$user_id',
							`name` = '$priority_name'
					WHERE	`id` = $priority_id");
}

function Disablepriority($priority_id)
{
	mysql_query("	UPDATE `priority`
					SET    `actived` = 0
					WHERE  `id` = $priority_id");
}

function CheckpriorityExist($priority_name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
											FROM   `priority`
											WHERE  `name` = '$priority_name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}


function Getpriority($priority_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT  `id`,
													`name`
											FROM    `priority`
											WHERE   `id` = $priority_id" ));

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
			<input type="hidden" id="priority_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
