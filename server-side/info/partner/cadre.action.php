<?php
/* ******************************
 *	Partner Cadre aJax actions
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
	    $cadre_id	= $_REQUEST['id'];
		$page		= GetPage(GetCadre($cadre_id));		
		$data		= array('page'	=> $page);

        break;
    case 'get_list':
	    $count = $_REQUEST['count'];
	    $hidden = $_REQUEST['hidden'];
	    $partner_id = $_REQUEST['partner_id'];
	    
	    $rResult = mysql_query("SELECT	`id`,
									  	`person_id`,
									  	`f_name`,
									  	`l_name`,
									  	`position`
								FROM	`partner_persons`
								WHERE 	`partner_id` = $partner_id && `actived`=1");	    
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
    case 'save_cadre':
		$cadre_id 			= $_REQUEST['pcid'];
		$partner_id 		= $_REQUEST['pid'];
					
    	$person_id			= $_REQUEST['cui'];
		$f_name	 			= $_REQUEST['cn'];
		$l_name				= $_REQUEST['cln'];
		$cadre_position		= $_REQUEST['cp'];
		$cadre_contact		= $_REQUEST['cc'];
		$cadre_phone		= $_REQUEST['cph'];
		$cadre_m_phone		= $_REQUEST['cmp'];
		
		$cadre_sale		= $_REQUEST['cs'];
		$cadre_limit		= $_REQUEST['cl'];
		
		if( $cadre_id == ''){
			AddCadre( $partner_id, $user_id, $person_id, $f_name, $l_name, $cadre_position, $cadre_phone, $cadre_m_phone, $cadre_contact, $cadre_sale, $cadre_limit);
		}else{
			SaveCadre($cadre_id, $partner_id, $user_id, $person_id, $f_name, $l_name, $cadre_position, $cadre_phone, $cadre_m_phone, $cadre_contact, $cadre_sale, $cadre_limit);
		}
		
        break;
    case 'disable':
		$cadre_id 		= $_REQUEST['id'];
		disable($cadre_id);	
				
        break;
    default:
       $error = 'Action is Null';
}

$data['error'] = $error;
echo json_encode($data);


/* ******************************
 *	Partner Cadre Functions
 * ******************************
 */

function AddCadre( $partner_id, $user_id, $person_id, $f_name, $l_name, $cadre_position, $cadre_phone, $cadre_m_phone, $cadre_contact, $cadre_sale, $cadre_limit)
{
	mysql_query("INSERT INTO `partner_persons`
					(`partner_id`, `user_id`, `person_id`,`f_name`, `l_name`, `position`, `phone`, `m_phone`, `chontacter`, `sale_rate`, `sale_limit`) 
				 VALUES
				 	($partner_id, $user_id, '$person_id', '$f_name', '$l_name', '$cadre_position', '$cadre_phone', '$cadre_m_phone', $cadre_contact, '$cadre_sale', '$cadre_limit')");
}

function SaveCadre($cadre_id, $partner_id, $user_id, $person_id, $f_name, $l_name, $cadre_position, $cadre_phone, $cadre_m_phone, $cadre_contact, $cadre_sale, $cadre_limit)
{
	mysql_query("UPDATE
	    			`partner_persons`
				 SET
				 	`partner_id` 	= $partner_id,
				 	`user_id` 		= $user_id,
				    `person_id` 	= '$person_id',
				    `f_name`		= '$f_name',
				    `l_name`		= '$l_name',
				    `position`		= '$cadre_position',
				    `phone` 		= '$cadre_phone',
				    `m_phone` 		= '$cadre_m_phone',
				    `chontacter` 	= $cadre_contact,
				    `sale_rate`		= '$cadre_sale',
				    `sale_limit`	= '$cadre_limit'
				 WHERE
					`id` 			= $cadre_id");
}

function disable($cadre_id){
	mysql_query("UPDATE		`partner_persons`
				 SET		`actived` = 0
				 WHERE 		`id` = $cadre_id");
}	
		
function Contact($point)
{
	$data='';
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

function GetCadre($cadre_id) 
{
    $res = mysql_fetch_assoc(mysql_query("SELECT	`id`,
    												`person_id`,
											    	`f_name`,
											    	`l_name`,
											    	`position`,
											   		`chontacter`,
											   		`phone`,
											   		`m_phone`,
    												`chontacter`,
											   		`sale_rate`,
											   		`sale_limit`
									      FROM `partner_persons`
									      WHERE `id` = $cadre_id &&	`actived`=1"));
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
					<td style="width: 170px;"><label for="cadre_user_id">პირადი №</label></td>
					<td>
						<input type="text" id="cadre_user_id" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['person_id'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="cadre_name">სახელი</label></td>
					<td>
						<input type="text" id="cadre_name" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['f_name'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="cadre_lname">გვარი</label></td>
					<td>
						<input type="text" id="cadre_lname" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['l_name'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="cadre_position">თანამდებობა</label></td>
					<td>
						<input type="text" id="cadre_position" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['position'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="cadre_contact">საკონტაქტო</label></td>
					<td>
						<select id="cadre_contact" class="idls small">' . Contact($res['chontacter'],$res['id']) . '</select>
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="cadre_phone">ტელეფონი</label></td>
					<td>
						<input type="text" id="cadre_phone" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['phone'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="cadre_m_phone">მობილური</label></td>
					<td>
						<input type="text" id="cadre_m_phone" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['m_phone'] . '" />
					</td>
				</tr>
			</table>
	    </fieldset>
	    <fieldset>		    
	    	<legend>ფასდაკლება</legend>
    		<table class="dialog-form-table">
				<tr>
					<td style="width: 170px;"><label for="cadre_sale">ფასდაკლების %</label></td>
					<td>
						<input type="text" id="cadre_sale" class="idle num" onblur="this.className=\'idle num\'" onfocus="this.className=\'activeField num\'" value="' . $res['sale_rate'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="cadre_limit">ლიმიტი</label></td>
					<td>
						<input type="text" id="cadre_limit" class="idle num" onblur="this.className=\'idle num\'" onfocus="this.className=\'activeField num\'" value="' . $res['sale_limit'] . '" />
					</td>
				</tr>
			</table>
			<!-- ID -->
			<input type="hidden" id="partner_cadre_id" value="' . $res['id'] . '" />
	    </fieldset>
    </div>
    ';
	return $data;
}

?>