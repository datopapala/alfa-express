<?php
/* ******************************
 *	Client Object Person List aJax actions
 * ******************************
 */

include('../../../../includes/classes/core.php');

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
		$page		= GetPage(GetCall($list_id));
		
		$data		= array('page'	=> $page);

        break;
    case 'get_list':
	    $count		= $_REQUEST['count'];
	    $hidden		= $_REQUEST['hidden'];
	    $local_id	= $_REQUEST['id'];
	    
	    $data = array(
	    		"aaData"	=> array()
	    );
	    
	    if (!empty($local_id)) {
		    $rResult = mysql_query("SELECT		`services_degree`.`id`,
												`client_object_persons`.`name`,
												`services_degree`.`comment`,
												`services_degree`.`call_date`,
												`services_degree`.`degree_type` AS `degree`
									FROM		`services_degree`
									LEFT JOIN	`client_object_persons` ON `client_object_persons`.`id` = `services_degree`.`client_object_persons`
									WHERE		`services_degree`.`client_object` = '$local_id' AND	`services_degree`.`actived` = 1");
			

	
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
							switch($row[$i]){								
								case '1': $row[$i] = '<div style="background-color: green; width: 100%; height: 100%;"></div>'; break;	
								case '2': $row[$i] = '<div style="background-color: yellow; width: 100%; height: 100%;"></div>'; break;
								case '3': $row[$i] = '<div style="background-color: red; width: 100%; height: 100%;"></div>'; break;
							}
						}
					}
					$data['aaData'][] = $row;
				}
			}
	    }
	    
        break;
    case 'save_call':
    	$service_degree_id	= $_REQUEST['id'];
    	$client_object_id	= $_REQUEST['cid'];    	
		$person_name		= $_REQUEST['p'];		
		$service_degree_t	= $_REQUEST['d'];
		$comment			= $_REQUEST['c'];		
		
		if($service_degree_id == ''){
			AddCall($user_id, $client_object_id, $person_name, $service_degree_t, $comment);
		}else{
			SaveCall($service_degree_id, $user_id, $client_object_id, $person_name, $service_degree_t, $comment);
		}

        break;
    case 'getperson':
    	$pers_name = $_REQUEST['n'];
    	$array = GetPerson($pers_name);
    	$data		= array(
    			'phone'	=> $array[0],
    			'email' => $array[1]
    	);
    	
    	break;
    case 'disable':
		$list_id = $_REQUEST['id'];
		Disable($list_id);
		
        break;
    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Client Object Person List Functions
 * ******************************
 */

function GetPerson($person_name){
	$res = mysql_fetch_assoc( mysql_query("	SELECT	phone_number,
													mail
											FROM	client_object_persons
											WHERE	client_object_persons.`name` = '$person_name'"));
	
	$arr = array(
			"0"				=> $res['phone_number'],
			"1"				=> $res['mail']
	);
	return $arr;
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

function AddCall($user_id, $client_object_id, $person_name, $service_degree_t, $comment)
{
	$res = mysql_fetch_assoc( mysql_query("	SELECT	`id`
											FROM	client_object_persons
											WHERE	client_object_persons.`name` = '$person_name'"));
	$c_date = date('Y-m-d H:i:s');
	mysql_query("INSERT	`services_degree`
						(`user_id`, `client_object`, `client_object_persons`, `call_date`, `degree_type`, `comment`)
				VALUES	
						($user_id,$client_object_id,$res[id],'$c_date',$service_degree_t,'$comment')");
}

function SaveCall($service_degree_id, $user_id, $client_object_id, $person_name, $service_degree_t, $comment)
{
	$res = mysql_fetch_assoc( mysql_query("	SELECT	`id`
											FROM	client_object_persons
											WHERE	client_object_persons.`name` = '$person_name'"));	
	$c_date = date('Y-m-d H:i:s');
	mysql_query("	UPDATE	`services_degree`
					SET
					    `user_id`				= '$user_id',
					    `client_object`			= '$client_object_id',
					    `client_object_persons`	= '$res[id]',
					    `call_date`				= '$c_date',
					    `degree_type`			= '$service_degree_t',
					    `comment`				= '$comment'					    					    
				    WHERE
				    	`id` = '$service_degree_id'	");
}

function Disable($list_id)
{
    mysql_query("	UPDATE
				    	`services_degree`
				    SET
					    `actived`	= 0
				    WHERE
				    	`id` = '$list_id'");
}

function GetCall($list_id)
{
    $res = mysql_fetch_assoc(mysql_query("SELECT	`services_degree`.`id` as `id`,
													`client_object_persons`.`name` as `name`,
													`client_object_persons`.`phone_number` as `phone`,
													`client_object_persons`.`mail` as `mail`,
													`services_degree`.`degree_type` as `degree_type`,
													`services_degree`.`comment` as `comment`
										FROM		`services_degree`
										LEFT JOIN	`client_object_persons` ON `client_object_persons`.`id` = `services_degree`.`client_object_persons`
										WHERE		`services_degree`.`id` = '$list_id'"));
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
					<td style="width: 170px;"><label for="per_contact_person">საკონტაქტო პირი</label></td>
					<td>			
						<div class="seoy-row" id="per_contact_person_seoy">
							<input type="text" id="per_contact_person" class="idle seoy-large" onblur="this.className=\'idle seoy-large\'" onfocus="this.className=\'activeField seoy-large\'" value="' . $res['name'] . '" style=" width: 250px !important; "/>
							<button id="per_contact_person_btn" class="combobox">per_contact_person</button>
						</div>			
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
					<td style="width: 170px;"><label for="service_degree">მომსახურების დომე</label></td>
					<td>
						<select id="service_degree" class="idls date_time" style="width: 165px !important;">'. GetServiceDegree($res['degree_type'])  .'</select>							
					</td>
				</tr>								
				<tr class="comment">
					<td style="width: 170px;" valign="top"><label for="per_comment">შენიშვნა</label></td>
					<td>
								olhouigo
						<textarea id="per_comment" class="idle large" cols="40" rows="3">' . $res['comment'] . '</textarea>
					</td>
				</tr>
			</table>
			<!-- ID -->
			<input type="hidden" id="servise_degree_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>';
	return $data;
}

?>