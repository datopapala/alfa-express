<?php
require_once('../../includes/classes/core.php');
$action	= $_REQUEST['act'];
$error	= '';
$data	= '';

$template_id 	= $_REQUEST['id'];
$template_name  = $_REQUEST['name'];
$content    	= $_REQUEST['content'];


switch ($action) {
	case 'get_add_page':
		$page		= GetPage();
		$data		= array('page'	=> $page);

		break;
	case 'get_edit_page':
		$template_id		= $_REQUEST['id'];
		$page		= GetPage(Gettemplate($template_id));
		$data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
			
		$rResult = mysql_query("SELECT 	template.id,
										template.`name`,
										template.`content`
							    FROM 	template
							    WHERE 	template.actived=1");

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
	case 'save_template':
		


		if($template_id != ''){
			Savetemplate($template_id, $template_name, $content);
			}
			else{
			if(!ChecktemplateExist($template_name, $template_id)){
				if ($template_id == '') {
					Addtemplate($template_name, $content);
				}else {
					
				$error = '"' . $template_name . '" უკვე არის სიაში!';

			}
		}
	}

		break;
	case 'disable':
		$template_id	= $_REQUEST['id'];
		Disabletemplate($template_id);

		break;
	default:
		$error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Category Functions
* ******************************
*/

function Addtemplate($template_name, $content)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO 	 	`template`
									(`user_id`,`name`, `content`, `actived`)
								VALUES 	
									('$user_id','$template_name', '$content', 1)");
}

function Savetemplate($template_id, $template_name, $content)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("	UPDATE `template`
					SET     `user_id`	='$user_id',
							`name` 		= '$template_name',
							`content`	= '$content'
					WHERE	`id` 		= $template_id");
}

function Disabletemplate($template_id)
{
	mysql_query("	UPDATE `template`
					SET    `actived` = 0
					WHERE  `id` = $template_id");
}

function ChecktemplateExist($template_name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
											FROM   `template`
											WHERE  `name` = '$template_name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}


function Gettemplate($template_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT  `id`,
													`name`,
													`content`
											FROM    `template`
											WHERE   `id` = $template_id" ));

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
					<td style="width: 170px;"><label for="CallType">სახელი</label></td>
					<td>
						<input type="text" id="name" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['name'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="CallType">საუბრის შინაარსი</label></td>
					<td colspan="6">	
						<textarea  style="width: 350px; resize: none;" id="content" class="idle" name="call_content" cols="300" rows="2">' . $res['content'] . '</textarea>
					</td>
				</tr>

			</table>
			<!-- ID -->
			<input type="hidden" id="template_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>

