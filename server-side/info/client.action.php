<?php
/* ******************************
 *	Client aJax actions
 * ******************************
 */
 
include('../../includes/classes/core.php');
include('../../includes/classes/logger.class.php');

$action 	= $_REQUEST['act'];
$error		= '';
$data		= '';
$user_id	= $_SESSION['USERID'];

$_log   = Logger::instance('../../log/info/client/', Logger::OFF);

switch ($action) {
    case 'get_add_page':
		$page		= GetPage();
		
		$data		= array('page'	=> $page);
		
        break;
    case 'get_edit_page':
	    $client_id		= $_REQUEST['id'];
		$page		= GetPage(GetClient($client_id));
        
        $data 		= array('page'	=> $page);
        
        break; 
    case 'get_planned_quantity':
    	$prod_id	= $_REQUEST['id']; 
    	$client_id	= $_REQUEST['cid'];
    	
    	$type		= $_REQUEST['t'];
    	$page		= GetPlannedQuantity($client_id, $prod_id, $type);    	
    	$data 		= array('page'	=> $page);
    	
    	break;       
    case 'get_list':
	    $count = $_REQUEST['count'];
	    $hidden = $_REQUEST['hidden'];
	    
	    $rResult = mysql_query("SELECT	`client`.`id`,
										`client`.`name`,
										`client`.`rs_id`,
	    								`client`.`address`,
	    								`client`.`phone_number`,
	    								`client`.`contact_person`,
	    								`pay_method`.`name`
							    FROM 	`client` LEFT JOIN `pay_method`
								ON		`client`.`pay_method` = `pay_method`.`id` 
	    						WHERE 	`client`.`actived` = 1");
		
		$data = array(
			"aaData"	=> array()
		);
		
		if(!$rResult){
			$error = 'Invalid query: ' . mysql_error();
		}else{
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
			
			$_log->logInfo('get_list');
		}
		
        break;
    case 'get_cartridzge_price':
    	$client_id 		= $_REQUEST['local_id'];
    	$count = $_REQUEST['count'];
    	$hidden = $_REQUEST['hidden'];
    	
    	
    	
    	$rResult = mysql_query("SELECT DISTINCT  `production`.`id` as `id`,
								    			`production`.`name`,
								    			ROUND(`cp2`.`price`),
								    			ROUND(`cp3`.`price`),
								    			ROUND(`cp4`.`price`),
								    			ROUND(`cp5`.`price`),
								    			ROUND(`cp6`.`price`),
								    			ROUND(`cp7`.`price`),
								    			`cp2`.`planned_quantity`,
								    			`cp3`.`planned_quantity`,
								    			`cp4`.`planned_quantity`,
								    			`cp5`.`planned_quantity`,
								    			`cp6`.`planned_quantity`,
								    			`cp7`.`planned_quantity`,
								    			'0' as `fact1`,
								    			'0' as `fact2`,
								    			'0' as `fact3`,
								    			'0' as `fact4`,
								    			'0' as `fact5`,
								    			'0' as `fact6`,
								    			`cp2`.`price` * `cp2`.`planned_quantity` +
								    			`cp3`.`price` * `cp3`.`planned_quantity` +
								    			`cp4`.`price` * `cp4`.`planned_quantity` +
								    			`cp5`.`price` * `cp5`.`planned_quantity` +
								    			`cp6`.`price` * `cp6`.`planned_quantity` +
								    			`cp7`.`price` * `cp7`.`planned_quantity`,
								    			'' as `fact`,
								    			'' as `delta`,
								    			'' as `persentage`,
								    			'' as `money`								    				
					    			FROM              `client`
					    			LEFT JOIN cartridge_pricelist ON client.id = cartridge_pricelist.client_id
					    			LEFT JOIN production ON production.id = cartridge_pricelist.production_id
					    			LEFT JOIN `cartridge_pricelist` as `cp2` ON `cp2`.`client_id` = client.id AND `cp2`.parts_type = 2 AND `cp2`.`production_id` = `production`.`id` AND `cp2`.`actived` = 1
					    			LEFT JOIN `cartridge_pricelist` as `cp3` ON `cp3`.`client_id` = client.id AND `cp3`.parts_type = 3 AND `cp3`.`production_id` = `production`.`id` AND `cp3`.`actived` = 1
					    			LEFT JOIN `cartridge_pricelist` as `cp4` ON `cp4`.`client_id` = client.id AND `cp4`.parts_type = 4 AND `cp4`.`production_id` = `production`.`id` AND `cp4`.`actived` = 1
					    			LEFT JOIN `cartridge_pricelist` as `cp5` ON `cp5`.`client_id` = client.id AND `cp5`.parts_type = 5 AND `cp5`.`production_id` = `production`.`id` AND `cp5`.`actived` = 1
					    			LEFT JOIN `cartridge_pricelist` as `cp6` ON `cp6`.`client_id` = client.id AND `cp6`.parts_type = 6 AND `cp6`.`production_id` = `production`.`id` AND `cp6`.`actived` = 1
					    			LEFT JOIN `cartridge_pricelist` as `cp7` ON `cp7`.`client_id` = client.id AND `cp7`.parts_type = 7 AND `cp7`.`production_id` = `production`.`id` AND `cp7`.`actived` = 1
					    			WHERE  `client`.`id` = '$client_id' AND NOT	ISNULL(`production`.`id`)
					    			GROUP BY	`production`.`id`");
    	$data = array(
    			"aaData"	=> array()
    	);
    	
    	if(!$rResult){
    		$error = 'Invalid query: ' . mysql_error();
    	}else{
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
    			
    		$_log->logInfo('get_list');
    	}
    	
    	 
    	break;
    case 'get_printer_price':
    	$client_id 	= $_REQUEST['local_id'];
    	$count 		= $_REQUEST['count'];
    	$hidden 	= $_REQUEST['hidden'];
    	
    	
    	$rResult = mysql_query("SELECT           `id`,
							                `name` ,
							                ROUND(`price1`),
							                ROUND(`price2`),
							                ROUND(`price3`),
							                ROUND(`price4`),
							                ROUND(`price5`),
							                ROUND(SUM(`geg`)),
							                `planned_quantity1`,
							                `planned_quantity2`,
							                `planned_quantity3`,
							                `planned_quantity4`,
							                `planned_quantity5`,
							                SUM(`planned_quantity`),
							                `fact1`,
							                `fact2`,
							                `fact3`,
							                `fact4`,
							                `fact5`,
							                `fact6`,
							                SUM(`geg`),
							                `fact`,
							                `delta`,
							                `persentage`,
							                `money`
							FROM
							(
							SELECT  DISTINCT `production`.`id` as `id`,
							                `production`.`name` as `name` ,
							                IF( ISNULL(`pp5`.`price`),0,`pp5`.`price`) as `price1`,
							                IF( ISNULL(`pp15`.`price`),0,`pp15`.`price`) as `price2`,
							                IF( ISNULL(`pp27`.`price`),0,`pp27`.`price`) as `price3`,
							                IF( ISNULL(`pp2`.`price`),0,`pp2`.`price`) as `price4`,
							                IF( ISNULL(`pp23`.`price`),0,`pp23`.`price`) as `price5`,
							                IF( ISNULL( (`pp`.`price`) ),0 ,(`pp`.`price`)) as `price`,
							                IF( ISNULL(`pp5`.`planned_quantity`),0,`pp5`.`planned_quantity`) as `planned_quantity1`,
							                IF( ISNULL(`pp15`.`planned_quantity`),0,`pp15`.`planned_quantity`) as `planned_quantity2`,
							                IF( ISNULL(`pp27`.`planned_quantity`),0,`pp27`.`planned_quantity`) as `planned_quantity3`,
							                IF( ISNULL(`pp2`.`planned_quantity`),0,`pp2`.`planned_quantity`) as `planned_quantity4`,
							                IF( ISNULL(`pp23`.`planned_quantity`),0,`pp23`.`planned_quantity`) as `planned_quantity5`,
							               `pp`.`planned_quantity` as `planned_quantity`,
							                '0' as `fact1`,
							                '0' as `fact2`,
							                '0' as `fact3`,
							                '0' as `fact4`,
							                '0' as `fact5`,
							                '0' as `fact6`,
							                IF( ISNULL(`pp5`.`price`),0,`pp5`.`price`) * IF( ISNULL(`pp5`.`planned_quantity`),0,`pp5`.`planned_quantity`) +
							                IF( ISNULL(`pp15`.`price`),0,`pp15`.`price`) * IF( ISNULL(`pp15`.`planned_quantity`),0,`pp15`.`planned_quantity`) +
							                IF( ISNULL(`pp27`.`price`),0,`pp27`.`price`) * IF( ISNULL(`pp27`.`planned_quantity`),0,`pp27`.`planned_quantity`) +
							                IF( ISNULL(`pp2`.`price`),0,`pp2`.`price`) * IF( ISNULL(`pp2`.`planned_quantity`),0,`pp2`.`planned_quantity`) +
							                IF( ISNULL(`pp23`.`price`),0,`pp23`.`price`) * IF( ISNULL(`pp23`.`planned_quantity`),0,`pp23`.`planned_quantity`) +
							                IF( ISNULL( (`pp`.`price`) ),0 ,(`pp`.`price`)) * IF( ISNULL( (`pp`.`planned_quantity`) ),0 ,(`pp`.`planned_quantity`)) as `geg`,
							                '' as `fact`,
							                '' as `delta`,
							                '' as `persentage`,
							                '' as `money`
							FROM   client
							LEFT JOIN printer_pricelist ON client.id = printer_pricelist.client_id
							LEFT JOIN  production ON production.id = printer_pricelist.production_id
							LEFT JOIN  printer_pricelist AS `pp5` ON `pp5`.client_id = client.id AND `pp5`.parts_type = 5 AND `pp5`.production_id = production.id AND `pp5`.actived = 1
							LEFT JOIN  printer_pricelist AS `pp15` ON `pp15`.client_id = client.id AND `pp15`.parts_type= 15 AND `pp15`.production_id = production.id AND `pp15`.actived = 1
							LEFT JOIN  printer_pricelist AS `pp27` ON `pp27`.client_id = client.id AND `pp27`.parts_type= 27 AND `pp27`.production_id = production.id AND `pp27`.actived = 1
							LEFT JOIN printer_pricelist AS `pp2` ON `pp2`.client_id = client.id AND `pp2`.parts_type = 2 AND `pp2`.production_id = production.id AND `pp2`.actived = 1
							LEFT JOIN printer_pricelist AS `pp23` ON `pp23`.client_id = client.id AND `pp23`.parts_type = 23 AND `pp23`.production_id = production.id AND `pp23`.actived = 1
							LEFT JOIN  printer_pricelist AS pp ON pp.client_id = client.id AND pp.parts_type not in (5,15,27,2,23) AND pp.production_id = production.id AND pp.actived = 1
							WHERE    `client`.`id` = '$client_id' AND NOT ISNULL(`production`.`id`)
							) as `dream`
							GROUP BY  `id`");
    	$data = array(
    			"aaData"	=> array()
    	);
    	 
    	if(!$rResult){
    		$error = 'Invalid query: ' . mysql_error();
    	}else{
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
    		 
    		$_log->logInfo('get_list');
    	}
    	    	    	 
    	break;   	
    case 'save_client':
		$client_id 		= $_REQUEST['id'];
		
		$arr = array(
				//Main
				"rs_id"				=> $_REQUEST['ri'],
				"name"				=> htmlspecialchars($_REQUEST['n'], ENT_QUOTES),
				"address"			=> htmlspecialchars($_REQUEST['a'], ENT_QUOTES),
				"legal_status"		=> $_REQUEST['ls'],
				"pay_method"		=> $_REQUEST['pm'],		
				"vat_payer"			=> $_REQUEST['vp'],
				
				"contact_person"	=> htmlspecialchars($_REQUEST['cp'], ENT_QUOTES),
				"phone_number"		=> htmlspecialchars($_REQUEST['pn'], ENT_QUOTES),
				"mail"				=> htmlspecialchars($_REQUEST['m'], ENT_QUOTES),
				
				"comment"			=> htmlspecialchars($_REQUEST['c'], ENT_QUOTES),
				'image'				=> $_REQUEST['img']
		);
		
		if($client_id == ''){
			if(!CheckClientExist($arr['rs_id'])){
				AddClient($user_id, $arr);
			} else {
				$error = '"' . $arr['name'] . '" უკვე არის სიაში!';
			}
		}else{
			SaveClient($client_id, $user_id, $arr);
		}
		
        break;
    case 'disable':
		$client_id	= $_REQUEST['id'];
		DisableClient($client_id);
		
        break;
    case 'delete_image':
		$client_id = $_REQUEST['id'];
		DeleteImage($client_id);		
		
        break;
    case 'get_local_id':
		$local_id = GetLocalID();
		
		$data = array('local_id' => $local_id);
        
        break;
    case 'clear_db':
		ClearDB();
		
        break;
    case 'clear':
		$file_list = $_REQUEST['file'];
		if (!empty($file_list)) {
			$file_list = ClearFiles(json_decode($file_list));
		}
		$data = array('file_list' => json_encode($file_list));
		
        break;
    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Client Functions
 * ******************************
 */

function ClearDB()
{
	$req = mysql_query("SELECT	`id`
						FROM	`client_objects`
						WHERE	`client_id` NOT IN (SELECT `id` FROM `client`)");

	while( $res = mysql_fetch_assoc($req)){
		mysql_query("DELETE
					 FROM
						`client_objects`
					 WHERE
						`id` = $res[id]");
	}
}

function ClearFiles($file_list) {
	$array = array();
	foreach ($file_list as $file) {
		if($file != '0.jpg' && $file != 'index.html'){
			$req = mysql_query("SELECT	`id`
								FROM	`client`
								WHERE	`image` = '$file'");
			if (mysql_num_rows($req) == 0){
				$array[] = $file;
			}
		}
	}
	return $array;
}

function AddClient($user_id, $arr)
{
	mysql_query("INSERT INTO `client`
					(`user_id`, `name`, `legal_status`, `rs_id`, `vat_payer`, `pay_method`, `contact_person`, `phone_number`, `address`, `mail`, `image`, `comment`) 
				 VALUES
					($user_id, '$arr[name]', $arr[legal_status], $arr[rs_id], $arr[vat_payer], $arr[pay_method], '$arr[contact_person]', '$arr[phone_number]', '$arr[address]', '$arr[mail]', '$arr[image]', '$arr[comment]')");
}

function SaveClient($client_id, $user_id, $arr) 
{
	mysql_query("UPDATE
	    			`client`
				 SET
					`user_id`			= $user_id,
					`name`				= '$arr[name]',
					`legal_status`		= $arr[legal_status],
					`rs_id`				= $arr[rs_id],
					`vat_payer`			= $arr[vat_payer],
					`pay_method`		= $arr[pay_method],
					`contact_person`	= '$arr[contact_person]',
					`phone_number`		= '$arr[phone_number]',
					`address`			= '$arr[address]',
					`mail`				= '$arr[mail]',
					`image`				= '$arr[image]',
					`comment`			= '$arr[comment]'
				 WHERE
					`id` = $client_id");
}

function DisableClient($client_id)
{
    mysql_query("UPDATE `client`
				 SET	`actived` = 0
				 WHERE	`id` = $client_id ");
					
    mysql_query("UPDATE `client_objects`
				 SET	`actived` = 0
				 WHERE	`client_id` = $client_id ");
}

function DeleteImage($client_id)
{
	mysql_query("UPDATE
	    			`client`
				 SET
				    `image`			= NULL
				 WHERE
					`id`			= $client_id");
}

function GetLocalID()
{
	GLOBAL $db;
	$local_id = $db->increment('client');
	
	return $local_id;
}

function GetPlannedQuantity($client_id, $prod_id, $type){
	$data = '	<div id="dialog-form">
				    <fieldset>
				    	<legend>თავსებადი ნაწილები</legend>
						<table class="dialog-form-inner-table" id="planned_quantity">
							<thead>
								<tr>
									<th class="ui-state-default" style="width: 65%">ტიპი</th>
									<th class="ui-state-default" style="width: 35%">გეგმ.რაოდენობა</th>
								</tr>
							</thead>
							<tbody id="parts_table">';
	if( $type == 0){
		$req = mysql_query("SELECT		`cartridge_pricelist`.`id` as `id`,
										`cartridge_parts_type`.`name` as `name`,
										`cartridge_pricelist`.`planned_quantity` as `quantity`
							FROM		`cartridge_pricelist`
							LEFT JOIN	`cartridge_parts_type` ON `cartridge_pricelist`.`parts_type` = `cartridge_parts_type`.`id`
							WHERE		`cartridge_pricelist`.`client_id` = '$client_id' && `cartridge_pricelist`.`production_id` = '$prod_id' && `cartridge_pricelist`.`actived` = 1");
	}else{
		$req = mysql_query("SELECT		`printer_pricelist`.`id` as `id`,
										`printer_parts_type`.`name` as `name`,
										`printer_pricelist`.`planned_quantity` as `quantity`
							FROM		`printer_pricelist`
							LEFT JOIN	`printer_parts_type` ON `printer_parts_type`.`id` = `printer_pricelist`.`parts_type`
							WHERE		`printer_pricelist`.`client_id` = '$client_id' && `printer_pricelist`.`production_id` = '$prod_id' && `printer_pricelist`.`actived` = 1");
	}

	while( $res = mysql_fetch_assoc($req)){
		$data .= '
					<tr>
						<td>' . $res['name'] . '</td>
						<td><input type="text" id="cartridge_price" class="idle price" onblur="this.className=\'idle price\'" onfocus="this.className=\'activeField price\'" value="' . $res['quantity'] . '" cartridge_pricelist="' . $res['id'] . '" /></td>
					</tr>';
	}
	$data .='
							</tbody>
						</table>
			        </fieldset>
					<!-- ID -->
					<input type="hidden" id="cartridge_list_id" value="' . $client_id . '" />
					<input type="hidden" id="planned_quantity_type" value="' . $type  . '" />
							
			    </div>';
	
	return $data;
	
}

function CheckClientExist($client_name) 
{
    $res = mysql_fetch_assoc(mysql_query("SELECT	`id`
										  FROM		`client`
										  WHERE		`name` = '$client_name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}

function GetLegalStatus($point)
{
	$data = '';
	$req = mysql_query("SELECT	`id`,
								`name`
						FROM	`legal_status`");

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

function GetPayMethod($point)
{
	$data = '';
	$req = mysql_query("SELECT	`id`, `name`
						FROM	`pay_method`");

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

function GetVatStatus($point)
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

function GetClient($client_id) 
{
    $res = mysql_fetch_assoc(mysql_query("SELECT 	`id`,
    											 	`name`,
											     	`legal_status`,
											     	`rs_id`,
    												`vat_payer`,
    												`pay_method`,
											     	`contact_person`,
											     	`phone_number`,
											     	`address`,
											     	`mail`,
													`image`,
											     	`comment`
									      FROM 		`client`
									      WHERE 	`id` = $client_id"));
	return $res;
}


function ChangeMonth($month){
	switch ($month){
		case 'January'	:  $month = 'იანვარი'; break;
		case 'February' :  $month = 'თებერვალი'; break;
		case 'March'	:  $month = 'მარტი'; break;
		case 'April'	:  $month = 'აპრილი'; break;
		case 'May'		:  $month = 'მაისი'; break;
		case 'June'		:  $month = 'ივნისი'; break;
		case 'July'		:  $month = 'ივლისი'; break;
		case 'August'	:  $month = 'აგვისტო'; break;
		case 'September':  $month = 'სექტემბერი'; break;
		case 'October'	:  $month = 'ოქტომბერი'; break;
		case 'November' :  $month = 'ნოემბერი'; break;
		case 'December' :  $month = 'დეკემბერი'; break;						
	}
	return $month;	
}

function GetPage($res = '')
{
	$c_date = date('F');
	$c_date = ChangeMonth($c_date);
	$image = $res[image];
	if(empty($image)){
		$image = '0.jpg';
	}
	$data = '
	<div id="dialog-form">
		<div id="tabs">
			<ul>
			    <li id="1"><a href="#tabs-1" style=" font-size: 12px;">ძირითადი ინფორმაცია</a></li>
			    <li id="2"><a href="#tabs-2" style=" font-size: 12px;" title="ანგარიშსწორება">ანგ - ბა</a></li>
			    <li id="3"><a href="#tabs-3" style=" font-size: 12px;">ობიექტები</a></li>
			    <li id="4"><a href="#tabs-4" style=" font-size: 12px;">პრინტერების ფასები</a></li>
			    <li id="5"><a href="#tabs-5" style=" font-size: 12px;">კარტრიჯების ფასები</a></li>
			    <li id="6"><a href="#tabs-6" style=" font-size: 12px;">ბრუნვა</a></li>
			    <li id="7"><a href="#tabs-7" style=" font-size: 12px;" title="შესრულების გეგმა">შეს. გეგმა</a></li>		
			</ul>
			<!-- Main Info -->
			<div id="tabs-1" style="height: 354px;">
			    <fieldset style="width: 450px; float: left;">
			    	<legend>ძირითადი ინფორმაცია</legend>
					<table class="dialog-form-table" style="width: 100%">
						<tr>
							<th><label for="rs_id">საიდენტ. ნომერი</label></th>
							<td><input id="rs_id" type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['rs_id'] . '" /></td>
						</tr>
						<tr>
							<th><label for="name">დასახელება</label></th>
							<td><input id="name" type="text" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['name'] . '" /></td>
						</tr>
						<tr>
							<th><label for="address">იურიდიული მისამართი</label></th>
							<td><input id="address" type="text" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['address'] . '" /></td>
						</tr>
						<tr>
							<th><label for="legal_status">იურიდიული სტატუსი</label></th>
							<td><select id="legal_status" class="idls">' . GetLegalStatus($res[legal_status]) . '</select></td>
						</tr>
						<tr>
							<th><label for="pay_method">ანგარიშსწორების ფორმა</label></th>
							<td><select id="pay_method" class="idls">' . GetPayMethod($res[pay_method]) . '</select></td>
						</tr>
						<tr>
							<th><label for="vat_payer">დღგ</label></th>
							<td>
								<select id="vat_payer" class="idls small">' . GetVatStatus($res[vat_payer]) . '</select>
							</td>
						</tr>
					</table>
		        </fieldset>
		 	    <fieldset style="width: 316px; height: 192px; float: right;">
			    	<legend>კომპანიის ლოგოტიპი</legend>
					
			    	<table class="dialog-form-table" width="100%">
			    		<tr>
							<td id="img_colum" colspan="2">
								<img id="upload_img" src="media/uploads/images/client/' . $image . '">
							</td>
						</tr>
						<tr><!-- Upload Image -->
							<td id="act">
								<span>
									<a href="#" id="view_image" class="complate">View</a> | <a href="#" id="delete_image" class="delete">Delete</a>
								</span>
							</td>
							<td>
								<div class="file-uploader">
									<input id="choose_file" type="file" name="choose_file" class="input" style="display: none;">
									<button id="choose_button" class="center">აირჩიეთ ფაილი</button>
								</div>
							</td>
						</tr>
					</table>
		        </fieldset>
				<div class="clear"></div>
				<fieldset style="width: 450px; float: left;">
			    	<legend>კონტაკტი</legend>
					<table class="dialog-form-table" style="width: 100%">
						<tr>
							<th style="width: 205px;"><label for="contact_person">
							საკონტაქტო პირი</label></th>
							<td><input id="contact_person" type="text" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['contact_person'] . '" /></td>			
						</tr>
						<tr>
							<th><label for="phone_number">ტელ. ნომერი</label></th>
							<td><input id="phone_number" type="text" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['phone_number'] . '" /></td>
						</tr>
						<tr>
							<th><label for="mail">ელ-ფოსტა</label></th>
							<td><input id="mail" type="text" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['mail'] . '" /></td>
						</tr>
					</table>
		        </fieldset>
				<fieldset style="width: 316px; float: right;">
			    	<legend>შენიშვნა</legend>
		
			    	<table class="dialog-form-table" style="height: 90px;">
						<tr>
							<td valign="top">
								<textarea id="comment" class="idle large" cols="40" rows="4">' . $res['comment'] . '</textarea>
							</td>
						</tr>
					</table>
		        </fieldset>
			</div>
			<!-- /Main Info -->
									
			<!-- Account -->
			<div id="tabs-2">
				empty();
			</div>
			
			<!-- /Account -->
			<!-- Objects -->
			<div id="tabs-3">		
				<fieldset>		
				    <div class="inner-table">
					    <div id="dt_example" class="ex_highlight_row">
					        <div id="container" class="overhead_container">
					        	<div id="button_area">
					        		<button id="add_button_object">დამატება</button><button id="delete_button_object">წაშლა</button>
					        	</div>
					            <div id="dynamic">
					                <table class="display" id="object_list">
					                    <thead>
					                        <tr id="datatable_header">				                        
					                            <th>ID</th>
					                            <th style="width: 240px;">დასახელება</th>
					                            <th style="width: 100%">მისამართი</th>
												<th class="min">ქალაქი</th>										
												<th class="min">უბანი</th>
					                            <th class="check">#</th>
					                        </tr>
					                    </thead>
					                    <thead>
					                        <tr class="search_header">				                        
					                            <th class="colum_hidden">
					                            	<input type="text" name="search_id" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                            	<input type="text" name="search_name" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                            	<input type="text" name="search_address" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                            	<input type="text" name="search_city" value="ფილტრი" class="search_init" />
					                            </th>										
					                            <th>
					                            	<input type="text" name="search_distrinct" value="ფილტრი" class="search_init" />
					                            </th>										
												<th>
													<input type="checkbox" name="check-all" id="check-all-object">
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
			    <div id="add-edit-object-form" class="form-dialog" title="ობიექტი">
			    	<!-- aJax -->
				</div>
			</div>
			<!-- /Objects -->
			<!-- Pricelist Printer -->
			<div id="tabs-4">
				<fieldset>
				    <div class="inner-table">
					    <div id="dt_example" class="ex_highlight_row">
					        <div id="container" class="overhead_container">
					        	<div id="button_area">
					        		<button id="add_button_printer">დამატება</button><button id="delete_button_printer">წაშლა</button>
					        	</div>
					            <div id="dynamic">
					                <table class="display" id="printer_list">
					                    <thead>
					                        <tr id="datatable_header">				                        
					                            <th>ID</th>
					                            <th style="width: 40%">დასახელება</th>
					                            <th style="width: 60%">ტიპი</th>
					                            <th class="min">ფასი</th>
					                            <th class="check">#</th>
					                        </tr>
					                    </thead>
					                    <thead>
					                        <tr class="search_header">
					                            <th class="colum_hidden">
					                            	<input type="text" name="search_id" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                            	<input type="text" name="search_name" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                            	<input type="text" name="search_type" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                            	<input type="text" name="search_price" value="ფილტრი" class="search_init" />
					                            </th>
												<th>
													<input type="checkbox" name="check-all" id="check-all-printer">
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
			    <div id="add-edit-printer-form" class="form-dialog" title="პრინტერის ფასი">
			    	<!-- aJax -->
				</div>
			</div>
			<!-- /Pricelist Printer -->
			<!-- Pricelist Cartridge -->
			<div id="tabs-5">		
				<fieldset>		
				    <div class="inner-table">
					    <div id="dt_example" class="ex_highlight_row">
					        <div id="container" class="overhead_container">
					        	<div id="button_area">
					        		<button id="add_button_cartridge">დამატება</button><button id="delete_button_cartridge">წაშლა</button>
					        	</div>
					            <div id="dynamic">
					                <table class="display" id="cartridge_list">
					                    <thead>
					                        <tr id="datatable_header">				                        
					                            <th>ID</th>
					                            <th style="width: 100%">დასახელება</th>
					                            <th class="min">ფასი</th>
					                            <th class="check">#</th>
					                        </tr>
					                    </thead>
					                    <thead>
					                        <tr class="search_header">				                        
					                            <th class="colum_hidden">
					                            	<input type="text" name="search_id" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                            	<input type="text" name="search_prod" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                            	<input type="text" name="search_prod" value="ფილტრი" class="search_init" />
					                            </th>
												<th>
													<input type="checkbox" name="check-all" id="check-all-cartridge">
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
			<div id="tabs-6">	
		        <h1 style="margin-left: 45%; margin-bottom: 8px;">' . $c_date  .'</h1>
		        		
		        		
		        		
				<fieldset>		
				    <div class="inner-table">
					    <div id="dt_example" class="ex_highlight_row">
					        <div id="container" class="overhead_container">
					        	<div id="button_area">
					        	</div>
					            <div id="dynamic">
					                <table class="display" id="cartridzge_price">
					                    <thead>
												<tr style="font-size: 9pt;font-weight: normal;  height: 30px;  border:1px solid black; width: 100%;">
						        					<td rowspan="3" class="hidden">ID</td>		        		
													<td rowspan="2" style=" font-size: 11pt;font-weight: normal; vertical-align:middle; width:100px; text-align:center; border:1px solid #c5dbec;">კარტრიჯი</td>
													<td rowspan="2" colspan="6" style=" font-size: 11pt;font-weight: normal; vertical-align:middle; text-align:center;  border:1px solid #c5dbec;">ფასი</td> 
													<td rowspan="1" colspan="12" style="font-size: 10pt;font-weight: normal; vertical-align:middle; text-align:center; border:1px solid #c5dbec;">რაოდენობა</td>
													<td rowspan="1" colspan="3" style=" font-size: 10pt;font-weight: normal; vertical-align:middle; text-align:center; border:1px solid #c5dbec; width:170px;">თანხა</td>
													<td rowspan="1" colspan="2" style=" font-size: 10pt;font-weight: normal; vertical-align:middle; text-align:center; border:1px solid #c5dbec; width:80px;">გაყიდვები</td>
												</tr>        		
												<tr style="font-size: 9pt;font-weight: normal;  height: 30px; width: 100%;">
													<td rowspan="1" colspan="6" style=" font-size: 11pt;font-weight: normal; vertical-align:middle; text-align:center;  border:1px solid #c5dbec;">&nbsp;გეგმიური&nbsp;</td>        		
													<td rowspan="1" colspan="6" style=" font-size: 11pt;font-weight: normal; vertical-align:middle; text-align:center;  border:1px solid #c5dbec;">&nbsp;ფაქტიური&nbsp;</td>        		
													<td rowspan="2" colspan="1" style=" font-size: 9pt;font-weight: normal; vertical-align:middle; text-align:center;">&nbsp;გეგმიური&nbsp;</td>        		
													<td rowspan="2" colspan="1" style=" font-size: 9pt;font-weight: normal; vertical-align:middle; text-align:center;">&nbsp;ფაქტიური&nbsp;</td>
													<td rowspan="2" colspan="1" style=" font-size: 9pt;font-weight: normal; vertical-align:middle; text-align:center;">&nbsp;სხვაობა&nbsp;</td>        		
									        		<td rowspan="2" colspan="1" style=" font-size: 9pt;font-weight: normal; vertical-align:middle; text-align:center;">&nbsp;%&nbsp;</td>        		
													<td rowspan="2" colspan="1" style=" font-size: 9pt;font-weight: normal; vertical-align:middle; text-align:center;">&nbsp;თანხა&nbsp;</td>        		
									        		
									        	</tr>
												<tr  style="border:1px solid #c5dbec; height: 120px; width: 100%;">
													<td style="font-size: 11pt;font-weight: normal; vertical-align:middle; text-align:center;">მოდელი</td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">ტონერი</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">ბარაბანი</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">მაგნიტ. ლილ.</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">რეზინის PCR</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">გამწ. რაკელი</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">დოზ. რაკელი</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">ტონერი</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">ბარაბანი</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">მაგნიტ. ლილ</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">რეზინის PCR</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">გამწ. რაკელი</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">დოზ. რაკელი</span></td>	
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">ტონერი</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">ბარაბანი</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">მაგნიტ. ლილ</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">რეზინის PCR</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">გამწ. რაკელი</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">დოზ. რაკელი</span></td>	        		        																		
												</tr>
					                    </thead>
                                        <thead>
	                                                <tr class="search_header">
													<th class="colum_hidden">
													<input type="text" name="search_id" value="ფილტრი" class="search_init" />
										</th>
                         			    <th>
                             			 	<input type="text" id="search_catridge_model" name="search_catridge_model" value="ფილტრი" class="search_init" />
                           				 </th>
		        		                 <th>
		        		                 </th>
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>
		        		                 <th>
		        		                 </th>			        			
									  </thead>
                  					  <tfoot>
                     					  <tr>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>  
				                          <th>&nbsp;</th>                         
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>  
				                          <th>&nbsp;</th>                         
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>  
				                          <th>&nbsp;</th>                         
				                          <th>&nbsp;</th>		        				        		                       
				                          <th><p align="right" style="font-size:10px;">ჯამი:<br/>სულ ჯამი:</p></th>                          
				                          <th>&nbsp;</th>  
				                          <th>&nbsp;</th>    
				                          <th>&nbsp;</th>  
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>  		        				        		                                               
				                        </tr>
				                    </tfoot>		        		
					                </table>
					            </div>
					        </div>
					    </div>
					</div>
				</fieldset>	        			
		        <div style="padding: 2px;"></div>
				<fieldset>		
				    <div class="inner-table">
					    <div id="dt_example" class="ex_highlight_row">
					        <div id="container" class="overhead_container">
					        	<div id="button_area">
					        	</div>
					            <div id="dynamic">
					                <table class="display" id="printer_price">
					                    <thead>
												<tr style="font-size: 9pt;font-weight: normal;  height: 30px;  border:1px solid black; width: 100%;">
						        					<td rowspan="3" class="hidden">ID</td>		        		
													<td rowspan="2" style=" font-size: 11pt;font-weight: normal; vertical-align:middle; width:100px; text-align:center; border:1px solid #c5dbec;">პრინტერი</td>
													<td rowspan="2" colspan="6" style=" font-size: 11pt;font-weight: normal; vertical-align:middle; text-align:center;  border:1px solid #c5dbec;">ფასი</td> 
													<td rowspan="1" colspan="12" style="font-size: 10pt;font-weight: normal; vertical-align:middle; text-align:center; border:1px solid #c5dbec;">რაოდენობა</td>
													<td rowspan="1" colspan="3" style=" font-size: 10pt;font-weight: normal; vertical-align:middle; text-align:center; border:1px solid #c5dbec; width:200px;">თანხა</td>
													<td rowspan="1" colspan="2" style=" font-size: 10pt;font-weight: normal; vertical-align:middle; text-align:center; border:1px solid #c5dbec; width:90px;">გაყიდვები</td>
												</tr>        		
												<tr style="font-size: 9pt;font-weight: normal;  height: 30px; width: 100%;">
													<td rowspan="1" colspan="6" style=" font-size: 11pt;font-weight: normal; vertical-align:middle; text-align:center;  border:1px solid #c5dbec;">&nbsp;გეგმიური&nbsp;</td>        		
													<td rowspan="1" colspan="6" style=" font-size: 11pt;font-weight: normal; vertical-align:middle; text-align:center;  border:1px solid #c5dbec;">&nbsp;ფაქტიური&nbsp;</td>        		
													<td rowspan="2" colspan="1" style=" font-size: 9pt;font-weight: normal; vertical-align:middle; text-align:center;">&nbsp;გეგმიური&nbsp;</td>        		
													<td rowspan="2" colspan="1" style=" font-size: 9pt;font-weight: normal; vertical-align:middle; text-align:center; ">&nbsp;ფაქტიური&nbsp;</td>
													<td rowspan="2" colspan="1" style=" font-size: 9pt;font-weight: normal; vertical-align:middle; text-align:center;">&nbsp;სხვაობა&nbsp;</td>        		
									        		<td rowspan="2" colspan="1" style=" font-size: 9pt;font-weight: normal; vertical-align:middle; text-align:center;">&nbsp;%&nbsp;</td>        		
													<td rowspan="2" colspan="1" style=" font-size: 9pt;font-weight: normal; vertical-align:middle; text-align:center;">&nbsp;თანხა&nbsp;</td>        		
									        		
									        	</tr>
												<tr  style="border:1px solid #c5dbec; height: 130px; width: 100%;">
													<td style="font-size: 11pt;font-weight: normal; vertical-align:middle; text-align:center;">მოდელი</td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">თერმოფირი</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">საწნეხი ლილვ.</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">ფურც. ამტაცი</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">ბუშინგები</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">გაწმენდა</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">სხვა</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">თერმოფირი</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">საწნეხი ლილვ.</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">ფურც. ამტაცი</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">ბუშინგები</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">გაწმენდა</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">სხვა</span></td>	
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">თერმოფირი</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">საწნეხი ლილვ.</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">ფურც. ამტაცი</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">ბუშინგები</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">გაწმენდა</span></td>
													<td style="vertical-align: bottom !important; font-weight: normal;"><span class="rotate style2" style="font-size: 10pt !important; padding-left:10px;">სხვა</span></td>	        		        																		
												</tr>
					                    </thead>
                                        <thead>
	                                                <tr class="search_header">
													<th class="colum_hidden">
													<input type="text" name="search_id" value="ფილტრი" class="search_init" />
										</th>
                         			    <th>
                             			 	<input type="text" id="search_printer_model" name="search_printer_model" value="ფილტრი" class="search_init" />
                           				 </th>
		        		                 <th>
		        		                 </th>
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>	
		        		                 <th>
		        		                 </th>
		        		                 <th>
		        		                 </th>			        			
									  </thead>
                  					  <tfoot>
                     					  <tr>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>  
				                          <th>&nbsp;</th>                         
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>  
				                          <th>&nbsp;</th>                         
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>  
				                          <th>&nbsp;</th>                         
				                          <th>&nbsp;</th>		        				        		                       
				                          <th><p align="right" style="font-size:10px;">ჯამი:<br/>სულ ჯამი:</p></th>                          
				                          <th>&nbsp;</th>  
				                          <th>&nbsp;</th>    
				                          <th>&nbsp;</th>  
				                          <th>&nbsp;</th>
				                          <th>&nbsp;</th>  		        				        		                                               
				                        </tr>
				                    </tfoot>		        		
					                </table>
					            </div>
					        </div>
					    </div>
					</div>
				</fieldset>			     
			</div> 
			<div id="tabs-7">
				<fieldset>		
				    <legend>შესრულების გეგმა</legend>	
				    <div class="inner-table">
					    <div id="dt_example" class="ex_highlight_row">
					        <div id="container" class="overhead_container">
					        	<div id="button_area"> 
					        		<button id="add_cartridge_timetable">კარტრიჯი</button><button id="add_printer_timetable">პრინტერი</button><button id="delete_timetable">წაშლა</button>
					        	</div>
					            <div id="dynamic">
					                <table class="display" id="timetable_list">
					                    <thead>
					                        <tr id="datatable_header">				                        
					                            <th>ID</th>
					                            <th style="width: 100%">კატეგორია</th>
					                            <th style="width: 100%">შეკვეთის დრო</th>
		        								<th style="width: 100%">პროდ.<br>რაოდენობა</th>
		        								<th style="width: 100%">მიტანის დრო</th>		        		
					                            <th class="check">#</th>
					                        </tr>
					                    </thead>
					                    <thead>
					                        <tr class="search_header">				                        
					                            <th class="colum_hidden">
					                            	<input type="text" name="search_id" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                            	<input type="text" name="search_cat" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                            	<input type="text" name="search_request_time" value="ფილტრი" class="search_init" />
					                            </th>		        		
					                            <th>
					                            	<input type="text" name="search_prod_qunatity" value="ფილტრი" class="search_init" />
					                            </th>
					                            <th>
					                            	<input type="text" name="search_time" value="ფილტრი" class="search_init" />
					                            </th>		        		
												<th>
													<input type="checkbox" name="check-all" id="check-all-timetable">
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
		</div>	        		
		<!-- jQuery Dialog -->
	    <div id="add-edit-cartridge-form" class="form-dialog" title="კარტრიჯის ფასი">
	        <!-- aJax -->
	    </div>
		        		
	    <div id="add-edit-timetable-form" class="form-dialog" title="შესრულების გეგმა">
	        <!-- aJax -->
	    </div>		        				        		
		<!-- ID -->
		<input type="hidden" id="client_id" value="' . $res['id'] . '" />
		<input type="hidden" id="local_client_id" />
    </div>    
    ';
    
	return $data;
}
?>