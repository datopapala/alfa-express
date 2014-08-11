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
    	$list_id	= $_REQUEST['lid'];
    	$type		= $_REQUEST['type'];
		$page		= GetPage('', $list_id, $type);
		$data		= array('page'	=> $page);
		
        break;
    case 'get_edit_page':
	    $timetable_id	= $_REQUEST['id'];
    	$list_id		= $_REQUEST['lid'];
	    $page			= GetPage(GetTimetable($timetable_id), $list_id, '');
		
		$data		= array('page'	=> $page);
		
        break;
    case 'get_list':
	    $count		= $_REQUEST['count'];
	    $hidden		= $_REQUEST['hidden'];
	    $local_id	= $_REQUEST['local_id'];
	    
	    $rResult = mysql_query("SELECT	client_timetable.id,
										IF(ISNULL(cartridge_done_time), 'პრინტერი', 'კარტრიჯი') AS type,
										IF(ISNULL(request_end_time), CONCAT(request_start_time, '-დან'), CONCAT(request_end_time, '-მდე')) AS req_time,
							       		CONCAT(request_min_quantity,'-დან', ' - ', request_max_quantity, '-ჩათვლით') AS diff,
										CONCAT(
											IF(ISNULL(`days`),'',IF( `days` = 0,'იმავე დღეს ', CONCAT(`days`,' დღის შემდეგ ') ) ), IF(ISNULL(cartridge_done_time), printer_done_time, cartridge_done_time)  
										)		
								FROM	`client_timetable`
	    						WHERE	`client_timetable`.`client_id` = '$local_id'");
		
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
    case 'save_timetable':
		$timetable_id			= $_REQUEST['id'];		
		$arr = array(
				//Main
			"client_id"				=> $_REQUEST['cid'],
			"cartridge_done_time"	=> $_REQUEST['cdt'],
			"printer_done_time"		=> $_REQUEST['pdt'],
			"timetable_type"		=> $_REQUEST['t_type'],
			"request_time"			=> $_REQUEST['rt'],
			"request_time_type"		=> $_REQUEST['rtt'],
			"request_min_quantity"	=> $_REQUEST['rminq'],
			"request_max_quantity"	=> $_REQUEST['rmaxq'],
			"done_time"				=> $_REQUEST['dt'],
			"days"					=> $_REQUEST['d']
		);
		
		SaveTimtable($timetable_id, $user_id, $arr);

        break;
	case 'get_local_id':
		$local_id = GetLocalID();        
		$data = array('local_id' => $local_id);
        
		break;
    case 'disable':
    	$timetable_id			= $_REQUEST['id'];    	 
		Disable($timetable_id);
		
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

function SaveTimtable($timetable_id, $user_id, $arr){
	GLOBAL $error;			
	$result = mysql_query("INSERT IGNORE INTO `client_timetable` (`id`) VALUES ( $timetable_id )");
	if(!$result){
		$error = 'Invalid query: ' . mysql_error();
	}else{
		if( $arr[timetable_type] == 1 ){
			if( $arr[request_time_type] == 2){
				mysql_query("	UPDATE
										`client_timetable`
								SET
										`user_id`				= '$user_id',
										`client_id`				= '$arr[client_id]',
										`request_start_time`	= NULL,
										`request_end_time`		= '$arr[request_time]',
										`request_min_quantity`	= '$arr[request_min_quantity]',
										`request_max_quantity`	= '$arr[request_max_quantity]',
										`printer_done_time`		= '$arr[done_time]',
										`days`					= '$arr[days]'	
								WHERE
										`id`					= '$timetable_id'");				
				
			}else if($arr[request_time_type] == 1){
					mysql_query("	UPDATE
										`client_timetable`
									SET
										`user_id`				= '$user_id',
										`client_id`				= '$arr[client_id]',
										`request_start_time`	= '$arr[request_time]',
										`request_end_time`		= NULL,
										`request_min_quantity`	= '$arr[request_min_quantity]',
										`request_max_quantity`	= '$arr[request_max_quantity]',
										`printer_done_time`		= '$arr[done_time]',
										`days`					= '$arr[days]'
									WHERE
										`id`					= '$timetable_id'");									
			}else{
					mysql_query("	UPDATE
										`client_timetable`
									SET
										`user_id`				= '$user_id',
										`client_id`				= '$arr[client_id]',
										`request_start_time`	= NULL,
										`request_end_time`		= NULL,
										`request_min_quantity`	= NULL,
										`request_max_quantity`	= NULL,										
										`printer_done_time`		= '$arr[printer_done_time]',
										`days`					= NULL
									WHERE
										`id`					= '$timetable_id'");				
			}
		}else{
			if( $arr[request_time_type] == 2 ){
				mysql_query("	UPDATE
										`client_timetable`
								SET
										`user_id`				= '$user_id',
										`client_id`				= '$arr[client_id]',
										`request_start_time`	= NULL,										
										`request_end_time`		= '$arr[request_time]',
										`request_min_quantity`	= '$arr[request_min_quantity]',
										`request_max_quantity`	= '$arr[request_max_quantity]',
										`cartridge_done_time`	= '$arr[done_time]',
										`days`					= '$arr[days]'
									WHERE
										`id`					= '$timetable_id'");			
			}else if($arr[request_time_type] == 1){
				mysql_query("	UPDATE
										`client_timetable`
								SET
										`user_id`				= '$user_id',
										`client_id`				= '$arr[client_id]',
										`request_start_time`	= '$arr[request_time]',
										`request_end_time`		= NULL,										
										`request_min_quantity`	= '$arr[request_min_quantity]',
										`request_max_quantity`	= '$arr[request_max_quantity]',
										`cartridge_done_time`	= '$arr[done_time]',
										`days`					= '$arr[days]'
								WHERE
										`id`					= '$timetable_id'");				
			
			}else{
					mysql_query("	UPDATE
										`client_timetable`
									SET
										`user_id`				= '$user_id',
										`client_id`				= '$arr[client_id]',
										`request_start_time`	= NULL,
										`request_end_time`		= NULL,
										`request_min_quantity`	= NULL,
										`request_max_quantity`	= NULL,											
										`cartridge_done_time`	= '$arr[cartridge_done_time]',
										`days`					= NULL
									WHERE
										`id`					= '$timetable_id'");				
			}							
		}
	}
}

function GetLocalID(){
	GLOBAL $db;
	$local_id = $db->increment('client_timetable');
	
	return $local_id;	
}

function Disable($timetable_id){
	mysql_query("	DELETE
					FROM `client_timetable`
					WHERE	`id` = '$timetable_id'");
}

function GetTimetable($timetable_id)
{
	$res = mysql_fetch_assoc(mysql_query("SELECT	`client_timetable`.`id` as `id`,
													IF(ISNULL(request_end_time) AND ISNULL(request_start_time), IF(ISNULL(cartridge_done_time), HOUR(printer_done_time), HOUR(cartridge_done_time)), '') as `cpdone_hours`,
													IF(ISNULL(request_end_time) AND ISNULL(request_start_time), IF(ISNULL(cartridge_done_time), MINUTE(printer_done_time), MINUTE(cartridge_done_time)), '') as `cpdone_minutes`,
													CASE	WHEN ISNULL(request_end_time) AND ISNULL(request_start_time) THEN 0
															WHEN ISNULL(request_end_time) AND NOT ISNULL(request_start_time) THEN 1
															WHEN NOT ISNULL(request_end_time) AND ISNULL(request_start_time) THEN 2
													END as `request_time_type`,
													IF(ISNULL(request_end_time), HOUR(request_start_time), HOUR(request_end_time)) as `request_hours`,
													IF(ISNULL(request_end_time), MINUTE(request_start_time), MINUTE(request_end_time)) as `request_minutes`,
													request_min_quantity as `request_min_quantity`, 
													request_max_quantity as `request_max_quantity`,
													IF(ISNULL(cartridge_done_time), HOUR(printer_done_time), HOUR(cartridge_done_time)) AS `done_hours`,
													IF(ISNULL(cartridge_done_time), MINUTE(printer_done_time), MINUTE(cartridge_done_time)) AS `done_minute`,
													`client_timetable`.`days` as `days`,
													`client_timetable`.`printer_done_time` as `printer_done_time`
											FROM   	`client_timetable`
											WHERE   `id` = '$timetable_id'"));
	
	if( strlen( $res['cpdone_minutes'] ) == 1 ){
		$res['cpdone_minutes'] .='0';
	}
	
	if( strlen( $res['request_minutes']  ) == 1 ){
		$res['request_minutes'] .='0';
	}
	
	if( strlen( $res['done_minute']  ) == 1 ){
		$res['done_minute'] .='0';
	}
	
	return $res;
}

function GetPage($res = '', $id, $type)
{
	$res1 = mysql_fetch_assoc(mysql_query("	SELECT	`name`
											FROM	`client`
											WHERE	`client`.`id` = '$id'"));
	
	$data = '
	<div id="dialog-form">
	    <fieldset>
	    	<legend>ძირითადი ინფორმაცია</legend>
    		<table cellpadding="0" cellspacing="0" border="0" class="dialog-form-table">
				<tr>
					<td style="width: 170px;"><label for="client_name">კლიენტი :</label></td>
					<td>'. $res1['name'] .'</td>
				</tr>
			</table>
        </fieldset>
		<div style=" padding: 2px; "></div>					
	    <fieldset>
	    	<table class="dialog-form-table">							
				<tr>
					<td>';
						if( $type == '1' || $res['printer_done_time'] != ''){
							$data .= 'პრინტერის დაბრუნბე/მიტანის დრო : &nbsp;</td><td><input id="printer_done_hours" style="display:inline;" type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['cpdone_hours'] . '" /> :
																				 <input id="printer_done_minutes" style="display:inline;" type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['cpdone_minutes'] . '" />
									</td>';								
						}else{
							$data .= 'კარტრიჯის დაბრუნბე/მიტანის დრო : &nbsp;</td><td><input id="cartridge_done_hours" style="display:inline;" type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['cpdone_hours']  . '" /> :
																				 <input id="cartridge_done_minutes" style="display:inline;" type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['cpdone_minutes'] . '" />
									</td>';								
						}
												
	$data .= '		</td>
				</tr>																																									
			</table>';
	if( $res['cpdone_hours'] == '' &&  $res['cpdone_minutes'] == '' && $res['id'] != ''){
		$data .= '<div id="accordion">
				  <h3>მიწოდების გრაფიკი</h3>
				  <div>
					<div style=" margin-top: 2px; ">
						<table>
							<tr>
								<td style="text-align: center; width: 200px !important;">
									<label for="request_hours" style="display:block;">შეკვეთის დრო</label>
									<input id="request_hours" style="display:inline; width: 30px !important;" type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['request_hours']  . '" /> :
									<input id="request_minutes" style="display:inline; width: 30px !important;" type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['request_minutes'] . '" />
									<select id="request_time_type" class="idls" style="display:inline; width: 55px !important;">';
		if( $res['request_time_type'] == '2'){
			$data .= '
					<option value="0"></option>
					<option value="1">დან</option>
					<option value="2" selected>მდე</option>';
		}else if($res['request_time_type'] == '1'){
			$data .= '
					<option value="0"></option>
					<option value="1" selected>დან</option>
					<option value="2">მდე</option>';
		}else{
			$data .= '
					<option value="0"></option>
					<option value="1">დან</option>
					<option value="2">მდე</option>';
		}

		$data .= '					</select>
								</td>
								<td style="text-align: center; width: 200px !important;">
									<label for="cartridge_name" style="display:block;">კარტ. რაოდენობა</label>
									<input id="request_min_quantity" style="display:inline; width: 40px !important;"  type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['request_min_quantity'] . '" />–დან
									<input id="request_max_quantity" style="display:inline; width: 40px !important;"  type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['request_max_quantity'] . '" />–ჩათ
								</td>
								<td style="text-align: center; width: 45% !important;">
									<label for="done_time" style="display:block;">მიტანის დრო</label>
									<input id="days" style="display:inline; width: 30px !important;"  type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['days'] . '" /> დღის შემდეგ		
									<input id="done_hours" style="display:inline; width: 30px !important;"  type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['done_hours'] . '" /> :
									<input id="done_minute" style="display:inline; width: 30px !important;"  type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['done_minute'] . '" /> მდე
								</td>
							</tr>
						</table>
					</div>
				</div>
				</div>';		
	}else{
		$data .= '<div id="accordion">
				  <h3>მიწოდების გრაფიკი</h3>
				  <div>
					<div style=" margin-top: 2px; ">
						<table>
							<tr>
								<td style="text-align: center; width: 200px !important;">
									<label for="request_hours" style="display:block;">შეკვეთის დრო</label>
									<input id="request_hours" style="display:inline; width: 30px !important;" type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . '' . '" /> :
									<input id="request_minutes" style="display:inline; width: 30px !important;" type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . '' . '" />
									<select id="request_time_type" class="idls" style="display:inline; width: 55px !important;"><option value="0"></option> <option value="1">დან</option> <option value="2">მდე</option></select>
								</td>
								<td style="text-align: center; width: 200px !important;">
									<label for="cartridge_name" style="display:block;">კარტ. რაოდენობა</label>
									<input id="request_min_quantity" style="display:inline; width: 40px !important;"  type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . '' . '" />–დან
									<input id="request_max_quantity" style="display:inline; width: 40px !important;"  type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . '' . '" />–ჩათ
								</td>
								<td style="text-align: center; width: 45% !important;">
									<label for="done_time" style="display:block;">მიტანის დრო</label>
									<input id="days" style="display:inline; width: 30px !important;"  type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="0" /> დღის შემდეგ
									<input id="done_hours" style="display:inline; width: 30px !important;"  type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . '' . '" /> :
									<input id="done_minute" style="display:inline; width: 30px !important;"  type="text" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . '' . '" /> მდე
								</td>
							</tr>
						</table>
					</div>
				</div>
				</div>';	
	}
	$data .= '
        </fieldset>
		<!-- ID -->
		<input type="hidden" id="timetable_id" value="' . $res['id'] . '" />
		<input type="hidden" id="timetable_type" value="' . $type . '" />
    </div>
    ';
	return $data;
}
?>