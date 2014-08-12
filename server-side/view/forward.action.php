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
		$forward_id		= $_REQUEST['id'];
	       $page		= GetPage(Getforward($forward_id));
           $data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
		 
		$rResult = mysql_query("SELECT 	forward.id,
										forward.`name`
							    FROM 	forward
							    WHERE 	forward.actived=1");

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
	case 'save_forward':
		$forward_id 		= $_REQUEST['id'];
		$forward_name    = $_REQUEST['name'];
		
	
		
		if($forward_name != ''){
			if(!CheckforwardExist($forward_name, $forward_id)){
				if ($forward_id == '') {
					Adddeforward( $forward_id, $forward_name);
				}else {
					Savedeforward($forward_id, $forward_name);
				}
								
			} else {
				$error = '"' . $forward_name . '" უკვე არის სიაში!';
				
			}
		}
		
		break;
	case 'disable':
		$forward_id	= $_REQUEST['id'];
		Disableforward($forward_id);

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

function Adddeforward($forward_id, $forward_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO 	 `forward`
								(`name`,`user_id`)
					VALUES 		('$forward_name', '$user_id')");
}

function Savedeforward($forward_id, $forward_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("	UPDATE `forward`
					SET     `name` = '$forward_name',
							`user_id` ='$user_id'
					WHERE	`id` = $forward_id");
}

function Disableforward($forward_id)
{
	mysql_query("	UPDATE `forward`
					SET    `actived` = 0
					WHERE  `id` = $forward_id");
}

function CheckforwardExist($forward_name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
											FROM   `forward`
											WHERE  `name` = '$forward_name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}


function Getforward($forward_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT  `id`,
													`name`
											FROM    `forward`
											WHERE   `id` = $forward_id" ));

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
			<input type="hidden" id="forward_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
