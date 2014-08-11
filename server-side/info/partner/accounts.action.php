<?php
/* ******************************
 *	Partner Accounts aJax actions
 * ******************************
 */
 
include('../../../includes/classes/core.php');
$action		= $_REQUEST['act'];
$user_id	= $_SESSION['USERID'];
$error = '';
$data = '';

switch ($action) {
    case 'get_add_page':
		$page		= GetPage();
		$data		= array('page'	=> $page);
		
        break;
    case 'get_edit_page':
		$acc_id	= $_REQUEST['id'];
		$page		= GetPage(GetAccount($acc_id));
		
		$data 		= array('page'	=> $page);

        break;
    case 'get_list':
	    $count = $_REQUEST['count'];
	    $hidden = $_REQUEST['hidden'];
	    $part_id = $_REQUEST['part_id'];
	    
	    $rResult = mysql_query("SELECT `partner_accounts`.`id`,
									   `partner_accounts`.`pay_date`,
									   `overhead`.`waybill_number`,
									   `partner_accounts`.`pay_needed`,
									   `partner_accounts`.`payed`
								FROM `partner_accounts` LEFT JOIN `overhead`
									ON `partner_accounts`.`overhead_id` = `overhead`.`id`
								WHERE `partner_id` = '$part_id' && `actived`=1");
	    
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
			}
			$data['aaData'][] = $row;
		}

        break;
    case 'save_acc':
    	/*
    	param.pid	= $("#partner_id").val();
    	param.aid	= $("#acc_id").val();
    	param.bid	= $("#partner_bank_id").val();
    	
    	param.auid	= $("#acc_user_id").val();
    	param.an	= $("#acc_name").val();
    	param.aln	= $("#acc_lname").val();
    	param.ap	= $("#acc_position").val();
    	param.ainfo	= $("#acc_info").val();
    	param.ap 	= $("#acc_phone").val();
    	param.amp 	= $("#acc_m_phone").val();
    	
    	param.as	= $("#acc_sale").val();
    	param.bl	= $("#acc_limit").val();
    	
    	$acc_id 			= $_REQUEST['aid'];
    	$partner_id 		= $_REQUEST['pid'];
    	
    	$person_id			= $_REQUEST['auid'];
    		
    	$person_id			= $_REQUEST['cui'];
    	$f_name	 			= $_REQUEST['cn'];
    	$l_name				= $_REQUEST['cln'];
    	$cadre_position		= $_REQUEST['cp'];
    	$cadre_contact		= $_REQUEST['cc'];
    	$cadre_phone		= $_REQUEST['cph'];
    	$cadre_m_phone		= $_REQUEST['cmp'];
    	
    	
    	

		if($bank_name != '' && $bank_id == ''){
			if(!CheckBankExist($bank_name)){
				AddBank($partn_id, $bank_name, $branch, $bank_code, $bank_acc);
			} else {
				$error = '"' . $bank_name . '" უკვე არის სიაში!';
			}
		}else{
			SaveBank($bank_id, $branch, $bank_code, $bank_acc);
		}
		*/
    	
        break;
        case 'disable':
        	$acc_id 		= $_REQUEST['id'];
        	Disable($acc_id );
        
        	break;
    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Partner Accounts Functions
 * ******************************
 */

function Disable($acc_id )
{
}

function AddAccount($partn_id, $bank_name, $branch, $bank_code, $bank_acc)
{
	mysql_query("INSERT INTO `partner_bank`
					(`partn_id`, `bank_dasaxeleba`, `bank_filiali`, `bank_kodi`, `bank_angarishi`) 
				 VALUES
					('$partn_id', '$bank_name', '$branch', '$bank_code', '$bank_acc')");
}

function SaveAccount($bank_id, $branch, $bank_code, $bank_acc)
{
	mysql_query("UPDATE
	    			`partner_bank`
				 SET
				    `bank_filiali` = '$branch',
				    `bank_kodi` = '$bank_code',
				    `bank_angarishi` = '$bank_acc'
				 WHERE
					`id` = '$bank_id'");
}

function GetAccount($acc_id)
{
	$res = mysql_fetch_assoc(mysql_query("SELECT	`partner_accounts`.`id`,
												 	`partner_accounts`.`pay_date`,
												 	`overhead`.`waybill_number`,
												 	`partner_accounts`.`pay_needed`,
													`partner_accounts`.`payed`
										  FROM		`partner_accounts` LEFT JOIN `overhead`
											ON 		`partner_accounts`.`overhead_id` = `overhead`.`id`
										  WHERE 	`partner_accounts`.`id` = $acc_id"));
	return $res;
}

function GetPage($res = '')
{
	$data = '
	<div id="dialog-form">
	    <fieldset>
	    	<legend>ახლის დამატება</legend>
	    	<table class="dialog-form-table">
				<tr>
					<td style="width: 170px;"><label for="user_id">პირადი №</label></td>
					<td>
						<input type="text" id="user_id" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['user_id'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="c_fname">სახელი</label></td>
					<td>
						<input type="text" id="c_fname" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['f_name'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="c_lname">გვარი</label></td>
					<td>
						<input type="text" id="c_lname" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['l_name'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="c_position">თანამდებობა</label></td>
					<td>
						<input type="text" id="c_position" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['position'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="c_contact">საკონტაქტო</label></td>
					<td>
						<select id="c_contact" class="idls small"></select>
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="c_phone">ტელეფონი</label></td>
					<td>
						<input type="text" id="c_phone" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['phone'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="c_m_phone">მობილური</label></td>
					<td>
						<input type="text" id="c_m_phone" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['m_phone'] . '" />
					</td>
				</tr>
			</table>
	    </fieldset>
	    <fieldset>		    
	    	<legend>ფასდაკლება</legend>
    		<table class="dialog-form-table">
				<tr>
					<td style="width: 170px;"><label for="c_sale">ფასდაკლების %</label></td>
					<td>
						<input type="text" id="c_sale" class="idle num" onblur="this.className=\'idle num\'" onfocus="this.className=\'activeField num\'" value="' . $res['sale_rate'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="c_limit">ლიმიტი</label></td>
					<td>
						<input type="text" id="c_limit" class="idle num" onblur="this.className=\'idle num\'" onfocus="this.className=\'activeField num\'" value="' . $res['sale_limit'] . '" />
					</td>
				</tr>
			</table>
			<!-- ID -->
			<input type="hidden" id="cadre_id" value="' . $res['id'] . '" />
	    </fieldset>
    </div>
    ';
	return $data;
}
?>