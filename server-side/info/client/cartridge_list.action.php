<?php
/* ******************************
 *	Client Cartridge List aJax actions
 * ******************************
 */

include('../../../includes/classes/core.php');

$action		= $_REQUEST['act'];
$error		= '';
$data		= '';
$user_id	= $_SESSION['USERID'];

switch ($action) {
    case 'get_add_page':
		$page		= GetPage();
		$data		= array('page'	=> $page);
		
        break;
    case 'get_edit_page':
	    $list_id	= $_REQUEST['id'];
		$local_id	= $_REQUEST['lid'];
		$page		= GetPage(GetCartridgeList($list_id, $local_id));
		
		$data		= array('page'	=> $page);
		
        break;
    case 'get_list':
	    $count		= $_REQUEST['count'];
	    $hidden		= $_REQUEST['hidden'];
	    $local_id	= $_REQUEST['local_id'];
	    
	    $rResult = mysql_query("	SELECT      `cartridge_pricelist`.`production_id`,
									            `production`.`name` AS cartridge,
									             SUM(`cartridge_pricelist`.`price`) AS price
									FROM		`cartridge_pricelist`
									RIGHT JOIN  `production` ON `cartridge_pricelist`.`production_id` = `production`.`id`
									RIGHT JOIN	`cartridge_parts_type` ON `cartridge_pricelist`.`parts_type` = `cartridge_parts_type`.`id`
									WHERE		`cartridge_pricelist`.`client_id` = $local_id && `cartridge_pricelist`.`actived` = 1 && `production`.`actived` = 1
									GROUP BY	`cartridge_pricelist`.production_id");
		
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
    case 'save_cartridge':
		$cartridge_id	= $_REQUEST['id'];
		$local_id		= $_REQUEST['lid'];
		$prod_id		= GetProductID($_REQUEST['n']);
		$list			= json_decode($_REQUEST['list']);
		
		if($cartridge_id == ''){
			if(!CheckProductionExist($prod_id, $local_id)){
				for ($i = 0; $i < count($list); $i++) {
					AddCartridgeList($user_id, $local_id, $prod_id, $list[$i][0], $list[$i][1]);
				}
			} else {
				$error = '"' . $_REQUEST['n'] . '" უკვე არის სიაში!';
			}
		}else{
			for ($i = 0; $i < count($list); $i++) {
				SaveCartridgeList($cartridge_id, $user_id, $prod_id, $list[$i][0], $list[$i][1]);
			}
		}
		
        break;
    case 'get_parts_table':
		$prod_id	= GetProductID($_REQUEST['pn']);
		$local_id	= $_REQUEST['lid'];
		$table		= GetPartsTable($prod_id, $local_id);
		
		$data		= array('parts_table' => $table);
        
        break;
	case 'save_planned_quantity':
		$list			= json_decode($_REQUEST['list']);
		$type			= $_REQUEST['type'];
		SavePlannedQuantity($list, $type);
				
        break;	
    case 'disable':
		$cliend_id = $_REQUEST['lid'];
		$prod_id = $_REQUEST['id'];
		DisableCatridgeList($cliend_id,$prod_id);
		
        break;
    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Client Cartridge List Functions
 * ******************************
 */

function AddCartridgeList($user_id, $local_id, $prod_id, $price, $id)
{
	mysql_query("INSERT INTO `cartridge_pricelist`
					(`user_id`, `client_id`, `production_id`, `parts_type`, `price`)
				 VALUES
					($user_id, $local_id, $prod_id, $id, $price)");
}

function SaveCartridgeList($cartridge_id, $user_id, $prod_id, $price, $id)
{
	mysql_query("UPDATE
	    			`cartridge_pricelist`
				 SET
					`user_id`		= $user_id,
					`production_id`	= $prod_id,
					`price`			= $price
				 WHERE
					`id` = $id");
}

function SavePlannedQuantity($list, $type){
	if($type == 0 ){
		for ($i = 0; $i < count($list); $i++) {
			mysql_query("	UPDATE	`cartridge_pricelist`
			SET		`planned_quantity` =". $list[$i][0]."
			WHERE	`id` =". $list[$i][1]);
		}
	}else{
		for ($i = 0; $i < count($list); $i++) {
			mysql_query("	UPDATE	`printer_pricelist`
							SET		`planned_quantity` =". $list[$i][0]."
							WHERE	`id` =". $list[$i][1]);
		}				
	}
	
}

function DisableCatridgeList($cliend_id,$prod_id)
{
    mysql_query("UPDATE `cartridge_pricelist`
				SET `actived` = 0
				WHERE client_id= $cliend_id AND production_id = $prod_id");
}

function CheckProductionExist($prod_id, $local_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT	`id`
											FROM	`cartridge_pricelist`
											WHERE	`production_id` = $prod_id && `client_id` = $local_id && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}

function GetProductID($prod_name)
{
	$prod_name = htmlspecialchars($prod_name, ENT_QUOTES);
	$res = mysql_fetch_assoc(mysql_query("	SELECT	`id`
											FROM	`production`
											WHERE	`name` = '$prod_name'"));
	return $res['id'];
}

function GetPartsTable($prod_id, $client_id)
{
	$data = '';
	if (empty($client_id)) {
		$req = mysql_query("SELECT DISTINCT	`cartridge_identity`.`parts_type` AS `id`,
										`cartridge_parts_type`.`name`,
										0 as price
							FROM		`cartridge_identity`
							LEFT JOIN	`cartridge_parts_type` ON `cartridge_identity`.`parts_type` = `cartridge_parts_type`.`id`
							WHERE		`cartridge_identity`.`production_id` = $prod_id && `cartridge_identity`.`actived` = 1");
	}else{
		$req = mysql_query("SELECT		`cartridge_pricelist`.`id`,
										`cartridge_parts_type`.`name`,
										`cartridge_pricelist`.`price`
							FROM		`cartridge_pricelist`
							LEFT JOIN	`cartridge_parts_type` ON `cartridge_pricelist`.`parts_type` = `cartridge_parts_type`.`id`
							WHERE		`cartridge_pricelist`.`client_id` = $client_id && `cartridge_pricelist`.`production_id` = $prod_id && `cartridge_pricelist`.`actived` = 1");
	}
	while( $res = mysql_fetch_assoc($req)){
		$data .= '
					<tr>
						<td>' . $res['name'] . '</td>
						<td><input type="text" id="cartridge_price" class="idle price" onblur="this.className=\'idle price\'" onfocus="this.className=\'activeField price\'" value="' . $res['price'] . '" parts_id="' . $res['id'] . '" /></td>
					</tr>';
	}
	return $data;
}

function GetCartridgeList($list_id, $local_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT	`cartridge_pricelist`.`id`,
													`production`.`id` AS `prod_id`,
													`production`.`name`,
													`cartridge_pricelist`.`parts_type`,
													`cartridge_pricelist`.`price`
											FROM	`cartridge_pricelist` LEFT JOIN `production`
												ON	`cartridge_pricelist`.`production_id` = `production`.`id`
											WHERE	`cartridge_pricelist`.`production_id` = $list_id && `client_id` = $local_id"));
	return $res;
}

function GetPage($res = '')
{
	$data = '
	<div id="dialog-form">
	    <fieldset>
	    	<legend>ძირითადი ინფორმაცია</legend>
    		<table cellpadding="0" cellspacing="0" border="0" class="dialog-form-table">
				<tr>
					<td style="width: 170px;"><label for="cartridge_name">დასახელება</label></td>
					<td>
						<div class="seoy-row" id="prod_name_seoy">
							<input type="text" id="cartridge_name" class="idle seoy-address" onblur="this.className=\'idle seoy-address\'" onfocus="this.className=\'activeField seoy-address\'" value="' . $res['name'] . '" />
							<button id="prod_name_btn" class="combobox">cartridge_name</button>
						</div>
					</td>
				</tr>
			</table>
        </fieldset>
	    <fieldset>
	    	<legend>თავსებადი ნაწილები</legend>
			<table class="dialog-form-inner-table">
				<thead>
					<tr>
						<th class="ui-state-default" style="width: 75%">ტიპი</th>
						<th class="ui-state-default" style="width: 25%">ფასი</th>
					</tr>
				</thead>
				<tbody id="parts_table">
				</tbody>
			</table>
        </fieldset>
		<!-- ID -->
		<input type="hidden" id="cartridge_list_id" value="' . $res['id'] . '" />
    </div>
    ';
	return $data;
}
?>