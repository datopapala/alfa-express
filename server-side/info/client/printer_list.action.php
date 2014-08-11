<?php
/* ******************************
 *	Client Printer List aJax actions
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
		$page		= GetPage(GetPrinterList($list_id));
		
		$data		= array('page'	=> $page);
		
        break;
    case 'get_list':
	    $count		= $_REQUEST['count'];
	    $hidden		= $_REQUEST['hidden'];
	    $local_id	= $_REQUEST['local_id'];
	    
	    $rResult = mysql_query("	SELECT		`printer_pricelist`.`id`,
	    										`production`.`name` AS printer,
												`printer_parts_type`.`name` AS part,
												`printer_pricelist`.`price` AS price
									FROM		`printer_pricelist`
									RIGHT JOIN  `production` ON `printer_pricelist`.`production_id` = `production`.`id`
									RIGHT JOIN	`printer_parts_type` ON `printer_pricelist`.`parts_type` = `printer_parts_type`.`id`
									WHERE		`printer_pricelist`.`client_id` = $local_id && `printer_pricelist`.`actived` = 1");
		
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
    case 'save_printer':
		$printer_id		= $_REQUEST['id'];
		$local_id		= $_REQUEST['lid'];
		$prod_id		= GetProductID($_REQUEST['n']);
		
		$arr = array(
				"prod_id"	=> $prod_id,
				"type"	=> htmlspecialchars($_REQUEST['t'], ENT_QUOTES),
				"price"	=> htmlspecialchars($_REQUEST['p'], ENT_QUOTES)
		);
		
		if($printer_id == ''){
			AddPrinterList($user_id, $local_id, $arr);
		}else{
			SavePrinterList($printer_id, $user_id, $arr);
		}
		
        break;
    case 'disable':
		$list_id = $_REQUEST['id'];
		DisablePrinterList($list_id);
		
        break;
    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Client Printer List Functions
 * ******************************
 */

function AddPrinterList($user_id, $local_id, $arr)
{
	mysql_query("INSERT INTO `printer_pricelist`
					(`user_id`, `client_id`, `production_id`, `parts_type`, `price`)
				 VALUES
					($user_id, $local_id, '$arr[prod_id]', $arr[type], $arr[price])");
}

function SavePrinterList($printer_id, $user_id, $arr)
{
	mysql_query("UPDATE
	    			`printer_pricelist`
				 SET
					`user_id`		= $user_id,
					`production_id`	= '$arr[prod_id]',
					`parts_type`	= '$arr[type]',
					`price`			= '$arr[price]'
				 WHERE
					`id` = $printer_id");
}

function DisablePrinterList($list_id)
{
    mysql_query("	UPDATE
				    	`printer_pricelist`
				    SET
					    `actived`	= 0
				    WHERE
				    	`id` = $list_id");
}

function GetProductID($prod_name)
{
	$prod_name = htmlspecialchars($prod_name, ENT_QUOTES);
	$res = mysql_fetch_assoc(mysql_query("	SELECT	`id`
											FROM	`production`
											WHERE	`name` = '$prod_name'"));
	return $res['id'];
}

function GetPartType($point)
{
	$data	= '';
	
	$req	= mysql_query("	SELECT	`id`,
									`name`
							FROM	`printer_parts_type`");
	
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

function GetPrinterList($list_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT	`printer_pricelist`.`id`,
													`production`.`name`,
													`printer_pricelist`.`parts_type`,
													`printer_pricelist`.`price`
											FROM	`printer_pricelist` LEFT JOIN `production`
												ON	`printer_pricelist`.`production_id` = `production`.`id`
											WHERE	`printer_pricelist`.`id` = $list_id"));
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
					<td style="width: 170px;"><label for="printer_name">დასახელება</label></td>
					<td>
						<div class="seoy-row" id="prod_name_seoy">
							<input type="text" id="printer_name" class="idle seoy-address" onblur="this.className=\'idle seoy-address\'" onfocus="this.className=\'activeField seoy-address\'" value="' . $res['name'] . '" />
							<button id="prod_name_btn" class="combobox">printer_name</button>
						</div>
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="printer_type">ნაწილის ტიპი</label></td>
					<td>
						<select id="printer_type" class="idls">' . GetPartType($res[parts_type]) . '</select>
					</td>
				</tr>
				<tr>
					<td><label for="printer_price_p">ფასი</label></td>
					<td><input type="text" id="printer_price_p" class="idle price" onblur="this.className=\'idle price\'" onfocus="this.className=\'activeField price\'" value="' . $res['price'] . '" /></td>
				</tr>
			</table>
			<!-- ID -->
			<input type="hidden" id="printer_list_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>