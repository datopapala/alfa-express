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
    case 'get_edit_page':
	    $client_id		= $_REQUEST['id'];
	    $date			= $_REQUEST['date'];
	    
		$page		= GetPage($client_id, $date);
        
        $data 		= array('page'	=> $page);
        
        break;
    case 'get_add_page':
    	$id 		= $_REQUEST['id'];
    	$page		= GetCalientCallPage($id);
    	
    	$data 		= array('page'	=> $page);
    	    	
    	break;    
    case 'get_client_persons':
    	$client_name	= $_REQUEST['cn'];
    	$client_persons	= GetClientPersons($client_name, '');
    	
    	$data = array('client_persons' => $client_persons);
    	
    	break;
    case 'get_list':
	    $count = $_REQUEST['count'];
	    $hidden = $_REQUEST['hidden'];
	    
	    $rResult = mysql_query("SELECT		DISTINCT
											`client`.`id`,
											DATE(`services_degree`.`call_date`),
											`client`.`name`,
											IF
											( 
											(SELECT	COUNT(`cb`.`id`) FROM `client` as `c` LEFT JOIN	`client_objects` as `cb` ON `c`.`id` = `cb`.`client_id` WHERE `c`.`id` = `client`.`id`) > 1, `services_degree`.`client_comment`,`services_degree`.`comment`
											),
											`services_degree`.`degree_type` as `degree`,
											(SELECT	COUNT(`cb`.`id`) FROM `client` as `c` LEFT JOIN	`client_objects` as `cb` ON `c`.`id` = `cb`.`client_id` WHERE `c`.`id` = `client`.`id`) as `count`
								FROM 		`client`
								LEFT JOIN	`client_objects` ON `client`.`id` = `client_objects`.`client_id`
								RIGHT JOIN	`services_degree` ON `services_degree`.`client_object` = `client_objects`.`id`
								GROUP BY	`client`.`id`, DATE(`services_degree`.`call_date`)");
			    
		
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
						if( $aRow[$count] == '1' ){
							switch($row[$i]){
								case '1': $row[$i] = '<div style="background-color: green; width: 100%; height: 100%;"></div>'; break;
								case '2': $row[$i] = '<div style="background-color: yellow; width: 100%; height: 100%;"></div>'; break;
								case '3': $row[$i] = '<div style="background-color: red; width: 100%; height: 100%;"></div>'; break;
							}							
						}else{
							
							switch( GetAvarageDate($row[0],  $row[1]) ){
								case '1': $row[$i] = '<div style="background-color: green; width: 100%; height: 100%;"></div>'; break;
								case '2': $row[$i] = '<div style="background-color: yellow; width: 100%; height: 100%;"></div>'; break;
								case '3': $row[$i] = '<div style="background-color: red; width: 100%; height: 100%;"></div>'; break;
							}							
						}
										  
					}
				}
				$data['aaData'][] = $row;
			}
			
			$_log->logInfo('get_list');
		}
		
        break;        
    case 'save_client':
    	$client_id = $_REQUEST['cid'];
    	$client_comment = $_REQUEST['ccom'];
    	$date			= $_REQUEST['date'];
    	SaveClient($client_id, $client_comment, $date);
    	    	
    	break;
    	
    case 'get_client_id':
    	$client_name	= $_REQUEST['cn'];
    	$client_id		= GetClientID($client_name);
    	$data = array('client_id' => $client_id);
    	
    	break;
    
    case 'get_client_persons_info':
    	$person_id		= $_REQUEST['person_id'];
    	$client_info	= GetClientInfo($person_id);
    	$data 			= array('client_info' => $client_info);
    		 
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

function GetClientID($client_name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT		`client_objects`.`id`
											FROM		`client_objects`
											LEFT JOIN	`client` ON `client`.`id` = `client_objects`.`client_id`
											WHERE		CONCAT(`client`.`name`,'(',`client_objects`.`name`, ')') = '$client_name'"));
	return $res['id'];
}

function GetClientInfo($person_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT client_object_persons.phone_number AS `phone`,
													client_object_persons.mail AS `mail`
											FROM 	`client_object_persons`
											WHERE   client_object_persons.id = $person_id"));
	$arr = array($res['phone'], $res['mail']);
	return $arr;
}

