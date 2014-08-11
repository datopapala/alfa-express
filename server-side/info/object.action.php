<?php
/* ******************************
	Objrct aJax actions
   ******************************
*/
	include('../../includes/classes/core.php');
	$action 	= $_REQUEST['act'];
	$user_id	= $_SESSION['USERID'];
	$error		= '';
	$output 	= '';
	
	switch ($action) {
		case 'get_add_page':
			$page		= GetPage();
			$output 		= array('page'	=> $page);		
			break;
		case 'get_edit_page':
			$object_id		= $_REQUEST['id'];
			$page		= GetPage(GetObb($object_id));		
			$output 		= array('page'	=> $page);		
			break;
	    case 'get_list':
    	    $count = $_REQUEST['count'];
    	    $hidden	= $_REQUEST['hidden'];
    	    $rResult = mysql_query("SELECT	`obj1`.`id`,
    	    								`obj1`.`name`,
    	    								`object_type`.`object_name`,
    	    								`obj2`.`name`,
    	    								`obj1`.`address`
    	    						FROM	`object` as `obj1` LEFT JOIN `object_type`
									ON		`obj1`.`type` = `object_type`.`ID`
    	    								LEFT JOIN `object` as `obj2`
    	    		        		ON		`obj2`.`ID` = `obj1`.`parent`   	    						 
									WHERE	`obj1`.`actived` = 1");   			                            
			
			$output = array(
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
				$output['aaData'][] = $row;
			}

	        break;
	        case 'save_object':
	        	
	        	//
				$object_id		= $_REQUEST['id'];
				$name 		    = $_REQUEST['na'];
	        	$type 			= $_REQUEST['t'];
	        	$parent		    = $_REQUEST['p'];
	        	$address        = $_REQUEST['a'];
	        	$warehouse		= $_REQUEST['w'];
	        	
	        	
	        	if($object_id ==''){
	        			AddObject($user_id, $name, $type, $parent, $address,$warehouse);	        			
	        	}else{
	        		SaveObject($user_id, $object_id, $name, $type, $parent, $address,$warehouse);	        		
	        	}

	        	break;
	        case 'disable':   
	        	$object_id = $_REQUEST['id'];
	        	Disable($object_id);
	        	break;
	    default:
	       	$error = 'Action is Null';
	}
	
	
	$output['error'] = $error;	
	echo json_encode($output);
	
	function GetObb($object_id)
	{
		$res = mysql_fetch_assoc(mysql_query("	SELECT	`object`.`ID`,
														`object`.`name`,
														`object`.`address`,
														`object`.`warehouse`
												FROM	`object`
												WHERE   `object`.`actived` = 1 && `id`=$object_id"));
		return $res;
	}
	
	
	function AddObject($user_id, $name, $type, $parent, $address, $warehouse)
	{

		if($warehouse =='' || $warehouse == 0 ){
			mysql_query("INSERT INTO `object`
			(`user_id`, `name`, `type`, `parent`, `address`,warehouse)
			VALUES
			($user_id, '$name', $type, $parent, '$address',NULL)");			
		}else{
			mysql_query("INSERT INTO `object`
			(`user_id`, `name`, `type`, `parent`, `address`,warehouse)
			VALUES
			($user_id, '$name', $type, $parent, '$address',$warehouse)");			
		}		
		
	}
	
	function SaveObject($user_id, $object_id, $name, $type, $parent, $address, $warehouse)
	{
		if($warehouse =='' || $warehouse == '0' ){
		mysql_query("UPDATE
	    				`object`
				 	SET
				 		`user_id`	= $user_id,
				    	`name`		= '$name',
				    	`type`		= $type,
				    	`parent`	= $parent,
				    	`address`	= '$address',
				    	`warehouse`	= NULL
				 	WHERE
						`id`		= $object_id");
		}else{
			mysql_query("UPDATE
			`object`
			SET
			`user_id`	= $user_id,
			`name`		= '$name',
			`type`		= $type,
			`parent`	= $parent,
			`address`	= '$address',
			`warehouse`	= $warehouse
			WHERE
			`id`		= $object_id");
						
		}
	}
	
	
	function Disable($object_id)
	{
		mysql_query("UPDATE `object`
					 SET    `actived` = 0
					 WHERE 	`id` = $object_id");
	}
	
	function GetobType($object_id){

		$req = mysql_query("SELECT  `object`.`type`
									FROM   `object`
									WHERE   `id`=".$object_id);
		if( $req  ){
			return mysql_result($req ,0,'type');
		}
		return '';
	}
	
	function GetobjectType($object_id){
		$data = '';
		$req = mysql_query("SELECT	 `id`,
       								`object_name`
									FROM   `object_type`");
		
		if($object_id == ''){
			$data = '<option value="0" selected="selected"></option>';
		}
		
		while( $res = mysql_fetch_assoc( $req )){
			if($res['id'] == GetobType($object_id)){
				$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['object_name'] . '</option>';
			} else {
				$data .= '<option value="' . $res['id'] . '">' . $res['object_name'] . '</option>';
			}
		}		
		return $data;		
	}
	
	function getparentID($object_id){
		$req = mysql_query("SELECT  `object`.`parent`
								FROM   `object`
								WHERE   `id`=".$object_id);
		if( $req  ){
			return mysql_result($req ,0,'parent');
		}
		return '';
	}
	
	function Getobjectparent($object_id){
		$data = '';
		$req = mysql_query("SELECT  `id`,
									`name`
								FROM   `object`
								WHERE   (`type` = 1 || `type` = 2) && `object`.`actived` = 1 ");
	
		if($object_id == ''){
			$data = '<option value="0" selected="selected"></option>';
		}
	
		while( $res = mysql_fetch_assoc( $req )){
			if( $res['id'] == getparentID($object_id) ){
				$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
			} else {
				$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
			}
		}	
		return $data;	
	}
	
	function Getwarehouse($warehouse_id){
		$data = '';
		$req = mysql_query("SELECT  `id`,
									`name`
								FROM   `object`
								WHERE   `type` = 2 && `object`.`actived` = 1");
		$data = '<option value="0" selected="selected"></option>';
		
		while( $res = mysql_fetch_assoc( $req )){
			if( $res['id'] == $warehouse_id){
				$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
			}else{
				$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
			}
		}
		return $data;	
	}	
	
	function GetPage($res = '')
	{
		$data = '
			<div id="dialog-form">
			    <fieldset>
			    	<legend>ძირითადი ინფორმაცია</legend>
			
			    	<table class="dialog-form-table">
						<tr>
							<td style="width: 170px;"><label for="object_name">სახელი</label></td>
							<td>
								<div class="seoy-row" id="barcode_client_seoy">
									<input type="text" id="object_name" class="idle seoy-large" onblur="this.className=\'idle seoy-large\'" onfocus="this.className=\'activeField seoy-large\'" value="' . $res['name'] . '" />
								</div>
							</td>
						</tr>
						<tr>
							<td style="width: 170px;"><label for="type">ტიპი</label></td>
							<td>
								<select id="type" class="idls">' . GetobjectType($res['ID']) . '</select>
							</td>
						</tr>
						<tr>
							<td style="width: 170px;"><label for="parent">მშობელი</label></td>
							<td>
								<select id="parent" class="idls">' . Getobjectparent($res['ID']) . '</select>
							</td>
						</tr>								
						<tr>
							<td style="width: 170px;"><label for="address">მისამართი</label></td>
							<td>
								<div class="seoy-row" id="barcode_cartridge_seoy">
									<input type="text" id="address" class="idle seoy-large" onblur="this.className=\'idle seoy-large\'" onfocus="this.className=\'activeField seoy-large\'" value="' . $res['address'] . '" />
								</div>
							</td>
						</tr>
						<tr  id="warehouseID" class="warehou">
							<td style="width: 170px;"><label for="warehouse">საწყობი</label></td>
							<td>
								<select id="warehouse" class="idls">' . Getwarehouse($res['warehouse']) . '</select>
							</td>
						</tr>														
					</table>
					<!-- ID -->
					<input type="hidden" id="object_id" value="'.$res['ID']. '" />
		        </fieldset>
		    </div>
	    ';
		return $data;
	}
	
?>