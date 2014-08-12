<?php
require_once('../../includes/classes/core.php');
$action	= $_REQUEST['act'];
$error	= '';
$data	= '';

switch ($action) {
	case 'get_add_page':
		$page		= GetPage();
		$data		= array('page'	=> $page);

		break;
	case 'get_edit_page':
		$content_id		= $_REQUEST['id'];
	       $page		= GetPage(Getcontent($content_id));
           $data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
		 
		$rResult = mysql_query("SELECT 	content.id,
										content.`name`
							    FROM 	content
							    WHERE 	content.actived=1");

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
	case 'save_content':
		$content_id 		= $_REQUEST['id'];
		$content_name    = $_REQUEST['name'];
		
	
		
		if($content_name != ''){
			if(!CheckcontentExist($content_name, $content_id)){
				if ($content_id == '') {
					Adddecontent( $content_id, $content_name);
				}else {
					Savedecontent($content_id, $content_name);
				}
								
			} else {
				$error = '"' . $content_name . '" უკვე არის სიაში!';
				
			}
		}
		
		break;
	case 'disable':
		$content_id	= $_REQUEST['id'];
		Disablecontent($content_id);

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

function Adddecontent($content_id, $content_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO 	 `content`
								(`name`,`user_id`)
					VALUES 		('$content_name', '$user_id')");
}

function Savedecontent($content_id, $content_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("	UPDATE `content`
					SET     `name` = '$content_name',
							`user_id` ='$user_id'
					WHERE	`id` = $content_id");
}

function Disablecontent($content_id)
{
	mysql_query("	UPDATE `content`
					SET    `actived` = 0
					WHERE  `id` = $content_id");
}

function CheckcontentExist($content_name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
											FROM   `content`
											WHERE  `name` = '$content_name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}


function Getcontent($content_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT  `id`,
													`name`
											FROM    `content`
											WHERE   `id` = $content_id" ));

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

			</table>
			<!-- ID -->
			<input type="hidden" id="content_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
