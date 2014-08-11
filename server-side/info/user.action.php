<?php
/* ******************************
 *	Workers aJax actions
 * ******************************
 */
include('../../includes/classes/core.php');
include('../../includes/classes/log.class.php');

$log 		= new log();


$action 	= $_REQUEST['act'];
$user_id	= $_SESSION['USERID'];
$error 		= '';
$data 		= '';

switch ($action) {
	case 'get_add_page':
		$page		= GetPage();
		$data		= array('page'	=> $page);

		break;
	case 'get_edit_page':
	    $per_id		= $_REQUEST['id'];
		$page		= GetPage(GetWorker($per_id));

        $data		= array('page'	=> $page);

	    break;
	case 'get_list':
	    $count = $_REQUEST['count'];
	    $hidden = $_REQUEST['hidden'];
		$rResult = mysql_query("	SELECT `persons`.`id`,
                                           `persons`.`name`,
                                           `persons`.`tin`,
                                           `position`.`person_position`,
                                           `persons`.`address`
								    FROM   `persons` INNER JOIN `position`
									ON     `persons`.`position` = `position`.`ID`
									WHERE  `persons`.`actived` = 1");

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
	case 'get_pages_list':
		$count = $_REQUEST['count'];
		$hidden = $_REQUEST['hidden'];
		$rResult = mysql_query("SELECT    `pages`.`id`,
								          `menu_detail`.`title`
								FROM      `pages`
								LEFT JOIN `menu_detail` ON `menu_detail`.`page_id` = `pages`.`id`
								WHERE     (`menu_detail`.`parent` != 0 && menu_detail.url = '#') || (menu_detail.url = '')");

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
					$row[] = '<input type="checkbox" name="check_' . $aRow[$hidden] . '" class="check1" value="' . $aRow[$hidden] . '" />';
				}
			}
			$data['aaData'][] = $row;
		}

		break;
    case 'save_pers':
		$persons_id 			= $_REQUEST['id'];
    	$name 				= htmlspecialchars($_REQUEST['n'], ENT_QUOTES);
		$tin 				= $_REQUEST['t'];
		$position 			= $_REQUEST['p'];
		$address 			= htmlspecialchars($_REQUEST['a'], ENT_QUOTES);
		$image				= $_REQUEST['img'];
		$password			= $_REQUEST['pas'];
		$home_number		= $_REQUEST['h_n'];
		$mobile_number		= $_REQUEST['m_n'];
		$comment			= $_REQUEST['comm'];
		$user				= $_REQUEST['user'];
		$userpassword		= md5($_REQUEST['userp']);
		$group_permission	= $_REQUEST['gp'];

		$CheckUser 			= CheckUser($user);


		if(empty($persons_id)){
			if($CheckUser){
				AddWorker($user_id, $name, $tin, $position, $address, $image, $password, $home_number, $mobile_number, $comment,  $user, $userpassword, $group_permission);
				}else{
					$error = "მომხმარებელი ასეთი სახელით  უკვე არსებობს\nაირჩიეთ სხვა მომხმარებლის სახელი";
				}
		}else{
			SaveWorker($persons_id, $user_id, $name, $tin, $position, $address, $image, $password, $home_number, $mobile_number, $comment,  $user, $userpassword, $group_permission);
		}


        break;
	case 'save_group':
		$group_name		= $_REQUEST['nam'];
		$group_pages	= json_decode(stripslashes($_REQUEST['pag']));
  		$data		= array(
  			'inserted_value'	=> SaveGroup($group_name, $group_pages),
  			'inserted_name'		=> $group_name
  		);

  		GLOBAL $log;
  		$log->setInsertLog('group');

		break;
    case 'get_add_group_page':
    	$page		= GetGroupPage();
    	$data		= array('page'	=> $page);

    	break;
    case 'disable':
		$per_id = $_REQUEST['id'];
		DisableWorker($per_id);

        break;
	case 'delete_image':
		$pers_id 		= $_REQUEST['id'];
		DeleteImage($pers_id);

		break;
	case 'clear':
		$file_list = $_REQUEST['file'];
		ClearProduct();
		if (!empty($file_list)) {
			$file_list = ClearFiles(json_decode($file_list));
		}
		$data = array('file_list' => json_encode($file_list));




    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Workers Functions
 * ******************************
 */
