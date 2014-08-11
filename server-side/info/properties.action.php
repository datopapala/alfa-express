<?php 
/* ******************************
 *	Company Properties aJax actions
 * ******************************
 */

include('../../includes/classes/core.php');
$action	= $_REQUEST['act'];
$error	= '';
$data	= '';

switch ($action) {
	case 'set_data':
		$name		= $_REQUEST['name'];
		$address	= $_REQUEST['address'];
		$payer		= $_REQUEST['payer']; 
		
		SetData($name, $address, $payer);
	
		break;
	case 'get_data':	
	
		$data = GetData();
	
		break;
	default:
		$error = "Action is Null";
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Company Properties Functions
 * ******************************
 */

function SetData($name, $address, $payer){
	mysql_query("UPDATE `company_properties`
				 SET    `name`	  = '$name',
				        `address` = '$address',
				        `payer`   = $payer
				 WHERE  `id`      = 1
				");
}

function GetData(){
	$array = '';
	$req = mysql_query("SELECT 	`id` 				AS id,
							   	`tin`				AS tin,
							   	`name`				AS name,
							   	`address`           AS address,
								`tin`				AS user_name,
								`password`			AS user_password,
								`ip`       			AS ip,
								`su_name`			AS su_name,
								`su`				AS su,
								`sp`				AS sp, 
							   	`payer`				AS payer
						FROM   	`company_properties`
			
						");
	
	while( $res = mysql_fetch_assoc($req)){		
		$array = array(
				"tin"       		=> $res['tin'],
				"name"      		=> $res['name'],
				"address"   		=> $res['address'],
				"user_name"  		=> $res['user_name'],
				"user_password"   	=> $res['user_password'],
				"ip"   				=> $res['ip'],
				"su_name"  			=> $res['su_name'],
				"su"   				=> $res['su'],
				"sp"  				=> $res['sp'],
				"payer"     		=> $res['payer']				
		);		
	}
	
	return $array;
}

?>