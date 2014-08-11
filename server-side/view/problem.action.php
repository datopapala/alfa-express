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
		$problem_id		= $_REQUEST['id'];
		$page		= GetPage(Getproblem($problem_id));
		$data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
			
		$rResult = mysql_query("SELECT 	problem.id,
										problem.`name`
							    FROM 	problem
							    WHERE 	problem.actived=1");

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
	case 'save_problem':
		$problem_id 		= $_REQUEST['id'];
		$problem_name    = $_REQUEST['name'];



		if($problem_name != ''){
			if(!CheckproblemExist($problem_name, $problem_id)){
				if ($problem_id == '') {
					Addproblem( $problem_id, $problem_name);
				}else {
					Saveproblem($problem_id, $problem_name);
				}

			} else {
				$error = '"' . $problem_name . '" უკვე არის სიაში!';

			}
		}

		break;
	case 'disable':
		$problem_id	= $_REQUEST['id'];
		Disableproblem($problem_id);

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

function Addproblem($problem_id, $problem_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO 	 	`problem`
									(`user_id`,`name`)
					VALUES 		('$user_id','$problem_name')");
}

function Saveproblem($problem_id, $problem_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("	UPDATE `problem`
					SET     `user_id`='$user_id'
							`name` = '$problem_name'
					WHERE	`id` = $problem_id");
}

function Disableproblem($problem_id)
{
	mysql_query("	UPDATE `problem`
					SET    `actived` = 0
					WHERE  `id` = $problem_id");
}

function CheckproblemExist($problem_name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
											FROM   `problem`
											WHERE  `name` = '$problem_name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}


function Getproblem($problem_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT  `id`,
													`name`
											FROM    `problem`
											WHERE   `id` = $problem_id" ));

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
			<input type="hidden" id="problem_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