function CheckUser($user){
	$res = mysql_query("SELECT 	  `username`
							FROM  `users`
							WHERE `username` = '$user'");

	if(mysql_num_rows($res) > 0){
		return false;
	}

	return true;
}

function SaveGroup($group_name, $group_pages){
	mysql_query("INSERT	INTO `group`
						(`group`.`name`)
				VALUES
						('$group_name')");

	$group_id = mysql_insert_id();



	$parrentaray = array();
	foreach($group_pages as $group_page) {
		mysql_query("INSERT	INTO `group_permission`
						(`group_permission`.`group_id`, `group_permission`.`page_id`)
					VALUES
						('$group_id','$group_page')");


		$res = mysql_fetch_assoc( mysql_query("	SELECT		`menu_detail`.`parent` as `parent_id`
												FROM		`pages`
												LEFT JOIN	`menu_detail` ON `menu_detail`.`page_id` = `pages`.`id`
												LEFT JOIN	`menu_detail` as `menu_detail1` ON `menu_detail1`.`id` =  `menu_detail`.`parent`
												WHERE		`pages`.`id` = '$group_page' AND `menu_detail`.`parent` != 0 "));
		if( !in_array($res['parent_id'], $parrentaray) ){
			array_push($parrentaray, $res['parent_id']);
		}
	}
	$res = mysql_fetch_assoc( mysql_query("	SELECT	`pages`.`id` as `id`
											FROM	`pages`
											WHERE	`pages`.`name` = 'logout'"));
	mysql_query("INSERT	INTO `group_permission`
					(`group_permission`.`group_id`, `group_permission`.`page_id`)
				VALUES
					('$group_id','$res[id]')");


	mysql_query("INSERT	INTO `group_permission`
					(`group_permission`.`group_id`, `group_permission`.`page_id`)
				VALUES
					('$group_id','31')");


	foreach($parrentaray as $parrent) {
		mysql_query("INSERT	INTO `group_permission`
						(`group_permission`.`group_id`, `group_permission`.`page_id`)
					VALUES
						('$group_id','$parrent')");

	}

	return $group_id;
}

function ClearProduct() {
	$req = mysql_query("SELECT	`id`,
    							`name`
						FROM `persons`");

	while( $res = mysql_fetch_assoc($req)){
		$name = htmlspecialchars($res[name], ENT_QUOTES);

		GLOBAL $log;
		$log->setUpdateLogBefore('persons', $res[id]);

		mysql_query("	UPDATE
		`persons`
		SET
		`name`	= '$name'
		WHERE
		`id`	= '$res[id]'");

		$log->setUpdateLogAfter('persons', $res[id]);
	}
}

function AddWorker($user_id, $name, $tin, $position, $address, $image, $password, $home_number, $mobile_number, $comment,  $user, $userpassword, $group_permission)
{
	mysql_query("INSERT INTO `persons`
					(`user_id`, `name`, `tin`, `position`, `address`, `image`,`password`, `home_number`, `mobile_number`, `comment`)
				 VALUES
					($user_id, '$name', '$tin', $position, '$address', '$image','$password', '$home_number', '$mobile_number', '$comment')");
	$persons_id = mysql_insert_id();
	GLOBAL $log;
	$log->setInsertLog('persons');

	if( $user!= '' && $userpassword!='' && $group_permission!=''){
		mysql_query("INSERT	INTO	`users`
						(`username`, `password`, `person_id`, `group_id`)
					VALUES
						('$user','$userpassword','$persons_id','$group_permission')");
		$log->setInsertLog('users');
	}
}

function SaveWorker($persons_id, $user_id, $name, $tin, $position, $address, $image, $password, $home_number, $mobile_number, $comment, $user, $userpassword, $group_permission)
{
	GLOBAL $log;
	$log->setUpdateLogBefore('persons', $persons_id);

	mysql_query("UPDATE
	    			`persons`
				 SET
				 	`user_id`		= '$user_id',
				    `name`			= '$name',
				    `tin`			= '$tin',
				    `position`		= $position,
				    `address`		= '$address',
				    `image`			= '$image',
				    `password`  	= '$password',
				    `home_number`	= '$home_number',
				    `mobile_number` = '$mobile_number',
				    `comment`		= '$comment'
				 WHERE
					`id`		= $persons_id");

	$log->setUpdateLogAfter('persons', $persons_id);

	if( $user!= '' && $userpassword!='' && $group_permission!=''){
		$res = mysql_fetch_assoc( mysql_query("	SELECT	`users`.`id`
												FROM		`users`
												WHERE		`users`.`person_id` = '$persons_id'"));
		if( $res != '' ){
			GLOBAL $log;
			$log->setUpdateLogBefore('users', $persons_id);

			mysql_query("	UPDATE	`users`
							LEFT JOIN	`persons` ON `persons`.`id` = `users`.`person_id`
							SET
											`users`.`username` = '$user',
											`users`.`password` = '$userpassword',
											`users`.`group_id` = '$group_permission'
							WHERE		`users`.`person_id` = '$persons_id'	&& `persons`.actived = 1 && `users`.actived = 1");

			$log->setUpdateLogAfter('users', $persons_id);

		}else{
			mysql_query("INSERT	INTO	`users`
							(`users`.`username`, `users`.`password`, `users`.`person_id`, `users`.`group_id`)
						VALUES
							('$user','$userpassword','$persons_id','$group_permission')");
				GLOBAL $log;
				$log->setInsertLog('users');
		}

	}
}

function DisableWorker($per_id)
{
	GLOBAL $log;
	$log->setUpdateLogBefore('persons', $per_id);

    mysql_query("UPDATE `persons`
				 SET    `actived` = 0
				 WHERE  `id` = '$per_id'");

    $log->setUpdateLogAfter('persons', $per_id);


    $log->setUpdateLogBefore('users', $per_id);

    mysql_query("UPDATE `users`
				 SET    `actived` = 0
				 WHERE  `users`.`person_id` = '$per_id'");

    $log->setUpdateLogAfter('users', $per_id);
}

function GetPosition($point)
{
	$data = '';
    $req = mysql_query("SELECT 	`id`,
    						   	`person_position`
						FROM 	`position`
						WHERE 	`visible` = 'Yes'");

	if($point == ''){
		$data = '<option value="0" selected="selected"></option>';
	}

	while( $res = mysql_fetch_assoc( $req )){
		if($res['id'] == $point){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['person_position'] . '</option>';
		} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['person_position'] . '</option>';
		}
	}

	return $data;
}

function GetWorker($per_id)
{
    $res = mysql_fetch_assoc(mysql_query("	SELECT	`persons`.`id` as `id`,
													`persons`.`name` as `name`,
													`persons`.`tin` as `tin`,
													`persons`.`position` as `position`,
													`persons`.`address` as `address`,
													`persons`.`image` as `image`,
													`persons`.`password` as `password`,
													`users`.`username` as `username`,
													`users`.`password` as `user_password`,
													`users`.`group_id` as `group_id`,
													`persons`.`home_number` as `home_number`,
													`persons`.`mobile_number` as `mobile_number`,
    												`persons`.`comment` as `comment`
											FROM	`persons`
											LEFT JOIN	`users` ON `users`.`person_id` = `persons`.`id`
											WHERE	`persons`.`id` = '$per_id'"));
	return $res;
}

function DeleteImage($pers_id)
{
	GLOBAL $log;
	$log->setUpdateLogBefore('persons', $pers_id);

	mysql_query("UPDATE
	`persons`
	SET
	`image`			= NULL
	WHERE
	`id`			= $pers_id");

	$log->setUpdateLogAfter('persons', $pers_id);
}

function GetGroupPermission( $group_id ){
	$data = '';
	$req = mysql_query("SELECT	`group`.id as `id`,
								`group`.`name` as `name`
						FROM	`group`");

	if($group_id == ''){
		$data = '<option value="0" selected="selected"></option>';
	}

	while( $res = mysql_fetch_assoc( $req )){
		if($res['id'] == $group_id){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}

	return $data;
}

function GetGroupPage(){
	$data = '
	<div id="dialog-form">
 	    <fieldset style="width: 400px;">
	    	<legend>ჯგუფი</legend>
			<div style=" margin-top: 2px; ">
				<div style="width: 170px; display: inline;">
					<label for="group_name">ჯგუფის სახელი :</label>
					<input type="text" id="group_name" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" style="display: inline; margin-left: 25px;"/>
				</div>
			</div>
        </fieldset>
 	    <fieldset>
	    	<legend>გვერდები</legend>
            <div id="dynamic">
                <table class="display" id="pages" style="width: 380px !important; ">
                    <thead>
                        <tr style=" white-space: no-wrap;" id="datatable_header">
                            <th >ID</th>
                            <th style="width: 315px  !important;">გვერდის სახელი</th>
                            <th style="width: 65px !important;">#</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </fieldset>
    </div>
    ';
	return $data;
}

function GetPage($res = '')
{
	$image = $res['image'];
	if(empty($image)){
		$image = '0.jpg';
	}
	$data = '
	<div id="dialog-form">
	    <fieldset>
	    	<legend>ძირითადი ინფორმაცია</legend>

	    	<table class="dialog-form-table">
				<tr>
					<td style="width: 170px;"><label for="name">სახელი, გვარი</label></td>
					<td>
						<input type="text" id="name" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['name'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="tin">პირადი ნომერი</label></td>
					<td>
						<input type="text" id="tin" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['tin'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="position">თანამდებობა</label></td>
					<td>
						<select id="position" class="idls">' . GetPosition($res['position']) . '</select>
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="address">მისამართი</label></td>
					<td>
						<input type="text" id="address" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['address'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="home_number">სახლის ტელ: </label></td>
					<td>
						<input type="text" id="home_number" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['home_number'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="mobile_number">მობილური ტელ: </label></td>
					<td>
						<input type="text" id="mobile_number" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['mobile_number'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="comment">შენიშვნა: </label></td>
					<td valign="top">
						<textarea id="comment" class="idle large" cols="40" rows="4" style="width: 226px !important;">' . $res['comment'] . '</textarea>
					</td>
				</tr>
			</table>
			<!-- ID -->
			<div id="accordion">
			  <h3>მომხმარებელი</h3>
			  <div>
				<div>
					<div style="width: 170px; display: inline;"><label for="user">მომხმარებელი :</label>
						<input type="text" id="user" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['username'] . '" style="display: inline; margin-left: 42px;"/>
					</div>
				</div>
				<div style=" margin-top: 2px; ">
					<div style="width: 170px; display: inline;"><label for="user_password">პაროლი :</label>
						<input type="password" id="user_password" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['user_password'] . '" style="display: inline; margin-left: 84px;"/>
					</div>
				</div>
				<div style=" margin-top: 2px; ">
					<div style="width: 170px; display: inline; margin-top: 5px;"><label for="group_permission">ჯგუფი :</label>
						<select id="group_permission" class="idls" style="display: inline; margin-left: 101px;">' . GetGroupPermission( $res['group_id'] ) . '</select>
					</div>
				</div>
				<div style=" margin-top: 2px; ">
					<button id="add_group" style="outline:none; float: right; margin-right: 20px;">ჯგუფის დამატება</button>
				</div>
			  </div>
			</div>
        </fieldset>
 	    <fieldset>
	    	<legend>ტანამშრომლის სურათი</legend>

	    	<table class="dialog-form-table" width="100%">
	    		<tr>
					<td id="img_colum" colspan="2">
						<img id="upload_img" src="media/uploads/images/worker/' . $image . '">
					</td>
				</tr>
				<tr><!-- Upload Image -->
					<td id="act">
						<span>
							<a href="#" id="view_image" class="complate">View</a> | <a href="#" id="delete_image" class="delete">Delete</a>
						</span>
					</td>
					<td>
						<div class="file-uploader">
							<input id="choose_file" type="file" name="choose_file" class="input" style="display: none;">
							<button id="choose_button" class="center">აირჩიეთ ფაილი</button>
						</div>
					</td>
				</tr>
			</table>
        </fieldset>
		<input type="hidden" id="pers_id" value="' . $res['id'] . '" />
		<input type="hidden" id="is_user" value="' . false . '" />
    </div>
    ';
	return $data;
}

?>