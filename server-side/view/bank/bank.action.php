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
	case 'save_object_id':
		//$c_person			 = $_REQUEST['c_person'];
		$comp_id				 = $_REQUEST['id'];
		$c_person				 = $_REQUEST['bank_object_id'];
		$phone					 = $_REQUEST['phone'];
		$user_id				 = $_SESSION['USERID'];
		if($comp_id=='') $comp_id=$_REQUEST['bank_local_id'];
		$trans_obj		= $_REQUEST['trans_obj'];
		$trans_address 		= $_REQUEST['trans_address'];
		if($c_person!='')
		{	mysql_query("	UPDATE		`bank_object`
							SET			`user_id`		=  '$user_id',
										`name`			= '$trans_obj',
										`address`		= '$trans_address'
							WHERE
										`id` 			= '$c_person'");
		}
		else{
			mysql_query("	INSERT INTO bank_object
									(`user_id`,bank_id, `name`, address )
							VALUES
									('$user_id',$comp_id,'$trans_obj', '$trans_address')	");
			$data = array('myid'	=> mysql_insert_id());

		}


		break;
	case 'get_edit_page':
		$local_id=$_REQUEST['local_id'];
		$c_person	= $_REQUEST['id'];
		$page		= GetPage(Getbankobject($c_person,$local_id));

		$data		= array('page'	=> $page);

		break;
	case 'get_list':
		$count		= $_REQUEST['count'];
		$hidden		= $_REQUEST['hidden'];
		$local_id	= $_REQUEST['local_id'];
		$comp_id		= $_REQUEST['id'];

		$rResult = mysql_query("SELECT 	id,
										`name`,
										`address`
								FROM    `bank_object`
								WHERE	 bank_id=$local_id AND  actived=1");

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
		case 'get_list':
			$count		= $_REQUEST['count'];
			$hidden		= $_REQUEST['hidden'];
			$object_id				= $_REQUEST['object_id'];
			$person_id				= $_REQUEST['person_id'];


			$rResult = mysql_query("SELECT
											`c_person`,
											`phone`,
											`email`
									FROM    `bank_person`
									WHERE	 c_person=$object_id AND  actived=1");

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

	case 'get_local_id':

		$increment_id	= GetLocalID();
		$data			= array('increment'	=> $increment_id);

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

function Addbank_object($bank_object_name, $bank_object_address)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO `bank_object`
						(`user_id`,`name`, `address`)
				VALUES
						($user_id,'$bank_object_name', '$bank_object_address'))");
}

function Deleteobj($prod_id)
{
	mysql_query("	UPDATE `bank_object`
					SET    `actived` = 0
					WHERE  `id` = $prod_id");
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

function Getbank_object($c_person)
{

	$res = mysql_fetch_assoc(mysql_query("SELECT 	`id`
											FROM 	`bank_object`
											WHERE 	`name` = '$c_person'"));
	return $res['id'];
	}

function Getbankobject($c_person, $local_id=0)
{

	$res = mysql_fetch_assoc(mysql_query("SELECT 	id,
													`name`,
													address
									 		FROM 	`bank_object`
									 		WHERE 	`id` =$c_person "));
	return $res;

}


function GetLocalID(){
	GLOBAL $db;
	return $db->increment('bank_object');
}

		function GetPage($res = '')
		{
			//echo $_REQUEST['act'];return 0;
			if ($_REQUEST['act1']=='c_person'){
    	$data = '<fieldset>
    	<legend>საკონტაქტო ინფორმაცია</legend>
    	<table class="dialog-form-table">
    		<tr>
    			<td style="width: 170px;"><label for="trans_obj">საკონტაქტო პირი</label></td>
    				<td>
    				<div class="seoy-row" id="obj_name_seoy">
    	<input type="text" id="c_person" class="idle" onblur="this.className=\'idle \'" onfocus="this.className=\'activeField \'" value="' . $res['name'] . '" />
    	</div>
    	</td>
    	</tr>
    	<tr>
    	<td style="width: 170px;"><label for="trans_address">ტელეფონი</label></td>
    	<td>
    	<input type="text" id="phone" class="idle " onblur="this.className=\'idle \'" onfocus="this.className=\'activeField \'" value="' . $res['address'] . '" />
    	</td>
    	</tr>
    	</table>

    	</fieldset>';
    	return $data;

    }else {
		$data = '
		<div id="dialog-form">
		<fieldset>
		<legend>ძირითადი ინფორმაცია</legend>
		<table class="dialog-form-table">
				<tr>
					<td style="width: 170px;"><label for="trans_obj">ფილიალი</label></td>
					<td>
						<div class="seoy-row" id="obj_name_seoy">
							<input type="text" id="trans_obj" class="idle" onblur="this.className=\'idle \'" onfocus="this.className=\'activeField \'" value="' . $res['name'] . '" />
						</div>
					</td>
				</tr>
						<tr>
					<td style="width: 170px;"><label for="trans_address">მისამართი</label></td>
					<td>
						<input type="text" id="trans_address" class="idle " onblur="this.className=\'idle \'" onfocus="this.className=\'activeField \'" value="' . $res['address'] . '" />
					</td>
				</tr>
			</table>


        </fieldset>
				<br/>

		<fieldset id="bank_object_field">
	    	<legend>საკონტაქტო პირები</legend>

		    <div class="inner-table">
			    <div id="dt_example" class="ex_highlight_row">
			        <div id="container" class="overhead_container">
			        	<div id="button_area">
			        		<button id="add_button_c_person">დამატება</button><button id="delete_button_c_person">წაშლა</button>
			        	</div>
			            <div id="dynamic">
			                <table class="display" id="c_perso_list">
			                    <thead>
			                        <tr id="datatable_header">
			                            <th>ID</th>
			                            <th style="width : 100%">სახელი</th>
			                            <th  style="width : 100%">მისამართი</th>
					 					<th  style="width : 100%">მისამართი</th>
			                            <th class="check">#</th>
			                        </tr>
			                    </thead>
			                    <thead>
			                        <tr class="search_header">
			                           	<th><input type="text" name="search_prod" value="ფილტრი" class="search_init" /></th>
			                            <th><input type="text" name="search_prod" value="ფილტრი" class="search_init" /></th>
			                            <th><input type="text" name="search_prod" value="ფილტრი" class="search_init" /></th>
										<th><input type="checkbox" name="check-all" id="check-all-prod" style="margin-left: 13px;" /></th>
			                        </tr>
			                    </thead>
			                </table>
			            </div>
			        </div>
			    </div>
			</div>
		</fieldset>
    </div>
	<!-- ID -->
	<input style="display: none;"  id="bank_object_id" value="' . $res['id'] . '" />';
   }


	return $data;
}

?>