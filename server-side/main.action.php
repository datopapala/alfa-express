<?php
/* ******************************
 *	Request aJax actions
 * ******************************
*/

require_once('../includes/classes/core.php');
$action = $_REQUEST['act'];
$error	= '';
$data	= '';
$id=$_REQUEST['id'];

switch ($action) {
	case 'get_add_page':
		$number		= $_REQUEST['number'];
		$page		= GetPage($res='', $number);
		$data		= array('page'	=> $page);
		
        break;
	case 'disable':
		mysql_query("DELETE FROM `request`
						WHERE id=$id");
break;
    case 'get_edit_page':
	    $req_id		= $_REQUEST['id'];
		$page		= GetPage(GetRequest($req_id));
        
        $data		= array('page'	=> $page);
        
        break;
 	case 'get_list' :
		$count = 		$_REQUEST['count'];
	    $hidden = 		$_REQUEST['hidden'];
	    
	    mysql_query("SET @i = 0;");
	    $rResult = mysql_query("SELECT `id`,
							  			@i := @i + 1 AS `iterator`,
                                       `date`,
                                       `phone`,
										CASE	WHEN `info_category` = '0'  THEN  'ინფორმაცია'
										WHEN `info_category` = '1'  THEN  'პრეტენზია'
										WHEN `info_category` = '2'  THEN  'სხვა'
								END 	AS requester
							    FROM   `request`");

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
    case 'save_request':
    	
		$req_id 		= $_REQUEST['id'];
		
		$arr = array(
				"req_num"			=> $_REQUEST['req_num'],	
				"req_data"			=> $_REQUEST['req_data'],
				"req_phone"			=> $_REQUEST['req_phone'],
				"info_category"		=> $_REQUEST['info_category'],
				"first_name"		=> $_REQUEST['first_name'], 
				"last_name"			=> $_REQUEST['last_name'], 
				"phone"				=> $_REQUEST['phone'],
				"content"			=> $_REQUEST['content']
		);
		
		
		if($req_id == ''){
			AddRequest($arr);
		}else {
			SaveRequest($req_id, $arr);
		}
        break;
    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Request Functions
 * ******************************
 */

function AddRequest($arr)

{
	mysql_query("INSERT INTO `request`
						(`request_number`,
						 `date`, `phone`,
						 `info_category`,
						 `first_name`,
						 `last_name`,
						 `u_phone`,
						 `content`) 
				 VALUES
						 ('$arr[req_num]',
						  '$arr[req_data]',
						  '$arr[req_phone]',
						  '$arr[info_category]',
						  '$arr[first_name]',
						  '$arr[last_name]',
						  '$arr[phone]',
						  '$arr[content]')");

}

function SaveRequest($req_id, $arr)
{

	mysql_query("	UPDATE	`request`
					SET		`info_category`		=  $arr[info_category],
							`first_name`		= '$arr[first_name]',
							`last_name`			= '$arr[last_name]',
							`phone`				= '$arr[req_phone]',
							`u_phone`			= '$arr[phone]',
							`content`			= '$arr[content]'
					WHERE	`id` = $req_id");

}

function GetRequest($req_id) 
{
    $res = mysql_fetch_assoc(mysql_query("SELECT `id`,
												 `request_number`,
												 `date`,
												 `phone`,
												 `info_category`,
												 `first_name`,
												 `last_name`,
    											 `u_phone`,
												 `content`
									      FROM   `request`
									      WHERE  `id` = $req_id" ));
	
	return $res;
}

function GetPage($res = '', $number)
{
	if (empty($res)) {
		$req_number = time();
		$c_date		= date("Y-m-d");
		$male		= 'checked';
		$famale		= '';
		
		$physicall	= 'checked';
		$legall		= '';
		
		$information= 'checked';
		$claim		= '';
		$other		= '';
	}else{
		$req_number = $res['request_number'];
		$c_date		= $res['date'];
		
		if($res['gender'] == 0){
			$male	= 'checked';
			$famale = '';			
		}else{
			$male	= '';
			$famale = 'checked';			
		}
		
		if($res['requester'] == 0){
			$physicall	= 'checked';
			$legall		= '';		
		}else{
			$physicall	= '';
			$legall		= 'checked';				
		}
		
		if($res['info_category'] ==0){
			$information= 'checked';
			$claim		= '';
			$other		= '';
		}else if($res['info_category'] == 1){
			$information= '';
			$claim		= 'checked';
			$other		= '';
		}else{
			$information= '';
			$claim		= '';
			$other		= 'checked';
		}
	}
	
	$data  = '
	<!-- jQuery Dialog -->
    <div id="add-edit-goods-form" title="საქონელი">
    	<!-- aJax -->
	</div>
	<div id="dialog-form">
		<fieldset style="margin-top: 5px;">
	    	<legend>ძირითადი ინფორმაცია</legend>

	    	<table width="80%" class="dialog-form-table">
				<tr>
					<td style="width: 120px;"><label for="req_num">მომართვა №</label></td>
					<td style="width: 120px;"><label for="req_data">თარიღი</label></td>
					<td style="width: 170px;"><label for="req_phone">ტელეფონი</label></td>
				</tr>
				<tr>
					<td>
						<input type="text" id="req_num" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $req_number . '" disabled="disabled" />
					</td>
					<td>
						<input type="text" id="req_data" class="idle date" onblur="this.className=\'idle date\'" onfocus="this.className=\'activeField date\'" value="' . $c_date . '" disabled="disabled" />
					</td>';
	
					$num = 0;
					if($res[phone]==""){
						$num=$number;
					} else { $num=$res[phone]; }
					
					$data.='
							
					<td>
						<input type="text" id="req_phone" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $num . '" />
					</td>
				</tr>
			</table>
        </fieldset>
		<fieldset style="margin-top: 14px; width: 49%; float: right;">
			<legend>ინფორმაციის კატეგორია</legend>
			
			<table class="dialog-form-table">
				<tr>
					<td><input id="information" type="radio" name="info_category" value="0" ' . $information . '></td>
					<td style="width: 100px;"><label for="information">ინფორმაცია</label></td>
					<td><input id="claim" type="radio" name="info_category" value="1" ' . $claim . '></td>
					<td style="width: 100px;"><label for="claim">პრეტენზია</label></td>
					<td><input id="other" type="radio" name="info_category" value="2" ' . $other . '></td>
					<td style="width: 100px;"><label for="other">სხვა</label></td>
				</tr>
			</table>
		</fieldset>
		<fieldset style="margin-top: 14px; width: 40%; float: left;">
	    	<legend>ფიზიკური პირი</legend>

	    	<table class="dialog-form-table">
	    		<tr>
					<td style="width: 170px;"><label for="first_name">სახელი</label></td>
					<td>
						<input type="text" id="first_name" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['first_name'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="last_name">გვარი</label></td>
					<td>
						<input type="text" id="last_name" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['last_name'] . '" />
					</td>
				</tr>
				
				<tr>
					<td style="width: 170px;"><label for="phone">ტელეფონი</label></td>
					<td>
						<input type="text" id="phone" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['u_phone'] . '" />
					</td>
				</tr>
				
			</table>
        </fieldset>
								
		<fieldset style="margin-top: 14px; width: 49%; float: right;">
	    	<legend>დამატებითი ინფორმაცია</legend>

	    	<table class="dialog-form-table">
	    		<tr>
					<td style="width: 150px;"><label for="content">საუბრის შინაარსი</label></td>
					<td>
						<textarea id="content" class="idle large" name="content" cols="40" rows="4">' . $res['content'] . '</textarea>
					</td>
				</tr>
			</table>
        </fieldset>';
	$data .= '
		<!-- ID -->
		<input type="hidden" id="req_id" value="' . $res['id'] . '" />
    </div>';
    
	return $data;
}



?>
