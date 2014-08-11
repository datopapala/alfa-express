<?php
/* ******************************
 *	Client Object List aJax actions
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
		$page		= GetPage(GetObjectList($list_id));
		
		$data		= array('page'	=> $page);

        break;
    case 'get_list':
	    $count		= $_REQUEST['count'];
	    $hidden		= $_REQUEST['hidden'];
	    $local_id	= $_REQUEST['local_id'];
	    
	    $rResult = mysql_query("SELECT		`client_objects`.`id`,
											`client_objects`.`name`,
											CONCAT(`client_objects`.address_name , ' ', `address_types`.`name` , ' ', `client_objects`.`address_number` ),
											`cities`.`name` as `city`,	    		
											`districts`.`name` as `districts`
								FROM		`client_objects` 
								LEFT JOIN 	`client` ON	`client_objects`.`client_id` = `client`.`id`
								LEFT JOIN	`postal_codes` ON `postal_codes`.`id` = `client_objects`.`postal_code_id`
								LEFT JOIN	`districts` ON `districts`.`id` = `postal_codes`.`district_id`
								LEFT JOIN	`address_types` ON `address_types`.`id` = `client_objects`.`address_type`
								LEFT JOIN	`cities` ON `cities`.`id` = `client_objects`.`region`
								WHERE		`client`.`id` = '$local_id' && `client_objects`.`actived` = 1");
		
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
    case 'save_object':
		$object_id		= $_REQUEST['id'];
		$local_id		= $_REQUEST['lci'];
		
		$arr = array(
				"name"				=> htmlspecialchars($_REQUEST['on'], ENT_QUOTES),
				"address_name"		=> htmlspecialchars($_REQUEST['oan'], ENT_QUOTES),
				"city"				=> htmlspecialchars($_REQUEST['oct'], ENT_QUOTES),				
				"address_type"		=> htmlspecialchars($_REQUEST['oat'], ENT_QUOTES),
				"address_number"	=> htmlspecialchars($_REQUEST['oanum'], ENT_QUOTES),				
				"postal_code_id"	=> GetPostalCodeID( htmlspecialchars($_REQUEST['opc'], ENT_QUOTES) ),				
				"contact_person"	=> htmlspecialchars($_REQUEST['ocp'], ENT_QUOTES),
				"phone_number"		=> htmlspecialchars($_REQUEST['opn'], ENT_QUOTES),
				"mail"				=> htmlspecialchars($_REQUEST['om'], ENT_QUOTES),
				"comment"			=> htmlspecialchars($_REQUEST['oc'], ENT_QUOTES)
		);
		
		if($object_id == ''){
			AddObjectList($user_id, $local_id, $arr);
		}else{
			SaveObjectList($object_id, $user_id, $arr);
		}

        break;
    case 'disable':
		$list_id = $_REQUEST['id'];
		DisableObjectList($list_id);
		
        break;
    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Client Object List Functions
 * ******************************
 */

