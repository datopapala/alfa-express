<?php
/* ******************************
 *	Client Object List aJax actions
 * ******************************
 */

include('../../../includes/classes/core.php');

$action		= $_REQUEST['act'];
$error		= '';
$data		= '';
$user_id	= $_SESSION['USERID'];

switch ($action) {
    case 'get_add_page':
		$page		= GetPage();
		$data		= array('page'	=> $page);
		
        break;
    case 'get_edit_page':
	    $id	= $_REQUEST['id'];
		$page		= GetPage( $id );
		 
		$data		= array('page'	=> $page);

        break;
    case 'get_list':
	    $count		= $_REQUEST['count'];
	    $hidden		= $_REQUEST['hidden'];
	    $local_id	= $_REQUEST['id'];
	    $date		= $_REQUEST['date'];
	    
	    $rResult = mysql_query("SELECT		`services_degree`.`id`,
											`client_objects`.`name`,
											`client_object_persons`.`name`,
											`client_object_persons`.`phone_number`,
											`services_degree`.`comment`,
											`services_degree`.`degree_type`
								FROM		`client_objects` 
								LEFT JOIN	`client` ON	`client_objects`.`client_id` = `client`.`id`
								LEFT JOIN	`client_object_persons` ON `client_object_persons`.`client_object_id` = `client_objects`.`id` AND `client_object_persons`.`actived` = 1
								LEFT JOIN	`services_degree` ON `services_degree`.`client_object` = `client_objects`.`id`
								WHERE		`client`.`id` = '$local_id' && `client_objects`.`actived` = 1 && DATE(`services_degree`.`call_date`) = '$date'
								GROUP BY	`client_objects`.`id`");
		
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
					switch($row[$i]){
						case '1': $row[$i] = '<div style="background-color: green; width: 100%; height: 100%;"></div>'; break;
						case '2': $row[$i] = '<div style="background-color: yellow; width: 100%; height: 100%;"></div>'; break;
						case '3': $row[$i] = '<div style="background-color: red; width: 100%; height: 100%;"></div>'; break;
					}
				}
			}
			$data['aaData'][] = $row;
		}
		
        break;
    case 'save_client_call':
    	$service_degree_id	= $_REQUEST['id'];
    	$client_object_name	= $_REQUEST['cobn'];
    	$person_name		= $_REQUEST['p'];
    	$service_degree_t	= $_REQUEST['d'];
    	$call_date			= $_REQUEST['dat'];
    	$comment			= $_REQUEST['c'];
    	
    	SaveCall($service_degree_id, $user_id, $client_object_name, $person_name, $service_degree_t, $call_date, $comment);
    	
    	break;
	case 'getperson':
		$pers_name = $_REQUEST['n'];
		$array = GetPerson($pers_name);
		$data		= array(
			'phone'	=> $array[0],
			'email' => $array[1]
		);
        	 
		break;
    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Client Object List Functions
 * ******************************
 */

function SaveCall($service_degree_id, $user_id, $client_object_name, $person_name, $service_degree_t, $call_date, $comment){
	GLOBAL $error;
	$result = mysql_query("INSERT IGNORE INTO `services_degree` (`id`) VALUES ( $service_degree_id )");
	
	if(!$result){
		$error = 'Invalid query: ' . mysql_error();
	}else{
		$resP	= mysql_fetch_assoc( mysql_query("	SELECT	`id`
													FROM	`client_object_persons`
													WHERE	`client_object_persons`.`name` = '$person_name'"));
		
		$resOBj	= mysql_fetch_assoc( mysql_query("	SELECT		`client_objects`.`id`
													FROM 		`client_objects`
													LEFT JOIN 	`client` ON `client`.`id` = `client_objects`.`client_id`
													WHERE		CONCAT(`client`.`name`,'(',`client_objects`.`name`, ')') = '$client_object_name'"));
		if($resP == ''){
			//$error = 'საკონტაქტო პირი არასწორია!'; return ;
		}else if($resOBj == ''){
			$error = 'კლიენტი არასწორია!'; return ;
		}
		
		mysql_query("	UPDATE	`services_degree`
						SET	
								`user_id`				= $user_id,		
								`client_object`			= '$resOBj[id]',
								`client_object_persons`	= $person_name,
								`call_date`				= '$call_date',
								`degree_type`			= '$service_degree_t',
								`comment`				= '$comment'
						WHERE	`id`					= '$service_degree_id'");
	}
}

function GetPerson($person_name){
	$res = mysql_fetch_assoc( mysql_query("	SELECT	phone_number,
			mail
			FROM	client_object_persons
			WHERE	client_object_persons.`name` = '$person_name'"));

	$arr = array(
			"0"				=> $res['phone_number'],
			"1"				=> $res['mail']
	);
	return $arr;
}

function GetPage( $id )
{
	$res = mysql_fetch_assoc( mysql_query( "SELECT	`client_objects`.`name` AS `name`
											FROM	`client_objects`
											WHERE	`client_objects`.`id` = '$id'"));
	$data = '
	<div id="dialog-form">
		<fieldset>	
			<legend>ძირითადი ინფორმაცია</legend>	
				<table width="80%" class="dialog-form-table" cellpadding="10px" >								
					<tr align="center">
						<th colspan="2">
							<label for="client_object">კლიენტის ობიექტი:<span style="border-bottom: 1px solid #000; padding: 0 30px; margin-left: 50px;">'.$res['name'].'</span></label>
						</th>						
					</tr>
				</table>			
		</fieldset>				
		<fieldset>	
			<legend>ზარები</legend>	
		    <div class="inner-table">
			    <div id="dt_example" class="ex_highlight_row">
			        <div id="container" class="overhead_container">
			        	<div id="button_area">
							<button id="add_button_call">დამატება</button><button id="delete_button_call">წაშლა</button>
						</div>
			            <div id="dynamic">
			                <table class="display" id="calls_list">
			                    <thead>
			                        <tr id="datatable_header">				                        
			                            <th>ID</th>
										<th style="width: 200px">საკონტაქტო პირი</th>									
			                            <th style="width: 100%">შინაარსი</th>	
										<th style="width: 200px">თარიღი</th>									
										<th class="min">კმაყოფილების<br>მაღვენებელი</th>
			                        </tr>
			                    </thead>
			                    <thead>
			                        <tr class="search_header">				                        
			                            <th class="colum_hidden">
			                            	<input type="text" name="search_id" value="ფილტრი" class="search_init" />
			                            </th>
			                            <th>
			                            	<input type="text" name="search_contact_person" value="ფილტრი" class="search_init" />
			                            </th>									

			                            <th>
			                            	<input type="text" name="search_comment" value="ფილტრი" class="search_init" />
			                            </th>
			                            <th>
			                            	<input type="text" name="search_date" value="ფილტრი" class="search_init" />
			                            </th>									
			                            <th>
			                            	<input type="text" name="search_degree" value="ფილტრი" class="search_init" />
			                            </th>			
			                        </tr>
			                    </thead>
			                </table>
			            </div>
			        </div>
			    </div>
			</div>
		</fieldset>			        		
		<!-- ID -->
		<input type="hidden" id="client_object_id" value="' . $id . '" />
    </div>    
    ';
	return $data;
}

?>