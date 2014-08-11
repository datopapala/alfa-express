<?php

/* ******************************
 *	Request aJax actions
* ******************************
*/

require_once('../../includes/classes/core.php');
$action 	= $_REQUEST['act'];
$error		= '';
$data		= '';


//incomming
$incom_id				= $_REQUEST['id'];
$call_date				= $_REQUEST['call_date'];
$site_user_pin			= $_REQUEST['pin'];
$call_type_id			= $_REQUEST['call_type_id'];
$phone					= $_REQUEST['phone'];
$category_id			= $_REQUEST['category_id'];
$problem_date			= $_REQUEST['problem_date'];
$call_content			= $_REQUEST['call_content'];
$category_parent_id 	= $_REQUEST['category_parent_id'];
$call_status_id			= $_REQUEST['call_status_id'];
$problem_comment		= $_REQUEST['problem_comment'];

$pay_type_id			= $_REQUEST['pay_type_id'];
$bank_id				= $_REQUEST['bank_id'];
$bank_object_id			= $_REQUEST['bank_object_id'];
$card_type_id			= $_REQUEST['card_type_id'];
$card_type1_id 			= $_REQUEST['card_type1_id'];
$pay_aparat_id			= $_REQUEST['pay_aparat_id'];
$object_id				= $_REQUEST['object_id'];


// site_user
$personal_pin			= $_REQUEST['personal_pin'];
$personal_id			= $_REQUEST['personal_id'];
$personal_phone			= $_REQUEST['personal_phone'];
$mail				    = $_REQUEST['mail'];
$name				    = $_REQUEST['name'];
$operator_name			= $_REQUEST['operator_name'];



//task
$persons_id			    = $_REQUEST['persons_id'];
$task_type_id			= $_REQUEST['task_type_id'];
$priority_id			= $_REQUEST['priority_id'];
$comment 	        	= $_REQUEST['comment'];
$problem_id 	        = $_REQUEST['problem_id'];
$task_department_id 	= $_REQUEST['task_department_id'];
$planned_date			= $_REQUEST['planned_date'];
$call_duration			= $_REQUEST['call_duration'];
$c_date					= $_REQUEST['c_date	'];
$fact_end_date			= $_REQUEST['fact_end_date'];
$template_id			= $_REQUEST['template_id'];
$hidden_inc				= $_REQUEST['hidden_inc'];
$edit_id				= $_REQUEST['edit_id'];
$delete_id				= $_REQUEST['delete_id'];


// file
$rand_file			= $_REQUEST['rand_file'];
$file				= $_REQUEST['file_name'];


