<?php
/* ******************************
 *	Partner Bank aJax actions
 * ******************************
 */
 
include('../../../includes/classes/core.php');
$action 	= $_REQUEST['act'];
$user_id	= $_SESSION['USERID'];
$error = '';
$data = '';

switch ($action) {
    case 'get_add_page':
		$page		= GetPage();
		$data		= array('page'	=> $page);
		
        break;
    case 'get_edit_page':
		$bank_id	= $_REQUEST['id'];
		$page		= GetPage(GetBank($bank_id));
		
		$data 		= array('page'	=> $page);

        break;
    case 'get_list':
	    $count = $_REQUEST['count'];
	    $hidden = $_REQUEST['hidden'];
	    $partner_id = $_REQUEST['partner_id'];
	    
	    $rResult = mysql_query("SELECT	`id`,
									  	`name`,
									  	`object`,
									  	`code`,
									  	`account`
								FROM	`partner_bank`
								WHERE	`partner_id` = $partner_id && `actived`=1");
	    
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
    case 'save_bank':
		$bank_id 		= $_REQUEST['bid'];
		$partner_id		= $_REQUEST['pid'];
		
    	$bank_name		= $_REQUEST['bn'];
		$branch		 	= $_REQUEST['bb'];
		$bank_code 		= $_REQUEST['bc'];
		$bank_acc		= $_REQUEST['ba'];

		if($bank_name != '' && $bank_id == ''){
			if(!CheckBankExist($bank_name)){
				AddBank($user_id, $partner_id, $bank_name, $branch, $bank_code, $bank_acc);
			} else {
				$error = '"' . $bank_name . '" უკვე არის სიაში!';
			}
		}else{
			SaveBank($user_id, $bank_id, $partner_id, $bank_name, $branch, $bank_code, $bank_acc);
		}
		
        break;
    case 'disable':
		$bank_id 		= $_REQUEST['id'];
		Disable($bank_id);
		
        break;
    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Partner Bank Functions
 * ******************************
 */

function AddBank($user_id, $partner_id, $bank_name, $branch, $bank_code, $bank_acc)
{
	mysql_query("INSERT INTO `partner_bank`
					(`user_id`,`partner_id`, `name`, `object`, `code`, `account`) 
				 VALUES
				 	($user_id, '$partner_id', '$bank_name', '$branch', '$bank_code', '$bank_acc')");
}

function CheckBankExist($bank_name) 
{
    $res = mysql_fetch_assoc(mysql_query("SELECT	`id`
										  FROM		`partner_bank`
										  WHERE		`name` = '$bank_name'  && `actived`=1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}

function SaveBank($user_id, $bank_id, $partner_id, $bank_name, $branch, $bank_code, $bank_acc)
{
	mysql_query("UPDATE
	    			`partner_bank`
				 SET
				 	`user_id`		= $user_id,
				 	`partner_id`	= '$partner_id',
				 	`name`			= '$bank_name',
				    `object`		= '$branch',
				    `code` 			= '$bank_code',
				    `account` 		= '$bank_acc'
				 WHERE
					`id` = '$bank_id'");
}

function Disable($bank_id)
{
	mysql_query("UPDATE `partner_bank`
				 SET    `actived` = 0
				 WHERE 	`id` = $bank_id");
}

function GetBank($bank_id) 
{
    $res = mysql_fetch_assoc(mysql_query("SELECT	`id`,
    												`name`,
											    	`object`,
											    	`code`,
											    	`account`
									      FROM		`partner_bank`
									      WHERE		`id` = '$bank_id'"));
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
					<td style="width: 170px;"><label for="bank_name">დასახელება</label></td>
					<td>
						<input type="text" id="bank_name" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['name'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="bank_branch">ფილიალი</label></td>
					<td>
						<input type="text" id="bank_branch" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['object'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="bank_code">ბანკის კოდი</label></td>
					<td>
						<input type="text" id="bank_code" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['code'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="bank_account">ანგარიშის №</label></td>
					<td>
						<input type="text" id="bank_account" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['account'] . '" />
					</td>
				</tr>
			</table>
			<!-- ID -->
			<input type="hidden" id="partner_bank_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}
?>