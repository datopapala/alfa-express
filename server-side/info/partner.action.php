<?php
/* ******************************
 *	Partner aJax actions
* ******************************
*/

include('../../includes/classes/core.php');
$action 	= $_REQUEST['act'];
$user_id	= $_SESSION['USERID'];
$error 		= '';
$data 		= '';

switch ($action) {
	case 'get_add_page':
		$page		= GetPage();
		$data		= array('page'	=> $page);

		break;
	case 'get_edit_page':
		$part_id = $_REQUEST['id'];
		$page		= GetPage(GetPartner($part_id));

		$data 		= array('page'	=> $page);

		break;
	case 'get_list':
		$count = $_REQUEST['count'];
		$hidden = $_REQUEST['hidden'];
		$rResult = mysql_query("SELECT		`partners`.`id` 									AS id,
											`partners`.`name`           						AS name,
         									`partners`.`rs_id`            						AS rs_id,
         									`partners`.`physicall_address`  					AS address,
         									`partners`.`telefone`        				 		AS telefone,
											`partners`.`person` 						AS person,
         									`pay_method`.`name`        							AS pay_method
								FROM     	`partners`
								LEFT JOIN 	`pay_method` ON `partners`.`pay_method` = `pay_method`.`id`
								WHERE		`partners`.`actived`=1");

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
			$row['4']=getContactT($row['0']);
			$row['5']=getContactN($row['0']);
			$data['aaData'][] = $row;
		}

		break;		
	case 'save_part':	
		$part_id 		= $_REQUEST['id'];
		$p_identity_id	= $_REQUEST['pid'];
		$arr = array(
						'p_status' 		=>  $_REQUEST['ps'],
						'p_name' 		=>  $_REQUEST['pn'],
						'p_Iaddress'	=>  $_REQUEST['pia'],
						'p_Paddress'	=>  $_REQUEST['ppa'],
						'pay_method' 	=>  $_REQUEST['pm'],
						
						'payer'	 		=>  $_REQUEST['py'],
						'p_data' 		=>  $_REQUEST['pd'],
						'p_s_number' 	=>  $_REQUEST['psn'],
		);			

		if($p_identity_id != '' && $part_id == ''){
			if(!CheckPartnerUserExist($p_identity_id)){
				$in_id = AddPartner( $user_id, $p_identity_id, $arr);
				$data = array('in_id' => $in_id);
			} else {
				$error = '"' . p_identity_id . '" უკვე არის სიაში!';
			}
		}else{
			SavePartner( $user_id, $part_id, $p_identity_id,$arr);
		}

		break;		
	case 'get_local_id':
		$local_id = GetLocalID();
	
		$data = array('local_id' => $local_id);
	
		break;		
	case 'disable':
		$part_id 		= $_REQUEST['id'];
		Disable($part_id);

		break;		
	default:
		$error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);

