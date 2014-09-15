<?php

/* ******************************
 *	Request aJax actions
* ******************************
*/

require_once('../../../includes/classes/core.php');
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

$pay_type_id			= $_REQUEST['pay_type_id'];
$bank_id				= $_REQUEST['bank_id'];
$card_type_id			= $_REQUEST['card_type_id'];
$pay_aparat_id			= $_REQUEST['pay_aparat_id'];
$object_id				= $_REQUEST['object_id'];


// site_user
$personal_pin			= $_REQUEST['personal_pin'];
$personal_id			= $_REQUEST['personal_id'];
$personal_phone			= $_REQUEST['personal_phone'];
$mail				    = $_REQUEST['mail'];
$name				    = $_REQUEST['name'];
$friend_pin				= $_REQUEST['friend_pin'];

//task
$persons_id			    = $_REQUEST['persons_id'];
$task_type_id			= $_REQUEST['task_type_id'];
$priority_id			= $_REQUEST['priority_id'];
$comment 	        	= $_REQUEST['comment'];
$task_department_id 	= $_REQUEST['task_department_id'];
$hidden_inc				= $_REQUEST['hidden_inc'];
$edit_id				= $_REQUEST['edit_id'];
$delete_id				= $_REQUEST['delete_id'];

// file
$rand_file				= $_REQUEST['rand_file'];
$file					= $_REQUEST['file_name'];


