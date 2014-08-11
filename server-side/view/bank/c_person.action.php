<?php

/* ******************************
 *	Recipie obj List aJax actions
* ******************************
*/
include('../../../includes/classes/core.php');
$action	= $_REQUEST['act'];
$error	= '';
$data	= '';
switch ($action) {
	case 'get_add_page':
		$page		= GetPage();
		$data		= array('page'	=> $page);
		break;

	case 'get_edit_page':
		$c_person	    = $_REQUEST['id'];
		$page			= GetPage(Getbankobject($c_person,$local_id));
		$data			= array('page'	=> $page);

		break;

	case 'get_list':
		$count			= $_REQUEST['count'];
		$hidden			= $_REQUEST['hidden'];
		$object_id		= $_REQUEST['local_id'];

		$rResult 		= mysql_query("SELECT 	`id`,
												`c_person`,
												`phone`,
												`email`
										FROM    `bank_person`
										WHERE	 bank_id=$object_id AND  actived=1");

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

	case 'save_c_person':
		$person_id				= $_REQUEST['person_id'];
		$object_id				= $_REQUEST['object_id'];
		$c_person				= $_REQUEST['c_person'];
		$phone					= $_REQUEST['phone'];
		$mail					= $_REQUEST['mail'];
		$user_id	= $_SESSION['USERID'];
		if($person_id!=''){
			mysql_query("	UPDATE		`bank_person`
							SET			`user_id`			= '$user_id',
										`c_person` 			= '$c_person',
										`bank_id`			=  $object_id,
										`phone`				= '$phone',
										`email`				= '$mail'
							WHERE		`id`   				= $person_id");
		}else{
			mysql_query("	INSERT INTO bank_person
								(`user_id`,bank_id, `c_person`, `phone`, `email`, `actived` )
							VALUES
								($user_id,$object_id,'$c_person', '$phone', '$mail', 1)");
		}

			break;

	case 'disable':

		Deleteobj($_REQUEST['id']);

		break;
	default:
		$error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Recipie obj List Functions
* ******************************
*/

function Addbank_object($user_id, $bank_object_name, $bank_object_address)
{
	mysql_query("INSERT INTO `bank_object`
						(`user_id`,`name`, `address`)
				VALUES
						($user_id,$bank_object_name, $bank_object_address)");
}

function Deleteobj($person_id)
{
	mysql_query("	UPDATE `bank_person`
					SET    `actived` = 0
					WHERE  `id` = $person_id");
}

function CheckobjExist($local_id, $prod_id)
{
$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
										FROM `transfer_detail`
										WHERE `transfer_id` = $local_id && `object_id` = $prod_id"));
		if($res['id'] != ''){
				return true;
	}
	return false;
}

function Getbank_object($c_person){
	$res = mysql_fetch_assoc(mysql_query("SELECT 	`id`
											FROM 	`bank_object`
											WHERE 	`name` = '$c_person'"));
	return $res['id'];
}

function Getbankobject($c_person){
	//echo $_REQUEST['id']."-$c_person-$local_id";
	//return 0;
	$res = mysql_fetch_assoc(mysql_query("	SELECT 	`id`,
													`c_person`,
													`phone`,
													`email`
											FROM	`bank_person`
											WHERE 	`id`=$c_person"));
	return $res;
}

function GetPage($res = ''){
	//echo $res["phone"];
	//return 0;
		$data = '
		<div id="dialog-form">
		<fieldset>
    		<legend>საკონტაქტო ინფორმაცია</legend>
    		<table class="dialog-form-table">
    			<tr>
    				<td style="width: 170px;"><label for="trans_obj">საკონტაქტო პირი</label></td>
    				<td><input type="text" id="c_person" class="idle" onblur="this.className=\'idle \'" onfocus="this.className=\'activeField \'" value="' . $res['c_person'] . '" /></td>
    			</tr>
    			<tr>
    				<td style="width: 170px;"><label for="trans_address">ტელეფონი</label></td>
    				<td><input type="text" id="phone" class="idle " onblur="this.className=\'idle \'" onfocus="this.className=\'activeField \'" value="' . $res['phone'] . '" /></td>
    			</tr>
    			<tr>
    				<td style="width: 170px;"><label for="trans_obj">mail</label></td>
    				<td><input type="email" id="mail"  class="idle" onblur="this.className=\'idle \'" onfocus="this.className=\'activeField \'" value="' . $res['email'] . '"  /></td>
    			</tr>
    		</table>
    	</fieldset>
    	</div>
    <!-- ID -->
	<input style="display: none;"  id="bank_person_id" value="' . $res['id'] . '" />';

	return $data;
}

?>