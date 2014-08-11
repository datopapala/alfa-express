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
		$page		= GetPage(GetPersonList($list_id));
		
		$data		= array('page'	=> $page);

        break;
    case 'get_list':
	    $count		= $_REQUEST['count'];
	    $hidden		= $_REQUEST['hidden'];
	    $local_id	= $_REQUEST['local_id'];
	    
	    $data = array(
	    		"aaData"	=> array()
	    );
	    
	    if (!empty($local_id)) {
		    $rResult = mysql_query("SELECT	`id`,
		    								`name`,
		    								`phone_number`,
		    								`mail`
									FROM	`client_object_persons`
		    						WHERE	`client_object_id` = '$local_id' && `actived` = 1");
			

	
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
			}
	    }
	    
        break;
    case 'save_person':
		$person_id	= $_REQUEST['id'];
		$local_id	= $_REQUEST['lci'];
		
		$arr = array(
				"name"				=> htmlspecialchars($_REQUEST['pcp'], ENT_QUOTES),
				"phone_number"		=> htmlspecialchars($_REQUEST['ppn'], ENT_QUOTES),
				"mail"				=> htmlspecialchars($_REQUEST['pm'], ENT_QUOTES),
				"comment"			=> htmlspecialchars($_REQUEST['pc'], ENT_QUOTES)
		);
		
		if($person_id == ''){
			AddPersonList($user_id, $local_id, $arr);
		}else{
			SavePersonList($person_id, $user_id, $arr);
		}

        break;
    case 'disable':
		$list_id = $_REQUEST['id'];
		DisablePersonList($list_id);
		
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

function AddPersonList($user_id, $local_id, $arr)
{
	mysql_query("INSERT INTO `client_object_persons`
					(`user_id`, `client_object_id`, `name`, `phone_number`, `mail`, `comment`) 
				 VALUES
					($user_id, $local_id, '$arr[name]', '$arr[phone_number]', '$arr[mail]', '$arr[comment]')");
}

function SavePersonList($person_id, $user_id, $arr) 
{
	mysql_query("UPDATE
	    			`client_object_persons`
				 SET
					`user_id`			= $user_id,
					`name`				= '$arr[name]',
					`phone_number`		= '$arr[phone_number]',
					`mail`				= '$arr[mail]',
					`comment`			= '$arr[comment]'
				 WHERE
					`id` = $person_id");
}

function DisablePersonList($list_id)
{
    mysql_query("	UPDATE
				    	`client_object_persons`
				    SET
					    `actived`	= 0
				    WHERE
				    	`id` = $list_id");
}

function GetPersonList($list_id) 
{
    $res = mysql_fetch_assoc(mysql_query("	SELECT	`id`,
    												`name`,
													`phone_number`,
    												`mail`,
    												`comment`
											FROM	`client_object_persons`
											WHERE	`id` = $list_id"));
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
						<input type="text" id="per_contact_person" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['name'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="per_phone_number">ტელ. ნომერი</label></td>
					<td>
						<input type="text" id="per_phone_number" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['phone_number'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="per_mail">ელ-ფოსტა</label></td>
					<td>
						<input type="text" id="per_mail" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['mail'] . '" />
					</td>
				</tr>
				<tr class="comment">
					<td style="width: 170px;" valign="top"><label for="per_comment">შენიშვნა</label></td>
					<td>
						<textarea id="per_comment" class="idle large" cols="40" rows="3">' . $res['comment'] . '</textarea>
					</td>
				</tr>
			</table>
			<!-- ID -->
			<input type="hidden" id="object_person_list_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>';
	return $data;
}

?>