function AddObjectList($user_id, $local_id, $arr)
{
	mysql_query("INSERT INTO `client_objects`
					(`user_id`, `client_id`, `name`, `region`, `address_name`, `address_type`, `address_number`, `postal_code_id`, `contact_person`, `phone_number`, `mail`, `comment`) 
				 VALUES
					($user_id, $local_id, '$arr[name]', '$arr[city]', '$arr[address_name]', '$arr[address_type]', '$arr[address_number]', '$arr[postal_code_id]', '$arr[contact_person]', '$arr[phone_number]', '$arr[mail]', '$arr[comment]')");
}

function SaveObjectList($object_id, $user_id, $arr) 
{
	mysql_query("UPDATE
	    			`client_objects`
				 SET
					`user_id`			= $user_id,
					`name`				= '$arr[name]',
					`region`			= '$arr[city]',
					`address_name`		= '$arr[address_name]',
					`address_type`		= '$arr[address_type]',
					`address_number`	= '$arr[address_number]',											
					`postal_code_id`    = '$arr[postal_code_id]',			
					`contact_person`	= '$arr[contact_person]',
					`phone_number`		= '$arr[phone_number]',
					`mail`				= '$arr[mail]',
					`comment`			= '$arr[comment]'
				 WHERE
					`id` = $object_id");
}

function DisableObjectList($list_id)
{
    mysql_query("	UPDATE
				    	`client_objects`
				    SET
					    `actived`	= 0
				    WHERE
				    	`id` = $list_id");
}

function GetObjectList($list_id) 
{
    $res = mysql_fetch_assoc(mysql_query("	SELECT	`client_objects`.`id` as `id`,
													`client_objects`.`name` as `name`,
    												`client_objects`.`region` as `region`, 
 													`client_objects`.`address` as `address`,   		
													`client_objects`.`address_name` as `address_name`,
													`client_objects`.`address_type` as `address_type`,
													`client_objects`.`address_number` as `address_number`,
													CONCAT(`postal_codes`.`code`, ' - ', `postal_codes`.`name`) as `postal_code`,
													`client_objects`.`contact_person` as `contact_person`,
													`client_objects`.`phone_number` as `phone_number`,
													`client_objects`.`mail` as `mail`,
													`client_objects`.`comment` as `comment`
											FROM		`client_objects`
											LEFT JOIN	`postal_codes` ON `postal_codes`.`id` = `client_objects`.`postal_code_id`
											WHERE		`client_objects`.`id` = '$list_id'"));
	return $res;
}

function GetPostalCodeID( $postal_code  ){
	$res = mysql_fetch_assoc( mysql_query( "SELECT	`postal_codes`.`id` as `id`
											FROM	`postal_codes`
											WHERE	CONCAT(`postal_codes`.`code`, ' - ', `postal_codes`.`name`) = '$postal_code'" ));
	return $res['id'];	
}

function GetCities( $object_City ){
	$data = '';
	$req = mysql_query("SELECT	`cities`.`id` as `id`,
								`cities`.`name` as `name`
						FROM	`cities`
						ORDER BY `cities`.`name`");
		
	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $object_City){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}
	return $data;	
}

function GetAddressTypes( $object_Addtype ){
	$data = '';
	$req = mysql_query("SELECT	`address_types`.`id` as `id`,
								`address_types`.`name` as `name`
						FROM	`address_types`");
	
	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $object_Addtype){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}
	
	return $data;	
}

function GetPage($res = '')
{
	$data = '
	<div id="dialog-form">
	    <fieldset>
	    	<legend>ძირითადი ინფორმაცია</legend>
    		<table class="dialog-form-table">
				<tr>
					<td style="width: 170px;"><label for="obj_name">დასახელება</label></td>
					<td>
						<input type="text" id="obj_name" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['name'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="obj_city">ქალაქი</label></td>
					<td>
						<select id="obj_city" style="width: 166px;">' .GetCities( $res['region']  ).'</select>															
					</td>
				</tr>																
				<tr>
					<td style="width: 170px;"><label for="obj_address">მისამართი</label></td>
					<td>
						<div class="seoy-row" id="obj_address_seoy">
							<table>
								<tr>
									<td>
										<input type="text" id="obj_address" style="width : 200px !important;" class="idle seoy-large" onblur="this.className=\'idle seoy-large\'" onfocus="this.className=\'activeField seoy-large\'" value="' . $res['address_name'] . '" />
										<button id="obj_address_btn" class="combobox">obj_address</button>									
									</td>
									<td>
										<select id="obj_address_type" style="margin-left: 10px; width: 80px;">' .GetAddressTypes( $res['address_type']  ).'</select>												
									</td>												
									<td>
										<label for="obj_address_number" style="margin-left: 10px;">№:</label>
									</td>
									<td>
										<input type="text" id="obj_address_number" style="width : 30px !important; margin-left: 10px;" class="idle seoy-large" onblur="this.className=\'idle seoy-large\'" onfocus="this.className=\'activeField seoy-large\'" value="' . $res['address_number'] . '" />
									</td>											
								</tr>
							</table>
						</div>								
					</td>
				</tr>
				<tr>
					<th><label for="postal_code">საფოსტო ინდექსი</label></th>
					<td>
						<div class="seoy-row" id="postal_code_seoy">
							<input type="text" id="postal_code" class="idle seoy-large" onblur="this.className=\'idle seoy-large\'" onfocus="this.className=\'activeField seoy-large\'" value="' . $res['postal_code'] . '" />
							<button id="postal_code_btn" class="combobox">postal_code</button>
						</div>
					</td>
				</tr>								
				<tr class="comment">
					<td style="width: 170px;" valign="top"><label for="obj_comment">შენიშვნა</label></td>
					<td>
						<textarea id="obj_comment" class="idle large" cols="40" rows="3">' . $res['comment'] . '</textarea>
					</td>
				</tr>
			</table>
        </fieldset>
		<fieldset>
	    	<legend>თანამშრომლები</legend>
		    <div class="inner-table">
			    <div id="dt_example" class="ex_highlight_row">
			        <div id="container" class="overhead_container">
			        	<div id="button_area">
			        		<button id="add_button_person">დამატება</button><button id="delete_button_person">წაშლა</button>
			        	</div>
			            <div id="dynamic">
			                <table class="display" id="person_list">
			                    <thead>
			                        <tr id="datatable_header">				                        
			                            <th>ID</th>
			                            <th style="width: 40%">სახელი</th>
			                            <th style="width: 60%">ტელ. ნომერი</th>
			                            <th class="min">ელ-ფოსტა</th>
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
		<!-- ID -->
		<input type="hidden" id="object_list_id" value="' . $res['id'] . '" />
				
		<!-- jQuery Dialog -->
	    <div id="add-edit-person-form" class="form-dialog" title="თანამშრომელი">
	    	<!-- aJax -->
		</div>
    </div>
    ';
	return $data;
}

?>