switch ($action) {
	case 'get_add_page':
		$number		= $_REQUEST['number'];
		$page		= GetPage($res='', $number);
		$data		= array('page'	=> $page);

		break;
	case 'disable':
		$incom_id				= $_REQUEST['id'];
		mysql_query("			UPDATE `incomming_call`
									    SET `actived`=0
										WHERE `id`=$incom_id ");
		break;
	case 'get_edit_page':
		$page		= GetPage(Getincomming($incom_id));

		$data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count = 		$_REQUEST['count'];
		$hidden = 		$_REQUEST['hidden'];
	  	$rResult = mysql_query("select  		incomming_call.id,           
												incomming_call.id,
											  	DATE_FORMAT(incomming_call.`date`,'%d-%m-%y %H:%i:%s'),
												category.`name`,
												site_user.`pin`,
												incomming_call.phone,				
												site_user.`name`,
	  											'' AS `time`,
	  											incomming_call.call_content
								FROM 			incomming_call
								LEFT JOIN		site_user ON incomming_call.id=site_user.incomming_call_id
								LEFT JOIN 		category  ON incomming_call.call_category_id=category.id
	  							WHERE incomming_call.actived = 1");
	  
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
	case 'save_incomming':
		$incom_id = $_REQUEST['id'];
		$task_type_id = $_REQUEST['task_type_id'];
		if($incom_id == ''){
			
			Addincomming( $phone,  $call_type_id, $category_id, $category_parent_id, $object_id, $pay_type_id, $bank_id, $card_type_id, $pay_aparat_id, $problem_date,  $call_content,$file,$rand_file,$hidden_inc);
			$incomming_call_id = mysql_insert_id();
			if($task_type_id != 0){
			Addtask($incomming_call_id, $persons_id, $task_type_id,  $priority_id, $task_department_id,  $comment);
			}
			Addsite_user($incomming_call_id, $personal_pin, $friend_pin, $personal_id);
		
		}else {
			
			Saveincomming($call_type_id, $phone, $category_id, $category_parent_id, $object_id, $pay_type_id, $bank_id, $card_type_id, $pay_aparat_id,  $problem_date, $call_content,$file,$rand_file);
			
			Savetask($incom_id, $persons_id,  $task_type_id, $priority_id, $task_department_id, $comment);
			
			//Savesite_user($incom_id, $personal_pin, $name, $personal_phone, $mail,  $personal_id);
			
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
					WHERE   `incomming_call_id` = $edit_id
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
				`incomming_call_id`,
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
				WHERE   `incomming_call_id` = $edit_id
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
	case 'set_task_type':
	
		$cat_id	=	$_REQUEST['cat_id'];
		$data 	= 	array('cat'=>Getbank_object($cat_id));
	
		break;
	
	case 'get_add_info':
	
		$pin	=	$_REQUEST['pin'];
		$data 	= 	array('info' => get_addition_all_info($pin));
	
		break;
		case 'get_add_info1':
		
		$personal_id	=	$_REQUEST['personal_id'];
		$data 	= 	array('info1' => get_addition_all_info1($personal_id));
	
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

function Addincomming(  $phone,  $call_type_id, $category_id, $category_parent_id, $object_id, $pay_type_id, $bank_id, $card_type_id, $pay_aparat_id, $problem_date,  $call_content,$file,$rand_file,$hidden_inc){
	
	$c_date		= date('Y-m-d H:i:s');
	$user		= $_SESSION['USERID'];
	
	mysql_query("INSERT INTO `incomming_call` 
			(`user_id`, `date`, `phone`, `call_type_id`, `call_category_id`, `call_subcategory_id`, `object_id`, `pay_type_id`, `bank_id`, `bank_object_id`, `card_type_id`, `pay_aparat_id`, `problem_date`, `call_content`, `actived`)
			 VALUES 
			( '$user', '$c_date', '$phone', '$call_type_id', '$category_id', '$category_parent_id', '$object_id', '$pay_type_id', '$bank_id', '', '$card_type_id', '$pay_aparat_id', '$problem_date', '$call_content', '1');");
	
	if($rand_file != ''){
		mysql_query("INSERT INTO 	`file`
		( 	`user_id`,
		`incomming_call_id`,
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

function Addtask($incomming_call_id, $persons_id, $task_type_id,  $priority_id, $task_department_id,  $comment)
{
	
	$user		= $_SESSION['USERID'];
	mysql_query("INSERT INTO	`task` 
									(`user_id`,
									 `date`,
									 `responsible_user_id`,
									 `incomming_call_id`,
									 `task_type_id`,
									 `priority_id`,
									 `department_id`,
									 `phone`,
									 `comment`,
									 `problem_comment`)
						VALUES
									('$user',
									  NULL,
									 '$persons_id',
									 '$incomming_call_id',
									 '$task_type_id',
									 '$priority_id',
								     '$task_department_id',
								      NULL, 
								     '$comment', 
								     NULL)");
	
	
}

function Addsite_user($incomming_call_id, $personal_pin, $friend_pin, $personal_id)
{
	
	$user		= $_SESSION['USERID'];
	mysql_query("INSERT INTO `site_user` 	(`incomming_call_id`, `site`, `pin`, `friend_pin`, `name`, `phone`, `mail`, `personal_id`, `user`)
						           		 VALUES 
											( '$incomming_call_id', '243', '$personal_pin', '$friend_pin', '3414', 12412341, '124124124', '$personal_id', '$user')");

}
				
function Saveincomming($call_type_id, $phone, $category_id, $category_parent_id, $object_id, $pay_type_id, $bank_id, $card_type_id, $pay_aparat_id,  $problem_date, $call_content,$file,$rand_file)
{
	$incom_id	= $_REQUEST['id'];
	$user		= $_SESSION['USERID'];
	$c_date		= date('Y-m-d H:i:s');
	mysql_query("UPDATE  `incomming_call` 
				SET  
						 `user_id`				='$user', 
						 `date`					='$c_date',
						 `phone`				='$phone', 
						 `call_type_id`			='$call_type_id',
						 `call_category_id`		='$category_id',
						 `call_subcategory_id`	='$category_parent_id', 
						 `object_id`			='$object_id',
						 `pay_type_id`			='$pay_type_id',
						 `bank_id`				='$bank_id',
						`card_type_id`			='$card_type_id', 
						 `pay_aparat_id`		='$pay_aparat_id',
						 `problem_date`			='$problem_date',
						`call_content`			='$call_content',
						 `actived`				='1'
			    WHERE     `id`					='$incom_id'
							");
	

}       
function Savetask($incom_id, $persons_id,  $task_type_id, $priority_id, $task_department_id, $comment)
{

	$user  = $_SESSION['USERID'];
	mysql_query("UPDATE `task` SET  	 `user_id`='$user',
									 	 `responsible_user_id`='$persons_id',
									 	 `task_type_id`='$task_type_id',
										 `priority_id`='$priority_id', 
										 `task_department_id`='$task_department_id', 
										 `comment`='$comment' 
										  WHERE (`incomming_call_id`='$incom_id');");

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


}

function Getincomming($incom_id)
{
$res = mysql_fetch_assoc(mysql_query("	SELECT    	incomming_call.id AS id,
													incomming_call.phone AS `phone`,
													DATE_FORMAT(incomming_call.`date`,'%d-%m-%y %H:%i:%s') AS call_date,
													incomming_call.call_type_id AS call_type_id,
													incomming_call.call_category_id AS category_id,
													IF(ISNULL(task.`status`), 3, task.`status`) AS `status`,
													incomming_call.call_subcategory_id AS category_parent_id,
													DATE_FORMAT(incomming_call.`date`,'%d-%m-%y %H:%i:%s') AS problem_date,
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
													site_user.user AS user,
													task.task_type_id AS task_type_id,
													task.responsible_user_id AS persons_id,
													task.priority_id AS priority_id,
													task.department_id AS task_department_id,
													task.`comment` AS `comment`
										FROM 	   	incomming_call
										LEFT JOIN  	site_user ON incomming_call.id=site_user.incomming_call_id
										LEFT JOIN  	task  ON incomming_call.id=task.incomming_call_id
										where      	incomming_call.id = $incom_id
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
							WHERE   `incomming_call_id` = $res[id]
			");
	
	$data  .= '
	<!-- jQuery Dialog -->
    <div id="add-edit-goods-form" title="საქონელი">
    	<!-- aJax -->
	</div>
	<div id="dialog-form">
			<div style="float: left; width: 400px;">	
				<fieldset >
			    	<legend>ძირითადი ინფორმაცია</legend>
					<fieldset style="width:300px; float:left;">
				    	<table width="100%" class="dialog-form-table">
							<tr>
								<td>ზედნადების #</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'" value="' . $res['id']. '"/>
								</td>
							</tr>
							<tr>
								<td>ქვე-განყოფილება</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'" value="' . $res['id']. '"/>
								</td>
							</tr>
							<tr>
								<td>საწყობი</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'" value="' . $res['id']. '"/>
								</td>
							</tr>	
							<tr>
								<td>სხვა</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'" value="' . $res['id']. '"/>
								</td>
							</tr>					
						</table>
					</fieldset>
					<fieldset style="width:300px; float:left; margin-left: 10px;">
				    	<table width="100%" class="dialog-form-table">
							<tr>
								<td>შეძენის თარიღი</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'"  value="' . $res['id']. '"/>
								</td>
							</tr>
							<tr>
								<td>მიტანის თარიღი</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'" value="' . $res['id']. '"/>
								</td>
							</tr>
							<tr>
								<td>მონტაჟის თარიღი</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'"  value="' . $res['id']. '"/>
								</td>
							</tr>
							<tr>
								<td>სტატუსი</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'" value="' . $res['id']. '"/>
								</td>
							</tr>						
						</table>
					</fieldset>						
											';
												
		$data  .= '
		   
				<fieldset style="margin-top: 5px;">
			    	<legend>დავალების ფორმირება</legend>
		
			    	<table class="dialog-form-table">
						<tr>
							<td style="width: 180px;"><label for="d_number">დავალების ტიპი</label></td>
							<td style="width: 180px;"><label for="d_number">სცენარი</label></td>
							<td style="width: 180px;"><label for="d_number">პრიორიტეტი</label></td>
						</tr>
			    		<tr>
							<td style="width: 180px;" id="task_type_change"><select id="task_type_id" class="idls object">'.Gettask_type($res['task_type_id']).'</select></td>
							<td style="width: 180px;"><select id="task_department_id" class="idls object">'. Getdepartment($res['task_department_id']).'</select></td>
							<td style="width: 180px;"><select id="persons_id" class="idls object">'.Getpersons($res['persons_id']).'</select></td>
						</tr>
						<tr>
							<td style="width: 150px;"><label for="content">კომენტარი</label></td>
							<td style="width: 150px;"><label for="content"></label></td>
							<td style="width: 150px;"><label for="content"></label></td>
						</tr>
						<tr>
							<td colspan="6">
								<textarea  style="width: 627px; height: 80px; resize: none;" id="comment" class="idle" name="content" cols="300" rows="2">' . $res['comment'] . '</textarea>
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
					<legend>კონტრაგენტი</legend>
					<table style="height: 243px;">						
						<tr>
							<td style="width: 180px; color: #3C7FB1;">ტელეფონი</td>
							<td style="width: 180px; color: #3C7FB1;">პირადი ნომერი</td>
						</tr>
						<tr>
							<td>568919432</td>
							<td style="width: 180px;">
								<input type="text" id="personal_id" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['personal_id'] . '" />
							</td>					
						</tr>
						<tr>
							<td style="width: 180px; color: #3C7FB1;">სახელი და გვარი</td>
							<td style="width: 180px; color: #3C7FB1;">ელ-ფოსტა</td>
						</tr>
						<tr >
							<td style="width: 180px;">ზაზა მესხი</td>
							<td style="width: 180px;">z.mesxi@yahoo.com</td>			
						</tr>
						<tr>
							<td td style="width: 180px; color: #3C7FB1;">მისამართი</td>
							<td td style="width: 180px; color: #3C7FB1;">სტატუსი</td>
						</tr>
						<tr>
							<td style="width: 180px;">ყვარლის 149</td>
							<td td style="width: 180px;">VIP კლიენტი</td>
						</tr>
					</table>
				</fieldset>
				<div id="additional_info">';
					if (!empty($res['personal_pin'])){
							$data .= get_addition_all_info($res['personal_pin']);
						}
	  $data .= '</div>
				<fieldset>
					<legend>შენაძენი</legend> 
					<table style="float: left; border: 1px solid #85b1de; width: 100%; text-align: center;">
						<tr style="border-bottom: 1px solid #85b1de;">
							<td style="border-right: 1px solid #85b1de; padding: 3px 9px; color: #3C7FB1;">#</td>
	  						<td style="border-right: 1px solid #85b1de; padding: 3px 9px; color: #3C7FB1;">ნომენკლატურა</td>
	  						<td style="border-right: 1px solid #85b1de; padding: 3px 9px; color: #3C7FB1;">ფასი</td>
	  						<td style="border-right: 1px solid #85b1de; padding: 3px 9px; color: #3C7FB1;">სხვა</td>
						</tr>
						<tr style="border-bottom: 1px solid #85b1de; ">
							<td style="border-right: 1px solid #85b1de; padding: 3px 9px; word-break:break-all">1</td>
	  						<td style="border-right: 1px solid #85b1de; padding: 3px 9px; word-break:break-all"></td>
	  						<td style="border-right: 1px solid #85b1de; padding: 3px 9px; word-break:break-all"></td>
	  						<td style="border-right: 1px solid #85b1de; padding: 3px 9px; word-break:break-all"></td>							
						</tr>						
					<table/>
	  				<table style="float: left; width: 100%; text-align: center;">
	  					<tr>
	  						<td style="width: 10%;"></td>
	  						<td style="text-align: right; width: 49%;">ჯამი</td>
	  						<td style="width: 20%;"></td>
	  						<td style="width: 20%;"></td>
	  					</tr>
	  				<table/>
				</fieldset>
	  			<fieldset>
					<legend>საუბრის ჩანაწერი</legend> 
	  				<table style="float: left; border: 1px solid #85b1de; width: 250px; text-align: center; margin-left:40px;">
						<tr style="border-bottom: 1px solid #85b1de;">
							<td style="border-right: 1px solid #85b1de; padding: 3px 9px; width:200px; color: #3C7FB1;">დრო</td>
	  						<td style="border-right: 1px solid #85b1de; padding: 3px 9px; width:200px; color: #3C7FB1;">ჩანაწერი</td>
						</tr>
						<tr style="border-bottom: 1px solid #85b1de; ">
							<td style="border-right: 1px solid #85b1de; padding: 3px 9px; word-break:break-all">10:05:12 AM</td>
	  						<td style="border-right: 1px solid #85b1de; padding: 3px 9px; word-break:break-all">მოსმენა</td>
	  					</tr>
					<table/>
				</fieldset>
			</div>
    </div>';

	return $data;
}

?>