switch ($action) {
	case 'get_add_page':
		$number		= $_REQUEST['number'];
		$page		= GetPage($res='', $number);
		$data		= array('page'	=> $page);

		break;
	
	case 'get_edit_page':
		$page		= GetPage(Getincomming($incom_id),$incom_id);

		$data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count 		= $_REQUEST['count'];
		$hidden 	= $_REQUEST['hidden'];
		$user_id	= $_SESSION['USERID'];
  		$rResult = mysql_query("SELECT 	 	`task`.id,
											`task`.id,
											`site_user`.`name`,
											`site_user`.`pin`,
											`problem`.`name`,
											`person1`.`name` ,
											`person2`.`name` ,
											`task`.date,
											`call_status`.`name`
								FROM 		`task`			
								left JOIN 	`problem`		ON task.problem_id=problem.id
								LEFT JOIN 	`site_user`		ON task.id=site_user.task_id
								
								JOIN 		users AS `user1`			ON task.responsible_user_id=user1.id
								JOIN 		persons AS `person1`		ON user1.person_id=person1.id
								
								JOIN 		users AS `user2`			ON task.user_id=user2.id
								JOIN 		persons AS `person2`		ON user2.person_id=person2.id
								
								left JOIN 	call_status  	ON	task.call_status_id=call_status.id
								WHERE 		task.actived=1 AND task.responsible_user_id = $user_id and task.task_type_id != 1
									
	  			");
	  
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
	case 'save_incomming':
		$incom_id = $_REQUEST['id'];
		if($incom_id == ''){
			Addtask($persons_id, $c_date, $phone, $task_type_id, $template_id, $task_department_id, $call_type_id, $category_parent_id, $category_id, $problem_date, $call_status_id, $object_id, $comment, $planned_date, $fact_end_date, $call_duration, $priority_id, $problem_id, $pay_type_id, $bank_id, $pay_aparat_id, $card_type_id, $rand_file, $file, $hidden_inc);
			$task_id = mysql_insert_id();
			Addsite_user($task_id, $personal_pin, $name, $personal_phone, $mail,  $personal_id);
		
		}else {
			Savetask($incom_id, $persons_id, $c_date, $phone, $task_type_id, $template_id, $task_department_id, $call_type_id, $category_parent_id, $category_id, $problem_date, $call_status_id, $object_id, $comment, $planned_date, $fact_end_date, $call_duration, $priority_id, $problem_id, $pay_type_id, $bank_id, $pay_aparat_id, $card_type_id, $rand_file, $file);	
		}
		break;
		
	case 'get_calls':
	
		$data		= array('calls' => getCalls());
	
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
		
	case 'sub_category':
		
		$cat_id	=	$_REQUEST['cat_id'];
		$data 	= 	array('cat'=>Getcategory1($cat_id));
		
		break;	
		
	case 'sub_bank_category':
	
		$cat_id	=	$_REQUEST['cat_id'];
		$data 	= 	array('cat'=>Getbank_object($cat_id));
	
		break;
		
	case 'disable':
		$incom_id				= $_REQUEST['id'];
		mysql_query("			UPDATE 	`task`
								SET 	`actived`=0
								WHERE 	`id`=$incom_id ");
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

function Addtask($persons_id, $c_date, $phone, $task_type_id, $template_id, $task_department_id, $call_type_id, $category_parent_id, $category_id, $problem_date, $call_status_id, $object_id, $comment, $planned_date, $fact_end_date, $call_duration, $priority_id, $problem_id, $pay_type_id, $bank_id, $pay_aparat_id, $card_type_id, $rand_file, $file, $hidden_inc)
{
	$res_row = mysql_fetch_assoc(mysql_query("SELECT 	`users`.`id`
												FROM 	`users`
												JOIN 	`persons` ON `users`.`person_id` = `persons`.`id`
												WHERE 	`persons`.`id` = $persons_id"));
	
	$user		= $_SESSION['USERID'];
	mysql_query("INSERT INTO `task`
							 	( `id`,
							 	  `user_id`,
							 	  `responsible_user_id`, 
							 	  `date`,
							 	  `phone`, 
							 	  `planned_end_date`,
							 	  `fact_end_date`,
							 	  `call_duration`,							 	  
							 	  `task_type_id`,
							 	  `template_id`,
							 	  `priority_id`,
							 	  `problem_id`,
							 	  `department_id`,
							 	  `call_type_id`,
							 	  `category_id`,
							 	  `subcategory_id`,
							 	  `object_id`,
							 	  `pay_type_id`,
							 	  `bank_id`,
							 	  `card_type_id`,
							 	  `pay_aparat_id`,
							 	  `problem_date`,
							 	  `call_status_id`,							 	 
							 	  `comment`)
							 VALUES 
								(	'$hidden_inc',
									'$user',  
									'$res_row[id]', 
									current_timestamp(), 
									'$phone',
									'$planned_date',
									'$fact_end_date',
									'$call_duration',
									'$task_type_id',
									'$template_id', 
									'$priority_id',
									'$problem_id',
									'$task_department_id',
									'$call_type_id',
									'$category_parent_id',
									'$category_id',
									'$object_id',
									'$pay_type_id',
									'$bank_id',
									'$card_type_id',
									'$pay_aparat_id',
									'$problem_date',
									'$call_status_id',
									'$comment');");
	
	if($rand_file != ''){
		mysql_query("INSERT INTO 	`file`
								( 	`user_id`,
									`task_id`,
									`name`,
									`rand_name`
								)
								VALUES
								(	'$user',
									'$hidden_inc',
									'$file',
									'$rand_file'
								);");
	}

}
function Addsite_user($task_id, $personal_pin, $name, $personal_phone, $mail,  $personal_id)
{
	
	$user		= $_SESSION['USERID'];
	mysql_query("INSERT INTO `site_user` 	
								(`task_id`,
								`site`, 
								`pin`, 
								`name`, 
								`phone`, 
								`mail`, 
								`personal_id`, 
								`user`)
						      VALUES 
								( '$task_id',
								'243', 
								'$personal_pin', 
								'$name', 
								'$personal_phone', 
								'$mail', 
								'$personal_id',
								'$user')");

}
      
function Savetask($incom_id, $persons_id, $c_date, $phone, $task_type_id, $template_id, $task_department_id, $call_type_id, $category_parent_id, $category_id, $problem_date, $call_status_id, $object_id, $comment, $planned_date, $fact_end_date, $call_duration, $priority_id, $problem_id, $pay_type_id, $bank_id, $pay_aparat_id, $card_type_id,$rand_file, $file)
{

	$res_row = mysql_fetch_assoc(mysql_query("SELECT 	`users`.`id`
			FROM 	`users`
			JOIN 	`persons` ON `users`.`person_id` = `persons`.`id`
			WHERE 	`persons`.`id` = $persons_id"));
	
	
	$user  = $_SESSION['USERID'];
	mysql_query("UPDATE `task` SET  	 
								  `user_id` 			= '$user', 
							 	  `responsible_user_id` = '$res_row[id]', 
							 	  `date`				= '$c_date', 
							 	  `phone` 				= '$phone', 
							 	  `planned_end_date` 	= '$planned_date',
							 	  `fact_end_date` 		= '$fact_end_date',
							 	  `call_duration` 		= '$call_duration',
							 	  `task_type_id` 		= '$task_type_id',
							 	  `template_id`			= '$template_id',
							 	  `priority_id` 		= '$priority_id',
							 	  `problem_id` 			= '$problem_id',
							 	  `department_id` 		= '$task_department_id',
							 	  `call_type_id` 		= '$call_type_id',
							 	  `category_id` 		= '$category_parent_id',
							 	  `subcategory_id` 		= '$category_id',
							 	  `object_id` 			= '$object_id',
							 	  `pay_type_id` 		= '$pay_type_id',
							 	  `bank_id` 			= '$bank_id',
							 	  `card_type_id` 		= '$card_type_id',
							 	  `pay_aparat_id` 		= '$pay_aparat_id',
							 	  `problem_date` 		= '$problem_date',
							 	  `call_status_id` 		= '$call_status_id',		 	 
							 	  `comment` 			= '$comment',
							 	  `problem_comment` 	= '$problem_comment'
				WHERE (`id`='$incom_id');");
	

}



function Getcall_status($call_status_id)
{
	$data = '';
	$req = mysql_query("SELECT 	`id`, `name`
						FROM 	`call_status`
						WHERE 	actived=1");
	

	$data .= '<option value="0" selected="selected">----</option>';
	while( $res = mysql_fetch_assoc($req)){
		
		if($res['id'] == $call_status_id){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
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
	
function Getbank_object($bank_object_id)
{  
	$data = '';
	$req = mysql_query("SELECT  id,
						     	`name`
						FROM 	bank_object
						WHERE 	bank_object.bank_id=$bank_object_id && actived =1");


	$data .= '<option value="0" selected="selected">----</option>';
	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $bank_object_id){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}

	return $data;
}

function Getbank_object_edit($bank_object_id)
{

	$data = '';
	$req = mysql_query("SELECT  id,
								`name`
						FROM 	bank_object
						WHERE 	bank_object.id=$bank_object_id && actived =1");


	$data .= '<option value="0" selected="selected">----</option>';
	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $bank_object_id){
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
					    WHERE actived=1 ");
	

	$data .= '<option value="0" selected="selected">----</option>';
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

function getCalls(){
	$db1 = new sql_db ( "212.72.155.176", "root", "Gl-1114", "asteriskcdrdb" );

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

}

function Getincomming($incom_id){
$res = mysql_fetch_assoc(mysql_query("	SELECT    	task.id AS id,
													task.phone AS `phone`,
													task.date AS call_date,
													task.call_type_id AS call_type_id,
													task.subcategory_id AS category_id,
													task.call_status_id AS call_status_id,
													task.category_id AS category_parent_id,
													task.problem_date ,
													task.pay_type_id AS pay_type_id,
													task.bank_id AS bank_id,
													task.bank_object_id AS bank_object_id,
													task.card_type_id AS card_type_id,
													task.card_type_id AS card_type1_id,
													task.pay_aparat_id AS pay_aparat_id,
													task.object_id AS object_id,
													site_user.`name` AS `name`,
													site_user.mail AS mail,
													site_user.personal_id AS personal_id,
													site_user.phone AS personal_phone,
													site_user.pin AS personal_pin,
													site_user.user AS user,
													site_user.`name` AS operator_name,
													task.task_type_id AS task_type_id,
													task.responsible_user_id AS persons_id,
													task.priority_id AS priority_id,
													task.problem_id AS problem_id ,
													task.department_id AS task_department_id,
													task.`comment` AS `comment`,
													task.`planned_end_date`,
													task.`problem_comment` AS `problem_comment`,
													task.template_id AS	template_id												
										FROM 	   	`task`
										LEFT JOIN  	site_user ON task.id=site_user.task_id										
										WHERE      	task.id = $incom_id
			" ));
	
	return $res;
}

function GetPage($res='', $number, $incom_id)
{
	$num = 0;
	if($res[phone]==""){
		$num=$number;
	}else{ 
		$num=$res[phone]; 
	}
	
	$tanxa = 'class="hidden dialog-form-table"';
	$disabled = '';
	$hidden_class = '';
	
	if($_REQUEST['id'] == ''){
		$hidden_class = 'class="hidden"';
	}else{
		if($res[category_parent_id] == 407){
			$tanxa = 'class="dialog-form-table"';
		}else{
			$tanxa = 'class="hidden dialog-form-table"';
		}
		$disabled = 'disabled="disabled"';
	}
	
	$increm = mysql_query("	SELECT  `name`,
	 								`rand_name`,
									`id`
							FROM 	`file`
							WHERE   `task_id` = $res[id]
							  ");
	
	
	$data  .= '
	<!-- jQuery Dialog -->
    <div id="add-edit-goods-form" title="საქონელი">
    	<!-- aJax -->
	</div>
	<div id="dialog-form">
			<div style="float: left; width: 500px;">	
				<fieldset >
			    	<legend>ძირითადი ინფორმაცია</legend>
		
			    	<table width="100%" class="dialog-form-table">
						<tr>
							<td style="width: 215px;"><label for="req_num">დავალების №</label></td>
							<td style="width: 215px !important;"><label for="req_data">თარიღი</label></td>
							<td style="width: 215px;"><label for="req_phone">ტელეფონი</label></td>
						</tr>						
						<tr>
							<td style="width: 215px;">
								<input  style="width: 180px; type="text" id="id" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['id']. '" disabled="disabled" />
							</td>
							<td style="width: 215px;">
								<input style="width: 180px; type="text" id="c_date" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField date\'" value="' .  $res['call_date']. '" disabled="disabled" />
							</td>
							<td style="width: 215px;">
								<input style="width: 180px; type="text" id="phone" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $num . '" />
							</td>
							
						</tr>
						<tr>
							<td style="width: 215px;"><label for="task_type_id">დავალების ტიპი</label></td>
							<td style="width: 215px;"><label for="task_department_id">განყოფილება</label></td>
							<td style="width: 215px;"><label for="persons_id">პასუხისმგებელი პირი</label></td>
						</tr>
						<tr>
							<td style="width: 215px;"><select style="width: 186px;" id="task_type_id" class="idls object">'.Gettask_type($res['task_type_id']).'</select></td>
							<td style="width: 215px;"><select style="width: 186px;" id="task_department_id" class="idls object">'. Getdepartment($res['task_department_id']).'</select></td>
							<td style="width: 215px;"><select style="width: 186px;" id="persons_id" class="idls object">'.Getpersons($res['persons_id']).'</select></td>
						</tr>
					</table>';
									
		$data .= '<table id="additiona" class="dialog-form-table" width="100%">				
									
						<tr>
							<td style="width: 215px;"><label for="d_number">ზარის ტიპი</label></td>
							<td style="width: 215px;"><label for="d_number">კატეგორია</label></td>
							<td style="width: 215px;"><label for="d_number">ქვე-კატეგოტია</label></td>
							
						</tr>
						<tr>
							<td style="width: 215px;"><select style="width: 186px;" id="call_type_id" class="idls object"" >'. Getcall_type($res['call_type_id']).'</select></td>
							<td style="width: 215px;"><select style="width: 186px;" id="category_parent_id" class="idls object"" >'. Getcategory($res['category_parent_id']).'</select></td>
							<td style="width: 215px;"><select style="width: 186px;" id="category_id" class="idls object"" >'. Getcategory1_edit($res['category_id']).'</select></td>
							
						</tr>
						</table>
									<table id="additiona3" '.$tanxa.' width="100%">
						<tr >
							<td style="width: 215px;"><label for="d_number">შეტანის ფორმა</label></td>
							<td style="width: 215px;"><label for="d_number">მომსახურე ბანკი</label></td>
							<td style="width: 215px;"><label for="d_number">აპარატის ტიპი</label></td>
							
						</tr>
						<tr >
							<td style="width: 215px;"><select style="width: 186px;" id="pay_type_id" class="idls object">'. Getpay_type($res['pay_type_id']).'</select></td>
							<td style="width: 215px;"><select style="width: 186px;" id="bank_id" class="idls object">'. Get_bank($res['bank_id']).'</select></td>
							<td style="width: 215px;"><select style="width: 186px;" id="pay_aparat_id" class="idls object">'. Getpay_aparat($res['pay_aparat_id']).'</select></td>

						</tr>
						<tr >
							<td style="width: 215px;"><label for="d_number">ბარათის ტიპი</label></td>
							<td style="width: 215px;"></td>
							<td style="width: 215px;"></td>
							
						</tr>
						<tr >
							<td style="width: 215px;"><select style="width: 186px;" id="card_type_id" class="idls object">'. Getcard_type($res['card_type_id']).'</select></td>
							<td style="width: 215px;"></td>
							<td style="width: 215px;"></td>
						
						</tr>
					</table>
					<table id="additiona2" class="dialog-form-table" width="100%">	
						<tr>
							<td style="width: 215px;"><label for="req_num">პრობლემის თარიღი</label></td>
							<td style="width: 215px;"><label for="d_number">ზარის სტატუსი</label></td>
							<td style="width: 215px;"><label for="d_number">ობიექტი</label></td>			
						</tr>
						<tr>	
							<td style="width: 215px;">
								<input style="width: 180px;" type="text" id="problem_date" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res[problem_date] . '""  />
							</td>
							<td style="width: 215px;"><select style="width: 186px;" id="call_status_id" class="idls object">'. Getcall_status($res['call_status_id']).'</select></td>
							<td style="width: 215px;"><select style="width: 186px;" id="object_id" class="idls object">'. Getobject($res['object_id']).'</select></td>	
								
						</tr>
						</table>

									
								<table id="additiona1" class="hidden dialog-form-table" width="100%">
					
						<tr>
							<td style="width: 215px;"><label for="req_num">შესრულების გეგმ. დრო</label></td>
							<td style="width: 215px !important;"><label for="req_data">შესრულების ფაქტ. დრო</label></td>
							<td style="width: 215px;"><label for="req_phone">შესრულების დრო</label></td>
						</tr>
						<tr>
							<td style="width: 215px;">
								<input type="text" id="planned_date" style="width: 180px;" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' .  $res['planned_end_date']. '" />
							</td>
							<td style="width: 215px;">
								<input type="text" id="fact_end_date" style="width: 180px;" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' .  $res['call_date']. '" disabled="disabled" />
							</td>
							<td style="width: 215px;">
								<input type="text" id="call_duration" style="width: 180px;" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $num . '" />
							</td>
							
						</tr>
						<tr>
							<td style="width: 215px;"><label for="d_number">პრიორიტეტები</label></td>							
							<td style="width: 215px;"><label for="d_number">თემა</td>
							<td style="width: 215px;"></td>
						</tr>
						<tr>
							<td style="width: 215px;"><select id="priority_id" style="width: 186px;" class="idls object">'.Getpriority($res['priority_id']).'</select></td>							
							<td style="width: 215px;"><select id="template_id" style="width: 186px;" class="idls object">'.Gettemplate($res['template_id']).'</select></td>
							<td style="width: 215px;"></td>
						</tr>						
					</table>	
								<br>	
									
						<table>			
						<tr>
							<td style="width: 150px;"><label for="content">კომენტარი</label></td>
							<td style="width: 150px;"><label for="content"></label></td>
							<td style="width: 150px;"><label for="content"></label></td>
						</tr>
						<tr>
							<td colspan="6">
								<textarea  '.$disabled.' style="width: 641px; resize: none;" id="comment" class="idle" name="content" cols="300" rows="2">' . $res['comment'] . '</textarea>
							</td>
						</tr>
						<tr>
							<td '.$hidden_class.' style="width: 215px;"><label for="content">პრობლემის გადაწყვეტა</label></td>
						</tr>
						<tr>
							
							<td colspan="5">	
								<textarea '.$hidden_class.' style="width: 641px; resize: none;" id="problem_comment" class="idle" name="call_content" cols="300" rows="2">' . $res['problem_comment'] . '</textarea>
							</td>
						</tr>	
					</table>';		
									
		
												
		$data  .= '
				</fieldset >
		   
				
			</div>
			<div>
				  </fieldset>
			</div>
			<div style="float: right;  width: 355px;">
				 <fieldset>
					<legend>მომართვის ავტორი</legend>
					<table style="height: 243px;">
						<tr>
							<td style="width: 215px;">PIN კოდი</td>
							<td style="width: 215px;" class="hidden friend">მეგობრის PIN კოდი</td>
						</tr>
						<tr>
							<td style="width: 215px;"><input type="text" id="personal_pin" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['personal_pin']  . '" /></td>
							<td style="width: 215px;" class="hidden friend">
								<input type="text" id="req_phone" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $num1 . '" />
							</td>
						</tr>
						<tr>
							<td style="width: 215px;">პირადი ნომერი</td>
							<td style="width: 215px;"></td>
						</tr>
						<tr>
							<td style="width: 215px;">
								<input type="text" id="personal_id" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['personal_id'] . '" />
							</td>
							<td style="width: 215px;"></td>
						</tr>
						<tr>
							<td style="width: 215px;">სახელი და გვარი</td>
							<td></td>
						</tr>
						<tr >
							<td style="width: 215px;"></td>	
							<td></td>	
						</tr>
						<tr >
							<td style="width: 215px;">ტელეფონი</td>
							<td></td>
						</tr>
						<tr>
							<td style="width: 215px;"></td>
							<td></td>
						</tr>
						<tr>
							<td style="width: 215px;">ელ-ფოსტა</td>
							<td></td>
						</tr>
						<tr>
							<td style="width: 215px;"></td>
							<td ></td>
						</tr>
					
						
						<tr>
							<td td style="width: 215px;">user-ი</td>
							<td td style="width: 215px;"></td>
						</tr>
						<tr>
							<td style="width: 215px;"></td>
							<td td style="width: 215px;"></td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend>მომართვის ისტორია</legend>
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
		<!-- ID -->
		<input type="hidden" id="req_id" value="' . $res['id'] . '" />
    </div>';

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
