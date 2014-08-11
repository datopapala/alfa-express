<?php
require_once('../../includes/classes/core.php');
$action	= $_REQUEST['act'];
$error	= '';
$data	= '';
$bank_id 		= $_REQUEST['id'];
$bank_name      = $_REQUEST['name'];
$bank_phone      = $_REQUEST['bank_phone'];
switch ($action) {
	case 'get_add_page':
		$page		= GetPage();
		$data		= array('page'	=> $page);

		break;
	case 'get_edit_page':
		$bank_id		= $_REQUEST['id'];
		$page		= GetPage(Getbank($bank_id));
		$data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];

		$rResult = mysql_query("SELECT 	bank.id,
										bank.`name`,
										bank.`bank_phone`
							    FROM 	bank
							    WHERE 	bank.actived=1");

		$data = array(
				"aaData"	=> array()
		);

		while ( $aRow = mysql_fetch_array( $rResult ) )
		{
			$row = array();
			for ( $i = 0 ; $i < $count ; $i++ )
			{
				$row[] = $aRow[$i];
				if($i == ($count - 1)){
					$row[] = '<input type="checkbox" name="check_' . $aRow[$hidden] . '" class="check" value="' . $aRow[$hidden] . '" />';
				}
			}
			$data['aaData'][] = $row;
		}

		break;
	case 'save_bank':

		if($bank_name != '' && $bank_phone!=''){
			if ($bank_id == '') {
				if(!CheckbankExist($bank_name)){
					Addbank($bank_name,$bank_phone);
					$data = array('myid'	=> mysql_insert_id());
				}else {
					$error = '"' . $bank_name . '" უკვე არის სიაში!';
				}
			}else {
				Savebank($bank_id, $bank_name,$bank_phone);
			}
		}else {
			$error = 'შეავსეთ ყველა ველი!';
		}

		break;
	case 'disable':

		$bank_id	= $_REQUEST['id'];
		Disablebank($bank_id);

		break;

	case 'get_local_id':

		$increment_id	= GetLocalID();
		$data			= array('increment'	=> $increment_id);

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

function Addbank($bank_name, $bank_phone)
{
	$user_id	= $_SESSION['USERID'];
	global $bank_phone;
	mysql_query("INSERT INTO 	`bank`
									(`user_id`,`name`,`bank_phone`)
					VALUES 		('$user_id','$bank_name', '$bank_phone')");
}

function Savebank($bank_id, $bank_name, $bank_phone)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("	UPDATE `bank`
					SET    `user_id`= '$user_id',
							`name` = '$bank_name',
							`bank_phone`='$bank_phone'
					WHERE	`id` = $bank_id");
}

function Disablebank($bank_id)
{
	mysql_query("	UPDATE `bank`
					SET    `actived` = 0
					WHERE  `id` = $bank_id");
}

function CheckbankExist($bank_name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
											FROM   `bank`
											WHERE  `name` = '$bank_name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}


function Getbank($bank_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT  `id`,
													`name`,
													`bank_phone`
											FROM    `bank`
											WHERE   `id` = $bank_id" ));

	return $res;
}

function GetLocalID(){
	GLOBAL $db;
	return $db->increment('bank');
}

function GetPage($res = ''){
	$data = '
	<div id="dialog-form">
	    <fieldset>
	    	<legend>ბანკების დასახელება</legend>

	    	<table class="dialog-form-table">
				<tr>
					<td style="width: 170px;"><label for="CallType">სახელი</label></td>
					<td>
						<input type="text" id="name" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['name'] . '" />
					</td>
				</tr>
								<tr>
					<td style="width: 170px;"><label for="CallType">ტელეფონი</label></td>
					<td>
						<input type="text" id="bank_phone" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['bank_phone'] . '" />
					</td>
				</tr>

			</table>

        </fieldset>
		<fieldset id="fiel_bank">
	    	<legend>ფილიალები</legend>
		    <div class="inner-table">
			    <div id="dt_example" class="ex_highlight_row">
			        <div id="container" class="overhead_container">
			        	<div id="button_area">
			        		<button id="add_button_bank">დამატება</button><button id="delete_button_prod">წაშლა</button>
			        	</div>
			            <div id="dynamic">
			                <table class="display" id="obj_list">
			                    <thead>
			                        <tr id="datatable_header">
			                            <th>ID</th>
			                            <th style="width : 100%">სახელი</th>
			                            <th class="min">მისამართი</th>
			                            <th class="check">#</th>
			                        </tr>
			                    </thead>
			                    <thead>
			                        <tr class="search_header">

			                            <th>
			                            	<input type="text" name="search_prod" value="ფილტრი" class="search_init" />
			                            </th>
			                            <th><input type="text" name="search_prod" value="ფილტრი" class="search_init"</th>
										<th>
											<input type="checkbox" name="check-all" id="check-all-prod" style="margin-left: 13px;">
										</th>
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
	<input style="display: none;"  id="bank_id" value="' . $res['id'] . '" />';
	return $data;
}

?>