function SaveClient($client_id, $client_comment, $date){
	mysql_query("	UPDATE		`client`
					INNER JOIN	`client_objects` ON `client_objects`.`client_id` = `client`.`id`
					INNER JOIN	`services_degree` ON `services_degree`.`client_object` = `client_objects`.`id`
					SET 		`services_degree`.`client_comment` = '$client_comment'
					WHERE		`client`.`id` = '$client_id' AND DATE(`services_degree`.`call_date`) = '$date'");
	
}

function GetAvarageDate($client_id,  $date){
	$resultAll = mysql_fetch_assoc( mysql_query("	SELECT		COUNT( `services_degree`.`client_object` ) as `count`
													FROM		`services_degree`
													INNER JOIN	`client_objects` ON `client_objects`.`id` = `services_degree`.`client_object`
													WHERE		`client_objects`.`client_id` = '$client_id' AND	DATE(`services_degree`.`call_date`) = '$date'"));
	
	$resultSet = mysql_fetch_assoc( mysql_query("	SELECT		COUNT( `services_degree`.`client_object` ) as `count`
													FROM		`services_degree`
													INNER JOIN	`client_objects` ON `client_objects`.`id` = `services_degree`.`client_object`
													WHERE		`client_objects`.`client_id` = '$client_id' AND	DATE(`services_degree`.`call_date`) = '$date' AND `services_degree`.`degree_type` = 1"));
	
	if( $resultSet['count'] / $resultAll['count'] >= 0.98 )
		return 1; 
	else if( $resultSet['count'] / $resultAll['count'] >= 0.80 )
		return 2;
	else
		return 3;
}

function GetServiceDegree($degree_type){
	$data = '';
	$req = mysql_query("SELECT	`id`,
								`name`
						FROM	`services_degree_catalog`");

	$data .= '<option value="' . 0 . '" selected="selected">' . '' . '</option>';

	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $degree_type){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}
	return $data;
}

function GetPersons($id){
	$data = '';
	$req = mysql_query("SELECT		services_degree.client_object_persons AS `id`,
									client_object_persons.`name` AS `name`
						FROM		`services_degree`
						LEFT JOIN 	client_object_persons ON client_object_persons.id = services_degree.client_object_persons
						WHERE  		`services_degree`.`id` = $id");

	$data .= '<option value="' . 0 . '" selected="selected">' . '' . '</option>';

	$data .= '<option value="0" selected="selected"></option>';
	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $id){
			
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}
	return $data;
}

function GetClientPersons($client_name, $point)
{
	$data = '';
	$req = mysql_query("SELECT		`client_object_persons`.`id`,
									`client_object_persons`.`name`
						FROM		`client_objects`
						LEFT JOIN	`client_object_persons` ON `client_objects`.`id` = `client_object_persons`.`client_object_id`
						LEFT JOIN    client ON client.id = client_objects.client_id
						WHERE		CONCAT(`client`.`name`,'(',`client_objects`.`name`, ')') = '$client_name'");

	$data .= '<option value="0" selected="selected"></option>';
	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $point){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}
	return $data;
}

function GetCall($id){
	$res = mysql_fetch_assoc(mysql_query("SELECT	`services_degree`.`id` as `id`,
													CONCAT(`client`.`name`,'(',`client_objects`.`name`, ')') as `client`,
													`client_object_persons`.`name` as `persons`,
													`client_object_persons`.`phone_number` as `phone`,
													`client_object_persons`.`mail` as `mail`,
													`services_degree`.`degree_type` as `degree_type`,
													`services_degree`.`call_date` as `call_date`,
													`services_degree`.`comment` as `comment`
										FROM		`services_degree`
										LEFT JOIN	`client_object_persons` ON `client_object_persons`.`id` = `services_degree`.`client_object_persons`
										LEFT JOIN	`client_objects` ON `client_objects`.`id` = `services_degree`.`client_object`
										LEFT JOIN	`client` ON `client`.`id` = `client_objects`.`client_id`
										WHERE		`services_degree`.`id` = '$id'"));
	return $res;
}

function GetLocalID( $id ){
	GLOBAL $db;
	$id = $db->increment('services_degree');
	return $id;	
}

function GetCalientCallPage( $id ){
	if( $id == '' ){
		$res['id']	= GetLocalID();
	}else{
		$res		= GetCall($id);
	}
	$data = '
	<div id="dialog-form">
	    <fieldset>
	    	<legend>ძირითადი ინფორმაცია</legend>
    		<table class="dialog-form-table">
				<tr>
					<td style="width: 170px;"><label for="client_object">კლიენტი</label></td>
					<td>
						<div class="seoy-row" id="client_object_seoy">
							<input type="text" id="client_object" class="idle seoy-large" onblur="this.className=\'idle seoy-large\'" onfocus="this.className=\'activeField seoy-large\'" value="' . $res['client'] . '" />
							<button id="client_object_btn" class="combobox">client_object</button>
						</div>
					</td>
				</tr>		
				<tr>
					<td style="width: 170px;"><label for="per_contact_person">საკონტაქტო პირი</label></td>
					<td>
						<select id="persons" class="idls date_time" style="width: 165px !important;">'. GetPersons($res['id'])  .'</select>
					</td>
				</tr>	
																			
				<tr>
					<td style="width: 170px;"><label for="per_phone_number">ტელ. ნომერი</label></td>
					<td>
						<input type="text" id="per_phone_number" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['phone'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="per_mail">ელ-ფოსტა</label></td>
					<td>
						<input type="text" id="per_mail" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['mail'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="service_degree">მომსახურების დონე</label></td>
					<td>
						<select id="service_degree" class="idls date_time" style="width: 165px !important;">'. GetServiceDegree($res['degree_type'])  .'</select>
					</td>
				</tr>
				<tr>
					<td style="width: 170px;" valign="top"><label for="call_date">თარიღი</label></td>								
					<td><input id="call_date" type="text" class="idle date_time" onblur="this.className=\'idle date_time\'" onfocus="this.className=\'activeField date_time\'" value="' . $res['call_date'] . '" /></td>
				</tr>	
				<tr class="comment">
					<td style="width: 170px;" valign="top"><label for="per_comment">შენიშვნა</label></td>
					<td>
						<textarea id="per_comment" class="idle large" cols="40" rows="3">' . $res['comment'] . '</textarea>
					</td>
				</tr>
			</table>
			<!-- ID -->
			<input type="hidden" id="servise_degree_id" value="' . $res['id'] . '" />
			<input type="hidden" id="client_name" value="" />
        </fieldset>
    </div>';
	return $data;	
}

function GetPage($client_id, $date)
{
	$res = mysql_fetch_assoc( mysql_query( "SELECT		`client`.`name`,
														`services_degree`.`client_comment` as `comment`
											FROM		`client` 
											LEFT JOIN	`client_objects` ON	`client_objects`.`client_id` = `client`.`id`
											LEFT JOIN	`services_degree` ON `services_degree`.`client_object` = `client_objects`.`id`
											WHERE		`client`.`id` = '$client_id' && `client_objects`.`actived` = 1 && DATE(`services_degree`.`call_date`) = '$date'
											GROUP BY	`client`.`id`"));
	$data = '
	<div id="dialog-form">
		<fieldset>	
			<legend>ძირითადი ინფორმაცია</legend>	
				<table width="100%" class="dialog-form-table" cellpadding="10px" >								
					<tr align="center">
						<th>
							<label for="client_object">კლიენტი :<span style="border-bottom: 1px solid #000; padding: 0 30px; margin-left: 50px;">'.$res['name'].'</span></label>
						</th>
						<th>
							<label for="client_comment">შენიშვნა : </label>
						</th>
						<th>
							<textarea id="client_comment" class="idle large" cols="40" rows="3">' . $res['comment'] . '</textarea>									
						</th>																										
					</tr>
				</table>			
		</fieldset>				
		<fieldset>	
			<legend>ობიექტები</legend>	
		    <div class="inner-table">
			    <div id="dt_example" class="ex_highlight_row">
			        <div id="container" class="overhead_container">
			        	<div id="button_area">
							<button id="add_object_call">დამატება</button>									
			        	</div>
			            <div id="dynamic">
			                <table class="display" id="object_list">
			                    <thead>
			                        <tr id="datatable_header">				                        
			                            <th>ID</th>
			                            <th style="width: 100%">ფილიალი</th>
										<th class="min">საკონტაქტო პირი</th>
										<th class="min">ტელ. ნომერი</th>	
										<th style="width: 100%">შენიშვნა</th>					
										<th class="min">კმაყოფილების<br>მაჩვენებელი</th>
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
			                            	<input type="text" name="search_contact" value="ფილტრი" class="search_init" />
			                            </th>
			                            <th>
			                            	<input type="text" name="search_tel" value="ფილტრი" class="search_init" />
			                            </th>
			                            <th>
			                            </th>			
			                        </tr>
			                    </thead>
			                </table>
			            </div>
			        </div>
			    </div>
			</div>
		</fieldset>			        		
		<!-- ID -->
		<input type="hidden" id="client_id" value="' . $client_id . '" />
		
		<input type="hidden" id="date" value="' . $date . '" /> 
    </div>    
    ';
    
	return $data;
}
?>