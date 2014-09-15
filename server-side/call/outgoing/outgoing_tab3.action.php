<?php
/* ******************************
 *	Request aJax actions
 * ******************************
*/

require_once ('../../../includes/classes/core.php');
$action = $_REQUEST['act'];
$error	= '';
$data	= '';

$task_id				= $_REQUEST['id'];
$call_date				= $_REQUEST['call_date'];
$phone					= $_REQUEST['phone'];
$problem_comment 		= $_REQUEST['problem_comment'];
$call_duration 			= $_REQUEST['call_duration'];
$priority_id			= $_REQUEST['priority_id'];
$comment 	        	= $_REQUEST['comment'];
$hidden_inc				= $_REQUEST['hidden_inc'];
$edit_id				= $_REQUEST['edit_id'];
$delete_id				= $_REQUEST['delete_id'];

// file
$rand_file				= $_REQUEST['rand_file'];
$file					= $_REQUEST['file_name'];


switch ($action) {
	case 'get_add_page':
		$page		= GetPage();
		$data		= array('page'	=> $page);
		
        break;
    case 'get_edit_page':
	  
		$page		= GetPage(Getincomming($task_id));
        
        $data		= array('page'	=> $page);
        
        break;
	
 	case 'get_list' :
		$count		= $_REQUEST['count'];
	   	$hidden		= $_REQUEST['hidden'];
	    $user_id	= $_REQUEST['user_id'];
	    $user		= $_SESSION['USERID'];
	    
	    $group		= checkgroup($user);
	    
	    $filter = '';
	    if ($group != 2) {
	    	$filter = 'AND outgoing_call.responsible_user_id ='. $user;
	    }
	     
	    $rResult = mysql_query("SELECT 	 	`task`.id,
											`task`.id,
											`site_user`.`name`,
											`site_user`.`pin`,
											`person1`.`name` ,
											`person2`.`name` ,
											`incomming_call`.date,
											`status`.`call_status`
								FROM 		task			
								LEFT JOIN 		incomming_call ON task.incomming_call_id=incomming_call.id
								LEFT JOIN 	site_user		ON incomming_call.id=site_user.incomming_call_id
								
								
								JOIN 		users AS `user1`			ON task.responsible_user_id=user1.id
								JOIN 		persons AS `person1`		ON user1.person_id=person1.id
								
								JOIN 		users AS `user2`			ON task.user_id=user2.id
								JOIN 		persons AS `person2`		ON user2.person_id=person2.id
								
								LEFT JOIN `status`  	ON	task.`status`= `status`.id
								
								WHERE 		task.task_type_id=1 AND task.`status`=3");
	    
										    		
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
			}
			$data['aaData'][] = $row;
		}

        break;
        
        case 'delete_file':
        
        	mysql_query("DELETE FROM file WHERE id = $delete_id");
        
        	$increm = mysql_query("	SELECT  `name`,
        			`rand_name`,
        			`id`
        			FROM 	`file`
        			WHERE   `task_id` = $edit_id
        			");
        
        	$data1 = '';
        
        	while($increm_row = mysql_fetch_assoc($increm))	{
        	$data1 .='<tr style="border-bottom: 1px solid #85b1de;">
				          <td style="width:110px; display:block;word-wrap:break-word;">'.$increm_row[name].'</td>
        			<td ><button type="button" value="media/uploads/file/'.$increm_row[rand_name].'" style="cursor:pointer; border:none; margin-top:25%; display:block; height:16px; width:16px; background:none;background-image:url(\'media/images/get.png\');" id="download" ></button><input type="text" style="display:none;" id="download_name" value="'.$increm_row[rand_name].'"> </td>
        					<td ><button type="button" value="'.$increm_row[id].'" style="cursor:pointer; border:none; margin-top:25%; display:block; height:16px; width:16px; background:none; background-image:url(\'media/images/x.png\');" id="delete"></button></td>
 					  </tr>';
		}
        
		$data = array('page' => $data1);
        
        		break;
        
                		case 'up_now':
                		$user		= $_SESSION['USERID'];
                		if($rand_file != ''){
                		mysql_query("INSERT INTO 	`file`
                				( 	`user_id`,
                				`task_id`,
                					`name`,
                					`rand_name`
                					)
                					VALUES
                					(	'$user',
                					'$edit_id',
                					'$file',
                					'$rand_file'
                				);");
                				}
        
                				$increm = mysql_query("	SELECT  `name`,
                				`rand_name`,
                				`id`
                				FROM 	`file`
                				WHERE   `task_id` = $edit_id
                				");
        
                				$data1 = '';
        
                				while($increm_row = mysql_fetch_assoc($increm))	{
                				$data1 .='<tr style="border-bottom: 1px solid #85b1de;">
                					<td style="width:110px; display:block;word-wrap:break-word;">'.$increm_row[name].'</td>
        		<td ><button type="button" value="media/uploads/file/'.$increm_row[rand_name].'" style="cursor:pointer; border:none; margin-top:25%; display:block; height:16px; width:16px; background:none;background-image:url(\'media/images/get.png\');" id="download" ></button><input type="text" style="display:none;" id="download_name" value="'.$increm_row[rand_name].'"> </td>
				          <td ><button type="button" value="'.$increm_row[id].'" style="cursor:pointer; border:none; margin-top:25%; display:block; height:16px; width:16px; background:none; background-image:url(\'media/images/x.png\');" id="delete"></button></td>
        				          </tr>';
		}
        
		$data = array('page' => $data1);
        
		break;
    case 'save_outgoing':
	
		$user_id		= $_SESSION['USERID'];
		
		Savetask($task_id, $problem_comment, $file, $rand_file);
        break;
    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	taskFunctions
 * ******************************
 */
function checkgroup($user){
	$res = mysql_fetch_assoc(mysql_query("
			SELECT users.group_id
			FROM    users
			WHERE  users.id = $user
			"));
	return $res['group_id'];

}



function Savetask($task_id, $problem_comment, $file, $rand_file)
{
	$c_date		= date('Y-m-d H:i:s');
	$user  = $_SESSION['USERID'];
	mysql_query("UPDATE `task` SET  
								`user_id`				='$user',
								`problem_comment`		='$problem_comment', 
								`status`	='2', 
								`actived`	='1'
								 WHERE `id`				='$task_id'
									");

}
function Savesite_user($incom_id, $personal_pin, $name, $personal_phone, $mail,  $personal_id)
{

	$user  = $_SESSION['USERID'];
	mysql_query("UPDATE 	`site_user`
									SET
									`site`						='243',
									`pin`						='$personal_pin',
									`name`						='$name',
									`phone`						='$personal_phone',
									`mail`						='$mail',
									`personal_id`				='$personal_id',
									`user`						='$user'
									WHERE `incomming_call_id`	='$incom_id'
		
	");

}




function Getcall_status($status)
{
$data = '';
$req = mysql_query("SELECT 	`id`, `call_status`
					FROM 	`status`
					WHERE 	actived=1");


	$data .= '<option value="0" selected="selected">----</option>';
	while( $res = mysql_fetch_assoc($req)){

	if($res['id'] == $status){
		$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['call_status'] . '</option>';
} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['call_status'] . '</option>';
}
}
	return $data;
}
function Getpay_type($pay_type_id)
{
$data = '';
$req = mysql_query("SELECT 	`id`, `name`
					FROM 	`pay_type`
					WHERE 	actived=1");


		$data .= '<option value="0" selected="selected">----</option>';
		while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $pay_type_id){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
			} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
		}

		return $data;
	}
	function Get_bank($bank_id)
	{
	$data = '';
	$req = mysql_query("SELECT 	`id`, `name`
						FROM 	`bank`
						WHERE 	actived=1");


		$data .= '<option value="0" selected="selected">----</option>';
		while( $res = mysql_fetch_assoc($req)){
			if($res['id'] == $bank_id){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
	} else {
				$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
			}
	}

	return $data;
	}

function Getcard_type($card_type_id)
		{
		$data = '';
		$req = mysql_query("SELECT 	`id`, `name`
							FROM 	`card_type`
							WHERE 	actived=1");


	$data .= '<option value="0" selected="selected">----</option>';
		while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $card_type_id){
		$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}

	return $data;
		}
function Getcard_type1($card_type1_id)
		{
			$data = '';
			$req = mysql_query("SELECT 	`id`, `name`
								FROM 	`card_type`
								WHERE 	actived=1");


	$data .= '<option value="0" selected="selected">----</option>';
	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $card_type1_id){
		$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
			} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}

	return $data;
}
function Getpay_aparat($pay_aparat_id)
	{
	$data = '';
	$req = mysql_query("SELECT 	`id`, `name`
						FROM 	`pay_aparat`
						WHERE 	actived=1");

	$data .= '<option value="0" selected="selected">----</option>';
	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $pay_aparat_id){
		$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
	} else {
	$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
	}
	}

	return $data;
	}
function Getobject($object_id)
{
	$data = '';
	$req = mysql_query("SELECT 	`id`, `name`
						FROM 	`object`
						WHERE 	actived=1");


		$data .= '<option value="0" selected="selected">----</option>';
	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $object_id){
		$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
		$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
		}

	return $data;
		}
		function Getcategory($category_id)

{

	$data = '';
	$req = mysql_query("SELECT `id`, `name`
						FROM `category`
						WHERE actived=1 && parent_id=0 ");


$data .= '<option value="0" selected="selected">----</option>';
	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $category_id){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
}
}

return $data;
}

function Getcategory1($category_id)
{

		$data = '';
	$req = mysql_query("SELECT `id`, `name`
						FROM `category`
						WHERE actived=1 && parent_id=$category_id");

$data .= '<option value="0" selected="selected">----</option>';
while( $res = mysql_fetch_assoc($req)){
if($res['id'] == $category_id){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
} else {
$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
}
}

		return $data;

}

function Getcategory1_edit($category_id)
{

		$data = '';
		$req = mysql_query("SELECT `id`, `name`
							FROM `category`
							WHERE actived=1 && id=$category_id");

$data .= '<option value="0" selected="selected">----</option>';
while( $res = mysql_fetch_assoc($req)){
if($res['id'] == $category_id){
$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
} else {
$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}

	return $data;

}

function Getcall_type($call_type_id)
{
	$data = '';
		$req = mysql_query("SELECT `id`, `name`
							FROM `call_type`
							WHERE actived=1");

	$data .= '<option value="0" selected="selected">----</option>';
	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $call_type_id){
	$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
	} else {
		$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}

	return $data;
		}


function Getdepartment($task_department_id)
{
	$data = '';
	$req = mysql_query("SELECT `id`, `name`
						FROM `department`
						WHERE actived=1 AND id=37  ");

		while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $task_department_id){
		$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
		$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}

	return $data;
}


function Getpriority($priority_id)
		{
		$data = '';
		$req = mysql_query("SELECT `id`, `name`
							FROM `priority`
							WHERE actived=1 ");

						$data .= '<option value="0" selected="selected">----</option>';
						while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $priority_id){
		$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
						} else {
							$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}

	return $data;
}


function Gettemplate($template_id)
{
$data = '';
	$req = mysql_query("SELECT `id`, `name`
						FROM `template`
						WHERE actived=1 ");

							$data .= '<option value="0" selected="selected">----</option>';
							while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $template_id){
		$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
							} else {
							$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
		}

	return $data;
}


		function Gettask_type($task_type_id)
		{
		$data = '';
		$req = mysql_query("SELECT `id`, `name`
							FROM `task_type`
							WHERE actived=1 ");

		$data .= '<option value="0" selected="selected">----</option>';
	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $task_type_id){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
		$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}

	return $data;
}


function Getpersons($persons_id)
{
	$data = '';
	$req = mysql_query("SELECT `id`, `name`
						FROM `persons`
						WHERE actived=1 ");

		$data .= '<option value="0" selected="selected">----</option>';
	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $persons_id){
		$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
		$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}

	return $data;
}
function Getpersonss($persons_id)
{
	$data = '';
	$req = mysql_query("SELECT `id`, `name`
						FROM `persons`
						WHERE actived=1 ");

	$data .= '<option value="0" selected="selected">----</option>';
	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $persons_id){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}

	return $data;
}

function getCalls(){
		$db1 = new sql_db ( "212.72.155.175", "root", "Gl-1114", "asteriskcdrdb" );

	$req = mysql_query("

	SELECT  	DISTINCT
	IF(SUBSTR(cdr.src, 1, 3) = 995, SUBSTR(cdr.src, 4, 9), cdr.src) AS `src`
						FROM    	cdr
						GROUP BY 	cdr.src
						ORDER BY 	cdr.calldate DESC
						LIMIT 		12


						");

	$data = '<tr class="trClass">
					<th class="thClass">#</th>
					<th class="thClass">ნომერი</th>
					<th class="thClass">ქმედება</th>
	</tr>
	';
	$i	= 1;
	while( $res3 = mysql_fetch_assoc($req)){

		$data .= '
	    		<tr class="trClass">
					<td class="tdClass">' . $i . '</td>
			<td class="tdClass" style="width: 30px !important;">' . $res3['src'] . '</td>
					<td class="tdClass" style="font-size: 13px !important;"><button class="insert" number="' . $res3['src'] . '">დამატება</button></td>
					</tr>';
		$i++;
	}

	return $data;


}function Getincomming($task_id)
{
$res = mysql_fetch_assoc(mysql_query("	SELECT		task.id AS `id`,
													incomming_call.id AS `call_id`,
													IF(ISNULL(task.phone), incomming_call.phone, task.phone) AS `phone`,
													IF(ISNULL(incomming_call.date), task.date, incomming_call.date) AS call_date,
													incomming_call.call_type_id AS call_type_id,
													incomming_call.call_category_id AS category_id,
													IF(ISNULL(task.`status`), 3, task.`status`) AS `status`,
													incomming_call.call_subcategory_id AS category_parent_id,
													incomming_call.problem_date ,
													incomming_call.call_content AS call_content,
													incomming_call.pay_type_id AS pay_type_id,
													incomming_call.bank_id AS bank_id,
													incomming_call.bank_object_id AS bank_object_id,
													incomming_call.card_type_id AS card_type_id,
													incomming_call.card_type_id AS card_type1_id,
													incomming_call.pay_aparat_id AS pay_aparat_id,
													incomming_call.object_id AS object_id,
													site_user.`name` AS `name`,
													site_user.mail AS mail,
													site_user.personal_id AS personal_id,
													site_user.phone AS personal_phone,
													site_user.pin AS personal_pin,
													site_user.friend_pin AS friend_pin,
													site_user.`name` AS `name1`,
													site_user.`mail` AS `mail`,
													site_user.`user` AS `user`,
													task.task_type_id AS task_type_id,
													task.responsible_user_id AS persons_id,
													task.priority_id AS priority_id,
													task.planned_end_date AS planned_end_date,
													task.fact_end_date   AS fact_end_date,
													task.call_duration   AS 	call_duration,
													task.department_id AS task_department_id,
													task.phone AS phone,
													task.`comment` AS `comment`,
													task.problem_comment AS problem_comment,
													template.id AS template_id

										FROM 	   	task
										LEFT JOIN  	incomming_call  ON incomming_call.id = task.incomming_call_id
										LEFT JOIN  	site_user ON incomming_call.id = site_user.incomming_call_id
										LEFT JOIN  	template ON task.template_id = template.id
										WHERE      	task.id = $task_id
			" ));
	
	return $res;
}


function GetPage($res='', $number)
{
	$num = 0;
	if($res[phone]==""){
		$num=$number;
	}else{ 
		$num=$res[phone]; 
	}
	$increm = mysql_query("	SELECT  `name`,
			`rand_name`
			FROM 	`file`
			WHERE   `task_id` = $res[id]
			");
	if ($res[call_id] == '') {
		
		$data  .= '<div id="dialog-form">
							<div style="float: left; width: 500px;">
								<fieldset >
							    	<legend>ძირითადი ინფორმაცია</legend>
						
							    	<table width="100%" class="dialog-form-table">
										<tr>
											<td style="width: 180px;"><label for="req_num">დავალების №</label></td>
											<td style="width: 180px !important;"><label for="req_data">ფორმირების თარიღი</label></td>
											<td style="width: 180px;"><label for="req_phone">ტელეფონი</label></td>
										</tr>
										<tr>
											<td style="width: 180px;">
												<input type="text" id="id" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['id']. '" disabled="disabled" />
											</td>
											<td style="width: 180px;">
												<input type="text" id="c_date" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField date\'" value="' .  $res['call_date']. '" disabled="disabled" />
											</td>
											<td style="width: 180px;">
												<input type="text" id="phone" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' .  $res['phone'] . '" />
											</td>
											<td style="width: 69px;">
												<button class="calls">ნომრები</button>
											</td>
										</tr>
														
										<tr>
											<td style="width: 180px;"><label for="req_num">შესრულების გეგმ. დრო</label></td>
											<td style="width: 180px !important;"><label for="req_data">შესრულების ფაქტ. დრო</label></td>
											<td style="width: 180px;"><label for="req_phone">შესრულების დრო</label></td>
										</tr>
										<tr>
											<td style="width: 180px;">
												<input type="text" id="planned_end_date" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' .  $res['planned_end_date']. '" disabled="disabled" />
											</td>
											<td style="width: 180px;">
												<input type="text" id="fact_end_date" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' .  $res['fact_end_date']. '" disabled="disabled" />
											</td>
											<td style="width: 180px;">
												<input type="text" id="call_duration" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['call_duration'] . '" disabled="disabled" />
											</td>
											<td style="width: 69px;">
											</td>
										</tr>';
				
						$data  .= '<table class="dialog-form-table">
										<tr>
											<td style="width: 180px;"><label for="d_number">დავალების ტიპი</label></td>
											<td style="width: 180px;"><label for="d_number">განყოფილება</label></td>
											<td style="width: 180px;"><label for="d_number">პასუხისმგებელი პირი</label></td>
											
										</tr>
							    		<tr>
											<td style="width: 180px;"><select id="task_type_id" class="idls object"disabled="disabled" disabled="disabled">'.Gettask_type($res['task_type_id']).'</select></td>
											<td style="width: 180px;"><select id="task_department_id" class="idls object"disabled="disabled">'. Getdepartment($res['task_department_id']).'</select></td>
											<td style="width: 180px;"><select id="persons_id" class="idls object" disabled="disabled">'.Getpersonss($res['persons_id']).'</select></td>
											
										</tr>
										<tr>
											<td style="width: 180px;"><label for="d_number">პრიორიტეტები</label></td>
											<td style="width: 180px;"><label for="d_number">თემა</label></td>
											<td style="width: 180px;"></td>
										</tr>
										<tr>
											<td style="width: 180px;"><select id="priority_id" class="idls object" disabled="disabled">'.Getpriority($res['priority_id']).'</select></td>
											<td style="width: 180px;"><select id="template_id" class="idls object" disabled="disabled">'.Gettemplate($res['template_id']).'</select></td>
											<td style="width: 180px;"></td>
										</tr>
										<tr>
											<td style="width: 150px;"><label for="content">კომენტარი</label></td>
											<td style="width: 150px;"><label for="content"></label></td>
											<td style="width: 150px;"><label for="content"></label></td>
										</tr>
													
										
										<tr>
						
											<td colspan="6">
												<textarea  style="width: 641px; resize: none;" id="comment" class="idle" name="call_content" cols="300" rows="2" disabled="disabled">' . $res['comment'] . '</textarea>
											</td>
										</tr>
										<tr>
											<td style="width: 180px;"><label for="content">საუბრის შინაარსი</label></td>
										</tr>
										<tr>
								
											<td colspan="6">
												<textarea style="width: 641px; resize: none;" id="problem_comment" class="idle" name="call_content" cols="300" rows="2" disabled="disabled">' . $res['problem_comment'] . '</textarea>
											</td>
										</tr>
									</table>
						        </fieldset>
							</div>
							<div>
								  </fieldset>
							</div>
							<div style="float: right;  width: 355px;">
								 <fieldset>
									<legend>მომართვის ავტორი</legend>
									<table style="height: 243px;">
										<tr>
											<td style="width: 180px;">PIN კოდი</td>
											<td style="width: 180px;">ეგობრის PIN ცოდი</td>
										</tr>
										<tr>
											<td style="width: 180px;"><input type="text" id="personal_pin" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['personal_pin']  . '" /></td>
											<td style="width: 180px;"><input type="text" id="personal_pin" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['friend_pin']  . '" /></td>
											
										</tr>
										<tr>
											<td style="width: 180px;">პირადი ნომერი</td>
											<td style="width: 180px;"></td>
										</tr>
										<tr>
											<td style="width: 180px;">
												<input type="text" id="personal_id" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['personal_id'] . '" />
											</td>
											<td style="width: 180px;"></td>
										</tr>
										<tr>
											<td style="width: 180px;">სახელი და გვარი</td>
											</td>
										</tr>
										<tr >
											<td style="width: 180px;">' . $res['name1'] . '</td>
											</td>
										</tr>
										<tr >
											<td style="width: 180px;">ტელეფონი</td>
											</td>
										</tr>
										<tr>
											<td style="width: 180px;">' . $res['personal_phone'] . '</td>
											</td>
										</tr>
										<tr>
											<td style="width: 180px;">ელ-ფოსტა</td>
											</td>
										</tr>
										<tr>
											<td style="width: 180px;">' . $res['mail'] . '</td>
											<td ></td>
										</tr>
							
						
										<tr>
											<td td style="width: 180px;">user-ი</td>
											<td td style="width: 180px;"></td>
										</tr>
										<tr>
											<td style="width: 180px;">' . $res['user'] . '</td>
											<td td style="width: 180px;"></td>
										</tr>
									</table>
								</fieldset>
								<fieldset>
									<legend>მომართვის ავტორი</legend>
									<table>
										<tr>
											<td>სულ</td>
											<td></td>
											<td></td>
											<td style="width: 150px;"></td>
											<td>1</td>
										</tr>
										<tr>
											<td></td>
											<td>მოგვარებულები</td>
											<td></td>
											<td style="width: 150px;"></td>
										    <td>2</td>
										</tr>
										<tr>
											<td></td>
											<td></td>
											<td>პრეტენზია</td>
											<td style="width: 150px;"></td>
											<td>2</td>
										</tr>
										<tr>
											<td></td>
											<td></td>
											<td>ინფორმაცია</td>
											<td style="width: 150px;"></td>
											<td>2</td>
										</tr>
										<tr>
											<td></td>
											<td>მიმდინარე</td>
											<td></td>
											<td style="width: 150px;"></td>
											<td>2</td>
										</tr>
										<tr>
											<td></td>
											<td></td>
											<td></td>
											<td style="width: 150px;"></td>
											<td>2</td>
										</tr>
										<tr>
											<td></td>
											<td></td>
											<td>პრეტენზია</td>
											<td style="width: 150px;"></td>
											<td>2</td>
										</tr>
										<tr>
											<td></td>
											<td></td>
											<td>ინფორმაცია</td>
											<td style="width: 150px;"></td>
											<td>2</td>
										</tr>
										<tr>
											<td></td>
											<td></td>
											<td></td>
											<td style="width: 150px;"></td>
								    		<td style="width: 300px;">
											<input type="button" value="ვრცლად"/>
						      				</td>
										</tr>
						
									</tr>
									</table>
								</fieldset>
								<fieldset>
									<legend>დამატებითი ინფორმაცია</legend> 
									<table style="float: left; border: 1px solid #85b1de; width: 180px; text-align: center;">
										<tr style="border-bottom: 1px solid #85b1de;">
											<td colspan="2">საუბრის ჩანაწერი</td>
										</tr>
										<tr style="border-bottom: 1px solid #85b1de; ">
											<td>დრო</td>
											<td style="border-left:1px solid #85b1de; width: 50px;">ჩანაწერი</td>
										</tr>
										<tr >
											<td></td>
											<td><input type="button" value="მოსმენა"/></td>
										</tr>
									<table/>
									<table style="float: right; border: 1px solid #85b1de; width: 150px; text-align: center;">
										<tr>
											<td>
												<div class="file-uploader">
													<input id="choose_file" type="file" name="choose_file" class="input" style="display: none;">
													<button id="choose_button" class="center">აირჩიეთ ფაილი</button>
													<input id="hidden_inc" type="text" value="'. increment('task') .'" style="display: none;">
												</div>
											</td>
										</tr>
									</table>
								     <table style="float: right; border: 1px solid #85b1de; width: 150px; text-align: center;">
								          <tr style="border-bottom: 1px solid #85b1de;">
								           <td colspan="3">მიმაგრებული ფაილი</td>
								          </tr>
									</table>
									<table id="file_div" style="float: right; border: 1px solid #85b1de; width: 150px; text-align: center;">';
										
										while($increm_row = mysql_fetch_assoc($increm))	{	
											$data .=' 
													        <tr style="border-bottom: 1px solid #85b1de;">
													          <td style="width:110px; display:block;word-wrap:break-word;">'.$increm_row[name].'</td>													 
													          <td ><button type="button" value="media/uploads/file/'.$increm_row[rand_name].'" style="cursor:pointer; border:none; margin-top:25%; display:block; height:16px; width:16px; background:none;background-image:url(\'media/images/get.png\');" id="download" ></button><input type="text" style="display:none;" id="download_name" value="'.$increm_row[rand_name].'"> </td>
													          <td ><button type="button" value="'.$increm_row[id].'" style="cursor:pointer; border:none; margin-top:25%; display:block; height:16px; width:16px; background:none; background-image:url(\'media/images/x.png\');" id="delete"></button></td>
													        </tr>';
										}
								         
						 $data .= '
				 					</table>
								</fieldset>
							</div>
				    </div>';
	}else {
		$data  .= '<div id="dialog-form">
					<div style="float: left; width: 500px;">	
						<fieldset >
					    	<legend>ძირითადი ინფორმაცია</legend>
				
					    	<table width="100%" class="dialog-form-table">
								<tr>
									<td style="width: 180px;"><label for="req_num">მომართვა №</label></td>
									<td style="width: 180px !important;"><label for="req_data">თარიღი</label></td>
									<td style="width: 180px;"><label for="req_phone">ტელეფონი</label></td>
								</tr>
								<tr>
									<td style="width: 180px;">
										<input type="text" id="id" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['id']. '" disabled="disabled" />
									</td>
									<td style="width: 180px;">
										<input type="text" id="c_date" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField date\'" value="' .  $res['call_date']. '" disabled="disabled" />
									</td>
									<td style="width: 180px;">
										<input type="text" id="phone" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $num . '" />
									</td>
									<td style="width: 69px;">
										<button class="calls">ნომრები</button>
									</td>
								</tr>
								<tr>
									<td style="width: 180px;"><label for="d_number">ზარის ტიპი</label></td>
									<td style="width: 180px;"><label for="d_number">კატეგორია</label></td>
									<td style="width: 180px;"><label for="d_number">ქვე-კატეგოტია</label></td>
									<td ></td>
								</tr>
								<tr>
									<td style="width: 180px;"><select id="call_type_id" class="idls object" disabled="disabled">'. Getcall_type($res['call_type_id']).'</select></td>
									<td style="width: 180px;"><select id="category_parent_id" class="idls object" disabled="disabled">'. Getcategory($res['category_parent_id']).'</select></td>
									<td style="width: 180px;"><select id="category_id" class="idls object" disabled="disabled">'. Getcategory1_edit($res['category_id']).'</select></td>
									<td ></td>
								</tr>
							</table>';
				if (Getcategory($res['category_parent_id'])==407) {
					$data  .= '<table id="additional" class="hidden dialog-form-table" width="100%">
								<tr >
									<td style="width: 180px;"><label for="d_number">შეტანის ფორმა</label></td>
									<td style="width: 180px;"><label for="d_number">მომსახურე ბანკი</label></td>
									<td style="width: 180px;"><label for="d_number">ფილიალი</label></td>
									<td style="width: 106px;"></td>
								</tr>
								<tr >
									<td style="width: 180px;"><select id="pay_type_id" class="idls object" disabled="disabled">'. Getpay_type($res['pay_type_id']).'</select></td>
									<td style="width: 180px;"><select id="bank_id" class="idls object" disabled="disabled">'. Get_bank($res['bank_id']).'</select></td>
									<td style="width: 180px;"><select id="bank_object_id" class="idls object" disabled="disabled">'. Getbank_object_edit($res['bank_object_id']).'</select></td>
									<td style="width: 106px;"></td>
								</tr>
								<tr >
									<td style="width: 180px;"><label for="d_number">ბარათის ტიპი</label></td>
									<td style="width: 180px;"><label for="d_number">ანგარიშსწორება</label></td>
									<td style="width: 180px;"><label for="d_number">აპარატის ტიპი</label></td>
									<td style="width: 106px;"></td>
								</tr>
								<tr >
									<td style="width: 180px;"><select id="card_type_id" class="idls object" disabled="disabled">'. Getcard_type($res['card_type_id']).'</select></td>
									<td style="width: 180px;"><select id="card_type1_id" class="idls object" disabled="disabled">'. Getcard_type1($res['card_type1_id']).'</select></td>
									<td style="width: 180px;"><select id="pay_aparat_id" class="idls object" disabled="disabled">'. Getpay_aparat($res['pay_aparat_id']).'</select></td>
									<td style="width: 106px;"></td>
								</tr>
							</table>';
				}								
				
														
				$data  .= '<table width="100%" class="dialog-form-table">
								<tr>
									<td style="width: 180px;"><label for="req_num">პრობლემის თარიღი</label></td>
									<td style="width: 180px;"><label for="d_number">ობიექტი</label></td>
									<td style="width: 180px;"><label for="d_number">ზარის სტატუსი</label></td>			
									<td ></td>
								</tr>
								<tr>	
									<td style="width: 180px;"> <input type="text" id="problem_date" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res[problem_date] . '"disabled="disabled" /></td>
									<td style="width: 180px;"><select id="object_id" class="idls object" disabled="disabled">'. Getobject($res['object_id']).'</select></td>
									<td style="width: 180px;"><select id="call_status_id" class="idls object"disabled="disabled">'. Getcall_status($res['status']).'</select></td>	
									<td ></td>
								</tr>
								<tr>
									<td style="width: 180px;"><label for="content">საუბრის შინაარსი</label></td>
								</tr>
								<tr>
									
									<td colspan="6">	
										<textarea disabled="disabled" style="width: 641px; resize: none;" id="call_content" class="idle" name="call_content" cols="300" rows="2" disabled="disabled">' . $res['call_content'] . '</textarea>
									</td>
								</tr>	
								<tr>
									<td style="width: 180px;"><label for="content">პრობლემის გადაწყვეტა</label></td>
								</tr>
								<tr>
						
									<td colspan="6">
										<textarea  style="width: 641px; resize: none;" id="content1" class="idle" name="call_content" cols="300" rows="2"disabled="disabled">' . $res['content1'] . '</textarea>
									</td>
								</tr>		
							</table>
						</fieldset >
				   
						<fieldset style="margin-top: 5px;">
					    	<legend>დავალების ფორმირება</legend>
				
					    	<table class="dialog-form-table">
								<tr>
									<td style="width: 180px;"><label for="d_number">დავალების ტიპი</label></td>
									<td style="width: 180px;"><label for="d_number">განყოფილება</label></td>
									<td style="width: 180px;"><label for="d_number">პასუხისმგებელი პირი</label></td>
								</tr>
					    		<tr>
									<td style="width: 180px;"><select id="task_type_id" class="idls object"disabled="disabled">'.Gettask_type($res['task_type_id']).'</select></td>
									<td style="width: 180px;"><select id="task_department_id" class="idls object"disabled="disabled">'. Getdepartment($res['task_department_id']).'</select></td>
									<td style="width: 180px;"><select id="persons_id" class="idls object"disabled="disabled">'.Getpersonss($res['persons_id']).'</select></td>
								</tr>
								<tr>
									<td style="width: 180px;"><label for="d_number">პრიორიტეტები</label></td>
									<td style="width: 180px;"></td>
									<td style="width: 180px;"></td>
								</tr>
								<tr>
									<td style="width: 180px;"><select id="priority_id" class="idls object">'.Getpriority($res['priority_id']).'</select></td>
									<td style="width: 180px;"></td>
									<td style="width: 180px;"></td>
								</tr>
								<tr>
									<td style="width: 150px;"><label for="content">კომენტარი</label></td>
									<td style="width: 150px;"><label for="content"></label></td>
									<td style="width: 150px;"><label for="content"></label></td>
								</tr>
								<tr>
									<td colspan="6">
										<textarea  style="width: 641px; resize: none;" id="comment" class="idle" name="content" cols="300" rows="2">' . $res['comment'] . '</textarea>
									</td>
								</tr>
							
								</tr>
							</table>
				        </fieldset>
					</div>
					<div>
						  </fieldset>
					</div>
					<div style="float: right;  width: 355px;">
						 <fieldset>
									<legend>მომართვის ავტორი</legend>
									<table style="height: 243px;">
										<tr>
											<td style="width: 180px;">PIN კოდი</td>
											<td style="width: 180px;">ეგობრის PIN ცოდი</td>
										</tr>
										<tr>
											<td style="width: 180px;"><input type="text" id="personal_pin" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['personal_pin']  . '" /></td>
											<td style="width: 180px;"><input type="text" id="personal_pin" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['friend_pin']  . '" /></td>
											
										</tr>
										<tr>
											<td style="width: 180px;">პირადი ნომერი</td>
											<td style="width: 180px;"></td>
										</tr>
										<tr>
											<td style="width: 180px;">
												<input type="text" id="personal_id" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['personal_id'] . '" />
											</td>
											<td style="width: 180px;"></td>
										</tr>
										<tr>
											<td style="width: 180px;">სახელი და გვარი</td>
											</td>
										</tr>
										<tr >
											<td style="width: 180px;">' . $res['name1'] . '</td>
											</td>
										</tr>
										<tr >
											<td style="width: 180px;">ტელეფონი</td>
											</td>
										</tr>
										<tr>
											<td style="width: 180px;">' . $res['personal_phone'] . '</td>
											</td>
										</tr>
										<tr>
											<td style="width: 180px;">ელ-ფოსტა</td>
											</td>
										</tr>
										<tr>
											<td style="width: 180px;">' . $res['mail'] . '</td>
											<td ></td>
										</tr>
							
						
										<tr>
											<td td style="width: 180px;">user-ი</td>
											<td td style="width: 180px;"></td>
										</tr>
										<tr>
											<td style="width: 180px;">' . $res['user'] . '</td>
											<td td style="width: 180px;"></td>
										</tr>
									</table>
								</fieldset>
						<fieldset>
							<legend>მომართვის ავტორი</legend>
							<table>
								<tr>
									<td>სულ</td>
									<td></td>
									<td></td>
									<td style="width: 150px;"></td>
									<td>1</td>
								</tr>
								<tr>
									<td></td>
									<td>მოგვარებულები</td>
									<td></td>
									<td style="width: 150px;"></td>
								    <td>2</td>
								</tr>
								<tr>
									<td></td>
									<td></td>
									<td>პრეტენზია</td>
									<td style="width: 150px;"></td>
									<td>2</td>
								</tr>
								<tr>
									<td></td>
									<td></td>
									<td>ინფორმაცია</td>
									<td style="width: 150px;"></td>
									<td>2</td>
								</tr>
								<tr>
									<td></td>
									<td>მიმდინარე</td>
									<td></td>
									<td style="width: 150px;"></td>
									<td>2</td>
								</tr>
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td style="width: 150px;"></td>
									<td>2</td>
								</tr>
								<tr>
									<td></td>
									<td></td>
									<td>პრეტენზია</td>
									<td style="width: 150px;"></td>
									<td>2</td>
								</tr>
								<tr>
									<td></td>
									<td></td>
									<td>ინფორმაცია</td>
									<td style="width: 150px;"></td>
									<td>2</td>
								</tr>
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td style="width: 150px;"></td>
						    		<td style="width: 300px;">
									<input type="button" value="ვრცლად"/>
				      				</td>
								</tr>
												
							</tr>
							</table>
						</fieldset>
						<fieldset>
									<legend>დამატებითი ინფორმაცია</legend> 
									<table style="float: left; border: 1px solid #85b1de; width: 180px; text-align: center;">
										<tr style="border-bottom: 1px solid #85b1de;">
											<td colspan="2">საუბრის ჩანაწერი</td>
										</tr>
										<tr style="border-bottom: 1px solid #85b1de; ">
											<td>დრო</td>
											<td style="border-left:1px solid #85b1de; width: 50px;">ჩანაწერი</td>
										</tr>
										<tr >
											<td></td>
											<td><input type="button" value="მოსმენა"/></td>
										</tr>
									<table/>
									<table style="float: right; border: 1px solid #85b1de; width: 150px; text-align: center;">
										<tr>
											<td>
												<div class="file-uploader">
													<input id="choose_file" type="file" name="choose_file" class="input" style="display: none;">
													<button id="choose_button" class="center">აირჩიეთ ფაილი</button>
													<input id="hidden_inc" type="text" value="'. increment('task') .'" style="display: none;">
												</div>
											</td>
										</tr>
									</table>
								     <table style="float: right; border: 1px solid #85b1de; width: 150px; text-align: center;">
								          <tr style="border-bottom: 1px solid #85b1de;">
								           <td colspan="3">მიმაგრებული ფაილი</td>
								          </tr>
									</table>
									<table id="file_div" style="float: right; border: 1px solid #85b1de; width: 150px; text-align: center;">';
										
										while($increm_row = mysql_fetch_assoc($increm))	{	
											$data .=' 
													        <tr style="border-bottom: 1px solid #85b1de;">
													          <td style="width:110px; display:block;word-wrap:break-word;">'.$increm_row[name].'</td>													 
													          <td ><button type="button" value="media/uploads/file/'.$increm_row[rand_name].'" style="cursor:pointer; border:none; margin-top:25%; display:block; height:16px; width:16px; background:none;background-image:url(\'media/images/get.png\');" id="download" ></button><input type="text" style="display:none;" id="download_name" value="'.$increm_row[rand_name].'"> </td>
													          <td ><button type="button" value="'.$increm_row[id].'" style="cursor:pointer; border:none; margin-top:25%; display:block; height:16px; width:16px; background:none; background-image:url(\'media/images/x.png\');" id="delete"></button></td>
													        </tr>';
										}
								         
						 $data .= '
				 					</table>
								</fieldset>
					</div>
		    </div>';	
	}
	
	$data .= '<input type="hidden" id="outgoing_call_id" value="' . $res['id'] . '" />';

	return $data;
}
function increment($table){

	$result   		= mysql_query("SHOW TABLE STATUS LIKE '$table'");
	$row   			= mysql_fetch_array($result);
	$increment   	= $row['Auto_increment'];
	$next_increment = $increment+1;
	mysql_query("ALTER TABLE '$table' AUTO_INCREMENT=$next_increment");

	return $increment;
}
?>
