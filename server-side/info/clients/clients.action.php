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
	  	$rResult = mysql_query("SELECT 	`client`.`id`,
										`client`.`id`,
										`client`.`code`,
										`legal_status`.`name`,
										`client`.`name`,
										`client`.`phone`,
										`client`.`mail`,
										`client`.`name`,
										`client`.`name`,
	  									`client`.`name`
								FROM 	`client`
								JOIN 	`legal_status` ON `client`.`legal_status_id` = `legal_status`.`id`");
	  
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

function get_addition_all_info($pin)
{
	$res1 = mysql_fetch_assoc(mysql_query("	SELECT COUNT(*) AS `a`
											FROM 			`site_user`
											JOIN 			incomming_call ON site_user.incomming_call_id = incomming_call.id
											LEFT JOIN 		task ON incomming_call.id = task.incomming_call_id
											WHERE 			site_user.pin = $pin AND incomming_call.call_type_id = 1 AND (task.`status` = 3 OR ISNULL(task.`status`))"));
	
	$res2 = mysql_fetch_assoc(mysql_query("	SELECT COUNT(*) AS `a1`
											FROM 			`site_user`
											JOIN 			incomming_call ON site_user.incomming_call_id = incomming_call.id
											LEFT JOIN 		task ON incomming_call.id = task.incomming_call_id
											WHERE 			site_user.pin = $pin AND incomming_call.call_type_id = 2 AND (task.`status` = 3 OR ISNULL(task.`status`))"));
	
	$res3 = mysql_fetch_assoc(mysql_query("	SELECT COUNT(*) AS `a2`
											FROM 			`site_user`
											JOIN 			incomming_call ON site_user.incomming_call_id = incomming_call.id
											LEFT JOIN 		task ON incomming_call.id = task.incomming_call_id
											WHERE 			site_user.pin = $pin AND incomming_call.call_type_id = 1 AND (task.`status` != 3 AND NOT ISNULL(task.`status`))"));
	
	$res4 = mysql_fetch_assoc(mysql_query("	SELECT COUNT(*) AS `a3`
											FROM 			`site_user`
											JOIN 			incomming_call ON site_user.incomming_call_id = incomming_call.id
											LEFT JOIN 		task ON incomming_call.id = task.incomming_call_id
											WHERE 			site_user.pin = $pin AND incomming_call.call_type_id = 2 AND (task.`status` != 3 AND NOT ISNULL(task.`status`))"));
	
	$sum_incomming	 	= $res1['a'] + $res2['a1'] + $res3['a2'] + $res4['a3'];
	$sum_end	 		= $res1['a'] + $res2['a1'];
	$sum_actived		= $res3['a2'] + $res4['a3'];
	
	$data .= '<fieldset>
					<legend>მომართვების ისტორია</legend>
					<table>
						<tr>
							<td>სულ</td>
							<td></td>
							<td></td>
							<td style="width: 150px;"></td>
							<td>'.$sum_incomming.'</td>
						</tr>
						<tr>
							<td></td>
							<td>მოგვარებულები</td>
							<td></td>
							<td style="width: 150px;"></td>
						    <td>'.$sum_end.'</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td>პრეტენზია</td>
							<td style="width: 150px;"></td>
							<td>'. $res1['a'].'</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td>ინფორმაცია</td>
							<td style="width: 150px;"></td>
							<td>'.$res2['a1'].'</td>
						</tr>
						<tr>
							<td></td>
							<td>პრეტენზია</td>
							<td></td>
							<td style="width: 150px;"></td>
							<td>'.$sum_actived.'</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td>პრეტენზია</td>
							<td style="width: 150px;"></td>
							<td>'.$res3['a2'].'</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td>ინფორმაცია</td>
							<td style="width: 150px;"></td>
							<td>'. $res4['a3'].'</td>
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
					</table>
				</fieldset>';

	return $data;
}
function get_addition_all_info1($personal_id)
{
	$res1 = mysql_fetch_assoc(mysql_query("	SELECT COUNT(*) AS `a`
											FROM 			`site_user`
											JOIN 			incomming_call ON site_user.incomming_call_id = incomming_call.id
											LEFT JOIN 		task ON incomming_call.id = task.incomming_call_id
											WHERE 			site_user.personal_id = $personal_id AND incomming_call.call_type_id = 1 AND (task.`status` = 3 OR ISNULL(task.`status`))"));

	$res2 = mysql_fetch_assoc(mysql_query("	SELECT COUNT(*) AS `a`
											FROM 			`site_user`
											JOIN 			incomming_call ON site_user.incomming_call_id = incomming_call.id
											LEFT JOIN 		task ON incomming_call.id = task.incomming_call_id
											WHERE 			site_user.personal_id = $personal_id AND incomming_call.call_type_id = 2 AND (task.`status` = 3 OR ISNULL(task.`status`))"));

	$res3 = mysql_fetch_assoc(mysql_query("	SELECT COUNT(*) AS `a`
											FROM 			`site_user`
											JOIN 			incomming_call ON site_user.incomming_call_id = incomming_call.id
											LEFT JOIN 		task ON incomming_call.id = task.incomming_call_id
											WHERE 			site_user.personal_id = $personal_id AND incomming_call.call_type_id = 1 AND (task.`status` != 3 AND NOT ISNULL(task.`status`))"));

	$res4 = mysql_fetch_assoc(mysql_query("	SELECT COUNT(*) AS `a`
											FROM 			`site_user`
											JOIN 			incomming_call ON site_user.incomming_call_id = incomming_call.id
											LEFT JOIN 		task ON incomming_call.id = task.incomming_call_id
											WHERE 			site_user.personal_id = $personal_id AND incomming_call.call_type_id = 2 AND (task.`status` != 3 AND NOT ISNULL(task.`status`))"));

	$sum_incomming	 	= $res1['a'] + $res2['a'] + $res3['a'] + $res4['a'];
	$sum_end	 		= $res1['a'] + $res2['a'];
	$sum_actived		= $res3['a'] + $res4['a'];

	$data .= '<fieldset>
					<legend>მომართვების ისტორია</legend>
					<table>
						<tr>
							<td>სულ</td>
							<td></td>
							<td></td>
							<td style="width: 150px;"></td>
							<td>'.$sum_incomming.'</td>
						</tr>
						<tr>
							<td></td>
							<td>მოგვარებულები</td>
							<td></td>
							<td style="width: 150px;"></td>
						    <td>'.$sum_end.'</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td>პრეტენზია</td>
							<td style="width: 150px;"></td>
							<td>'. $res1['a'].'</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td>ინფორმაცია</td>
							<td style="width: 150px;"></td>
							<td>'.$res2['a'].'</td>
						</tr>
						<tr>
							<td></td>
							<td>პრეტენზია</td>
							<td></td>
							<td style="width: 150px;"></td>
							<td>'.$sum_actived.'</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td>პრეტენზია</td>
							<td style="width: 150px;"></td>
							<td>'.$res3['a'].'</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td>ინფორმაცია</td>
							<td style="width: 150px;"></td>
							<td>'. $res4['a'].'</td>
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
					</table>
				</fieldset>';

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
	
	$data  .= '
	<!-- jQuery Dialog -->
    <div id="add-edit-goods-form" title="საქონელი">
    	<!-- aJax -->
	</div>
	<div id="dialog-form">
			<div style="float: left; width: 500px;">	
				<fieldset >
			    	<legend>საკონტაკტო ინფო</legend>
					<fieldset style="width:665px; float:left;">
				    	<table width="100%" class="dialog-form-table">
							<tr>
								<td>სტატუსი</td>
								<td>VIP კლიენტი</td>
								<td></td>
								<td></td>								
							</tr>
							<tr>
								<td>კონტრაგენტი</td>
								<td>
									<input type="text" id="name" class="idle" onblur="this.className=\'idle\'"  value="' . $res['name']. '"  />
								</td>
								<td>მობილური 1</td>
								<td>
									<input type="text" id="mobile1" class="idle" onblur="this.className=\'idle\'"  value="' . $res['mobile1']. '"  />
								</td>
											
							</tr>
							<tr>
								<td>იურ. სტატუსი</td>
								<td>
									<input type="text" id="legal_status" class="idle" onblur="this.className=\'idle\'"  value="' . $res['legal_status_id']. '" />
								</td>
								<td>მობილური 2</td>
								<td>
									<input type="text" id="mobile2" class="idle" onblur="this.className=\'idle\'"  value="' . $res['mobile2']. '"  />
								</td>
										
							</tr>	
							<tr>
								<td>პირადი ნომერი</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'"  value="' . $res['id']. '"  />
								</td>
								<td>ტელეფონი</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'"  value="' . $res['id']. '"  />
								</td>		
							</tr>
							<tr>
								<td>დაბ. თარიღი</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'"  value="' . $res['id']. '"  />
								</td>
								<td>ელ-ფოსტა</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'"  value="' . $res['id']. '"  />
								</td>
							</tr>					
						</table>
					</fieldset>
					<fieldset style="width:665px; float:left;">
						<legend>მისამართი</legend>
				    	<table width="100%" class="dialog-form-table">
							<tr>
								<td style="font-weight:bold;">იურიდიული</td>
								<td></td>
								<td style="font-weight:bold;">ფაქტიური</td>
								<td></td>
							</tr>
							<tr>
								<td>მისამართი</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'"  value="' . $res['id']. '"  />
								</td>
								<td>მისამართი</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'"  value="' . $res['id']. '"  />
								</td>			
							</tr>
							<tr>
								<td>ქალაქი</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'"  value="' . $res['id']. '"  />
								</td>
								<td>ქალაქი</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'"  value="' . $res['id']. '"  />
								</td>			
							</tr>	
							<tr>
								<td>საფოსტო კოდი</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'"  value="' . $res['id']. '"  />
								</td>
								<td>საფოსტო კოდი</td>
								<td>
									<input type="text" id="id" class="idle" onblur="this.className=\'idle\'"  value="' . $res['id']. '"  />
								</td>			
							</tr>
							<tr>
								<td></td>
								<td></td>
								<td>კოპირება</td>
								<td>
									<input type="checkbox" value="">
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
			<div style="float: right; width: 450px;">
				<fieldset>
				<legend>საჩუქრები</legend>
				<div id="dt_example" class="inner-table">
		        <div style="width:440px;" id="container" >        	
		            <div id="dynamic">
		            	<div id="button_area">
		            		<button id="add_button_p">დამატება</button>
	        			</div>
		                <table class="" id="examplee" style="width: 100%;">
		                    <thead>
								<tr  id="datatable_header">
										
		                           <th style="display:none">ID</th>
									<th style="width:9%;">#</th>
									<th style=" word-break:break-all;">თარიღი</th>
									<th style=" word-break:break-all;">პროდუქტი</th>
									<th style=" word-break:break-all;">თანხა</th>
								</tr>
							</thead>
							<thead>
								<tr class="search_header">
									<th class="colum_hidden">
                            			<input type="text" name="search_id" value="ფილტრი" class="search_init" style="width: 20px"/>
                            		</th>
									<th>
										<input style="width:100px;" type="text" name="search_overhead" value="ფილტრი" class="search_init" />
									</th>
									<th>
										<input style="width:100px;" type="text" name="search_partner" value="ფილტრი" class="search_init" />
									</th>
									<th>
										<input style="width:100px;" type="text" name="search_overhead" value="ფილტრი" class="search_init" />
									</th>
									
									
								</tr>
							</thead>
		                </table>
		            </div>
		            <div class="spacer">
		            </div>
		        </div>
				</fieldset>
				<div id="additional_info">';
					if (!empty($res['personal_pin'])){
							$data .= get_addition_all_info($res['personal_pin']);
						}
	  $data .= '</div>
				<fieldset style="width: 440px;">
					<legend>შენაძენი</legend> 
					<table style="float: left; border: 1px solid #85b1de; width: 100%; text-align: center;">
						<tr style="border-bottom: 1px solid #85b1de;">
							<td style="border-right: 1px solid #85b1de; padding: 3px 9px; color: #3C7FB1;">#</td>
	  						<td style="border-right: 1px solid #85b1de; padding: 3px 9px; color: #3C7FB1;">ფილიალი</td>
	  						<td style="border-right: 1px solid #85b1de; padding: 3px 9px; color: #3C7FB1;">თარიღი</td>
	  						<td style="border-right: 1px solid #85b1de; padding: 3px 9px; color: #3C7FB1;">პროდუქტი</td>
	  						<td style="border-right: 1px solid #85b1de; padding: 3px 9px; color: #3C7FB1;">თანხა</td>
						</tr>
						<tr style="border-bottom: 1px solid #85b1de; ">
							<td style="border-right: 1px solid #85b1de; padding: 3px 9px; word-break:break-all">1</td>
	  						<td style="border-right: 1px solid #85b1de; padding: 3px 9px; word-break:break-all"></td>
	  						<td style="border-right: 1px solid #85b1de; padding: 3px 9px; word-break:break-all"></td>
	  						<td style="border-right: 1px solid #85b1de; padding: 3px 9px; word-break:break-all"></td>	
	  						<td style="border-right: 1px solid #85b1de; padding: 3px 9px; word-break:break-all"></td>		
						</tr>						
					<table/>
				</fieldset>
	  			<fieldset style="width: 440px;">
					<legend>საუბრის ჩანაწერი</legend> 
	  				<table style="float: left; border: 1px solid #85b1de; width: 250px; text-align: center; margin-left:100px;">
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