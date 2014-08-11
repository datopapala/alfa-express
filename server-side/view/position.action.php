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
		$position_id		= $_REQUEST['id'];
		$page		= GetPage(Getposition($position_id));
		$data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
			
		$rResult = mysql_query("SELECT 	    position.id,
										    position.`person_position`
							    FROM 		position
							    WHERE 		position.actived=1");

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
	case 'save_position':
		$position_id 		= $_REQUEST['id'];
		$position_name    = $_REQUEST['name'];



		if($position_name != ''){
			if(!CheckpositionExist($position_name, $position_id)){
				if ($position_id == '') {
					Addposition( $position_id, $position_name);
				}else {
					Saveposition($position_id, $position_name);
				}

			} else {
				$error = '"' . $position_name . '" უკვე არის სიაში!';

			}
		}

		break;
	case 'disable':
		$position_id	= $_REQUEST['id'];
		Disableposition($position_id);

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

function Addposition($position_id, $position_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO 	 `position`
								( `user_id`, `person_position`,actived)
						VALUES 
								( '$user_id','$position_name',  '1')");
}

function Saveposition($position_id, $position_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("	UPDATE `position`
					SET     `person_position` = '$position_name',
							`user_id` ='$user_id'
					WHERE	`id` = $position_id");
}

function Disableposition($position_id)
{
	mysql_query("	UPDATE `position`
					SET    `actived` = 0
					WHERE  `id` = $position_id");
}

function CheckpositionExist($position_name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
											FROM   `position`
											WHERE  `person_position` = '$position_name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}


function Getposition($position_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT  	`id`,
														`person_position`
											 FROM   	 `position`
											 WHERE   	`id` = $position_id" ));

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
						<input type="text" id="person_position" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['person_position'] . '" />
					</td>
				</tr>

			</table>
			<!-- ID -->
			<input type="hidden" id="position_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