function Disable($part_id)
{
	mysql_query("UPDATE `partners`
				SET    `actived` = 0
				WHERE 	`id` = $part_id");
}

function GetLocalID()
{
	GLOBAL $db;
	$local_id = $db->increment('partners');

	return $local_id;
}

function GetPartner($part_id)
{
	$res = mysql_fetch_assoc(mysql_query("SELECT	`id`,
       												`rs_id`,
       												`legal_status`,
       												`name`,
       												`legal_address`,
       												`physicall_address`,
       												`pay_method`,
       												`vat_payer`,
       												`vat_data`,
       												`vat_number`
										FROM 		`partners`
										WHERE 		`id` = $part_id && `actived`=1"));
	return $res;
}

function AddPartner( $user_id, $p_identity_id,$arr){
	
	
	mysql_query("INSERT INTO `partners`
	(`user_id`,`rs_id`,`legal_status`,`name`,`legal_address`,`physicall_address`,`pay_method`,`vat_payer`,`vat_data`,`vat_number`)
	VALUES
	('$user_id', '$p_identity_id' , $arr[p_status], '$arr[p_name]','$arr[p_Iaddress]','$arr[p_Paddress]' ,$arr[pay_method], $arr[payer], '$arr[p_data]', '$arr[p_s_number]')");
	return mysql_insert_id();				
}

function SavePartner( $user_id, $part_id, $p_identity_id, $arr){
	mysql_query("UPDATE
						`partners`
				SET
						`user_id`			= '$user_id',
						`name`				= '$arr[p_name]',
						`legal_status`		= $arr[p_status],
						`rs_id`				= '$p_identity_id',
						`legal_address`		= '$arr[p_Iaddress]',
						`physicall_address`	= '$arr[p_Paddress]',
						`pay_method`		= $arr[pay_method],
						`vat_payer`         = $arr[payer],
       					`vat_data`          = '$arr[p_data]',
       					`vat_number`        = '$arr[p_s_number]'
				WHERE	`id` 				= '$part_id'");
}


function CheckPartnerUserExist($p_identity_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT	`id`
											FROM 	`partners`
											WHERE	`rs_id` = '$p_identity_id' && `actived`=1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}

function GStatus($point)
{
	$data = '';
	$req = mysql_query("SELECT	`id`,
							 	`name`
						FROM `legal_status`");

	if($point == ''){
		$data = '<option value="0" selected="selected"></option>';
	}

	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $point){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}

	return $data;
}

function PayForm($point)
{
	$data = '';
	$req = mysql_query("SELECT `id`, `name`
							FROM `pay_method`");

	if($point == ''){
		$data = '<option value="0" selected="selected"></option>';
	}

	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $point){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}

	return $data;
}

function getContactT($partner_id){
	$req = mysql_query("SELECT		`partner_persons`.`m_phone` AS `telefone`
						FROM     	`partner_persons`
						WHERE		`partner_persons`.`actived`=1 && `partner_persons`.`partner_id`='".$partner_id."' && `partner_persons`.`chontacter`=1");
	$res = mysql_fetch_assoc($req);
	if($res != ''){
		return $res['telefone'];
	}else{ 
		return '';
	}
}

function getContactN($partner_id){
	$req = mysql_query("SELECT		CONCAT(`partner_persons`.`f_name`,' ',`partner_persons`.`l_name`) as `name`
						FROM     	`partner_persons`
						WHERE		`partner_persons`.`actived`=1 && `partner_persons`.`partner_id`=$partner_id && `partner_persons`.chontacter=1");
	$res = mysql_fetch_assoc($req);
	if($res != ''){
		return $res['name'];
	}else{ 
		return '';
	}
}

function VatStatus($point)
{
	$data = '';

	switch ($point) {
		case 0:
			$data = '<option value="1">კი</option>
					 <option value="0" selected="selected">არა</option>';
			break;
		case 1:
			$data = '<option value="1" selected="selected">კი</option>
					 <option value="0">არა</option>';
			break;
		default:
			$data = '<option value="1">კი</option>
					 <option value="0" selected="selected">არა</option>';
	}

	return $data;
}

function GetPage($res = ''){
	$data = '
	<div id="dialog-form">
		<div id="tabs">
			<ul>
			    <li id="1"><a href="#tabs-1">ძირითადი ინფორმაცია</a></li>
			    <li id="2"><a href="#tabs-2">ანგარიშწორება</a></li>
			    <li id="3"><a href="#tabs-3">საბანკო რეკვიზიტები</a></li>
			    <li id="4"><a href="#tabs-4">კადრები</a></li>
			</ul>
			<!-- Main Info -->
			<div id="tabs-1">
				<fieldset>
					<legend>პარტნიორი</legend>
			    	<table class="dialog-form-table">
						<tr>
							<td style="width: 170px;"><label for="partner_identity_id">საიდ. კოდი/ პირადი №</label></td>
							<td>
								<input type="text" id="partner_identity_id" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['rs_id'] . '" />
							</td>
						</tr>
						<tr>
							<td style="width: 170px;"><label for="partner_status">იურ. სტატუსი</label></td>
							<td>
								<select id="partner_status" class="idls">' . GStatus($res['legal_status']) . '</select>
							</td>
						</tr>
						<tr>
							<td style="width: 170px;"><label for="partner_name">დასახელება</label></td>
							<td>
								<input type="text" id="partner_name" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['name'] . '" />
							</td>
						</tr>
						<tr>
							<td style="width: 170px;"><label for="partner_Iaddress">იურიდიული მისამართი</label></td>
							<td>
								<input type="text" id="partner_Iaddress" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['legal_address'] . '" />
							</td>
						</tr>
						<tr>
							<td style="width: 170px;"><label for="partner_Paddress">ფაქტიური მისამართი</label></td>
							<td>
								<input type="text" id="partner_Paddress" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['physicall_address'] . '" />
							</td>
						</tr>
						<tr>
							<td style="width: 170px;"><label for="pay_method">ანგარიშსწორების ფორმა</label></td>
							<td>
								<select id="pay_method" class="idls">' . PayForm($res['pay_method']) . '</select>
							</td>
						</tr>
					</table>
			    </fieldset>
			    <fieldset>
			    	<legend>დღგ</legend>
			    	<table class="dialog-form-table">
						<tr>
							<td style="width: 170px;"><label for="payer">გადამხდელია</label></td>
							<td>
								<select id="payer" class="idls small">' . VatStatus($res['vat_payer']) . '</select>
							</td>
						</tr>
						<tr>
							<td style="width: 170px;"><label for="partner_data">თარიღი</label></td>
							<td>
								<input type="text" id="partner_data" class="idle date" onblur="this.className=\'idle date\'" onfocus="this.className=\'activeField date\'" value="' . $res['vat_data'] . '" />
							</td>
						</tr>
						<tr>
							<td style="width: 170px;"><label for="partner_s_number">სერთიფიკატის ნომერი</label></td>
							<td>
								<input type="text" id="partner_s_number" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['vat_number'] . '" />
							</td>
						</tr>
					</table>
			    </fieldset>
			    <!-- ID -->
				<input type="hidden" id="partner_id" value="' . $res['id'] . '" />
			</div>
			<!-- /Main Info -->
			<!-- Account -->
			<div id="tabs-2">
				<fieldset>		
				    <div class="inner-table">
					    <div id="dt_example" class="ex_highlight_row">
					        <div id="container" class="overhead_container">
					        	<div id="button_area">
									<button id="add_acc_button">დამატება</button><button id="delete_acc_button">წაშლა</button>
					        	</div>
					            <div id="dynamic">						
									<table class="display" id="acc_details">
					                    <thead>
					                        <tr id="datatable_header">
					                            <th>ID</th>
					                            <th style="width: 25%">ოპერაციის თარიღი</th>
					                            <th style="width: 25%">ზედნადები</th>
					                            <th style="width: 25%">სულ გადასახდელი</th>
					                            <th style="width: 25%">გადახდილი</th>
					                        </tr>
					                    </thead>
					                    <thead>
					                        <tr class="search_header">
					                            <th class="colum_hidden">
					                            	<input type="text" name="search_a_id" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                                <input type="text" name="search_date" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                                <input type="text" name="search_overhead" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                                <input type="text" name="search_branch" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                                <input type="text" name="search_code" value="ფილტრი" class="search_init" />
					                            </th>
					                        </tr>
					                    </thead>
					                </table>						
					            </div>
					        </div>
					    </div>
					</div>
				</fieldset>
				
				<!-- jQuery Dialog -->
			    <div id="add-edit-acc-form" class="form-dialog" title="ანგარიშწორება">
			    	<!-- aJax -->
				</div>
			</div>
			<!-- /Account -->
			<!-- Bank -->
			<div id="tabs-3">
				<fieldset>		
				    <div class="inner-table">
					    <div id="dt_example" class="ex_highlight_row">
					        <div id="container" class="overhead_container">
					        	<div id="button_area">
									<button id="add_bank_button">დამატება</button><button id="delete_bank_button">წაშლა</button>
					        	</div>
					            <div id="dynamic">						
									<table class="display" id="bank_details">
					                    <thead>
					                        <tr id="datatable_header">
					                            <th>ID</th>
					                            <th style="width: 25%">დასახელება</th>
					                            <th style="width: 25%">ფილიალი</th>
					                            <th style="width: 25%">ბანკის კოდი</th>
					                            <th style="width: 25%">ანგარიშის №</th>
					                            <th class="check">#</th>
					                        </tr>
					                    </thead>
					                    <thead>
					                        <tr class="search_header">
					                            <th class="colum_hidden">
					                            	<input type="text" name="search_b_id" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                                <input type="text" name="search_bank_name" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                                <input type="text" name="search_branch" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                                <input type="text" name="search_code" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                                <input type="text" name="search_account" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                            	<input type="checkbox" name="check-all-bank" id="check-all-bank">
					                            </th>
					                        </tr>
					                    </thead>
					                </table>						
					            </div>
					        </div>
					    </div>
					</div>
				</fieldset>
				
				<!-- jQuery Dialog -->
			    <div id="add-edit-bank-form" class="form-dialog" title="საბანკო რეკვიზიტები">
			    	<!-- aJax -->
				</div>
			</div>
			<!-- /Bank -->
			<!-- Cadre -->
			<div id="tabs-4">
				<fieldset>
				    <div class="inner-table">
					    <div id="dt_example" class="ex_highlight_row">
					        <div id="container" class="overhead_container">
					        	<div id="button_area">
									<button id="add_cadre_button">დამატება</button><button id="delete_cadre_button">წაშლა</button>
					        	</div>
					            <div id="dynamic">						
									<table class="display" id="cadre_details">
					                    <thead>
					                        <tr id="datatable_header">
					                            <th>ID</th>
					                            <th style="width: 25%">პირადი ნომერი</th>
					                            <th style="width: 25%">სახელი</th>
					                            <th style="width: 25%">გვარი</th>
					                            <th style="width: 25%">თანამდებობა</th>
					                            <th class="check">#</th>
					                        </tr>
					                    </thead>
					                    <thead>
					                        <tr class="search_header">
					                            <th class="colum_hidden">
					                            	<input type="text" name="search_c_id" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                                <input type="text" name="search_user_id" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                                <input type="text" name="search_f_name" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                                <input type="text" name="search_l_name" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                                <input type="text" name="search_position" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                            	<input type="checkbox" name="check-all-cadre" id="check-all-cadre">
					                            </th>
					                        </tr>
					                    </thead>
					                </table>						
					            </div>
					        </div>
					    </div>
					</div>
				</fieldset>
			        
				<!-- jQuery Dialog -->
			    <div id="add-edit-cadre-form" class="form-dialog" title="კადრები">
			    	<!-- aJax -->
				</div>
			</div>
			<!-- /Cadre -->							
		</div>
    </div>
    ';
	return $data;
	
	
	
	
}
?>