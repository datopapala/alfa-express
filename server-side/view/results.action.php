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
		$results_id		= $_REQUEST['id'];
	       $page		= GetPage(Getresults($results_id));
           $data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
		 
		$rResult = mysql_query("SELECT 	results.id,
										results.`name`
							    FROM 	results
							    WHERE 	results.actived=1");

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
	case 'save_results':
		$results_id 		= $_REQUEST['id'];
		$results_name    = $_REQUEST['name'];
		
	
		
		if($results_name != ''){
			if(!CheckresultsExist($results_name, $results_id)){
				if ($results_id == '') {
					Addderesults( $results_id, $results_name);
				}else {
					Savederesults($results_id, $results_name);
				}
								
			} else {
				$error = '"' . $results_name . '" უკვე არის სიაში!';
				
			}
		}
		
		break;
	case 'disable':
		$results_id	= $_REQUEST['id'];
		Disableresults($results_id);

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

function Addderesults($results_id, $results_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO 	 `results`
								(`name`,`user_id`)
					VALUES 		('$results_name', '$user_id')");
}

function Savederesults($results_id, $results_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("	UPDATE `results`
					SET     `name` = '$results_name',
							`user_id` ='$user_id'
					WHERE	`id` = $results_id");
}

function Disableresults($results_id)
{
	mysql_query("	UPDATE `results`
					SET    `actived` = 0
					WHERE  `id` = $results_id");
}

function CheckresultsExist($results_name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
											FROM   `results`
											WHERE  `name` = '$results_name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}


function Getresults($results_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT  `id`,
													`name`
											FROM    `results`
											WHERE   `id` = $results_id" ));

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
			<input type="hidden" id="results_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
