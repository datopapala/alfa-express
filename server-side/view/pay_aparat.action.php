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
		$payaparat_id		= $_REQUEST['id'];
		$page		= GetPage(Getpay_aparat($payaparat_id));
		$data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
			
		$rResult = mysql_query("SELECT 	pay_aparat.id,
										pay_aparat.`name`
							    FROM 	pay_aparat
							    WHERE 	pay_aparat.actived=1");

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
	case 'save_payaparat':
		$payaparat_id 		= $_REQUEST['id'];
		$payaparat_name    = $_REQUEST['name'];



		if($payaparat_name != ''){
			if(!Checkpay_aparatExist($payaparat_name, $payaparat_id)){
				if ($payaparat_id == '') {
					Addpay_aparat( $payaparat_id, $payaparat_name);
				}else {
					Savepay_aparat($payaparat_id, $payaparat_name);
				}

			} else {
				$error = '"' . $payaparat_name . '" უკვე არის სიაში!';

			}
		}

		break;
	case 'disable':
		$payaparat_id	= $_REQUEST['id'];
		Disablepay_aparat($payaparat_id);

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

function Addpay_aparat($payaparat_id, $payaparat_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO 	 	`pay_aparat`
									(`user_id`,`name`)
					VALUES 		('$user_id','$payaparat_name')");
}

function Savepay_aparat($payaparat_id, $payaparat_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("	UPDATE `pay_aparat`
					SET     `user_id`='$user_id',
							`name` = '$payaparat_name'
					WHERE	`id` = $payaparat_id");
}

function Disablepay_aparat($payaparat_id)
{
	mysql_query("	UPDATE `pay_aparat`
					SET    `actived` = 0
					WHERE  `id` = $payaparat_id");
}

function Checkpay_aparatExist($payaparat_name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
											FROM   `pay_aparat`
											WHERE  `name` = '$payaparat_name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}


function Getpay_aparat($payaparat_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT  `id`,
													`name`
											FROM    `pay_aparat`
											WHERE   `id` = $payaparat_id" ));

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
			<input type="hidden" id="payaparat_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
