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
		$product_id		= $_REQUEST['id'];
	       $page		= GetPage(Getproduct($product_id));
           $data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
		 
		$rResult = mysql_query("SELECT 	product.id,
										product.`name`
							    FROM 	product
							    WHERE 	product.actived=1");

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
	case 'save_product':
		$product_id 		= $_REQUEST['id'];
		$product_name    = $_REQUEST['name'];
		
	
		
		if($product_name != ''){
			if(!CheckproductExist($product_name, $product_id)){
				if ($product_id == '') {
					Adddeproduct( $product_id, $product_name);
				}else {
					Savedeproduct($product_id, $product_name);
				}
								
			} else {
				$error = '"' . $product_name . '" უკვე არის სიაში!';
				
			}
		}
		
		break;
	case 'disable':
		$product_id	= $_REQUEST['id'];
		Disableproduct($product_id);

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

function Adddeproduct($product_id, $product_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO 	 `product`
								(`name`,`user_id`)
					VALUES 		('$product_name', '$user_id')");
}

function Savedeproduct($product_id, $product_name)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("	UPDATE `product`
					SET     `name` = '$product_name',
							`user_id` ='$user_id'
					WHERE	`id` = $product_id");
}

function Disableproduct($product_id)
{
	mysql_query("	UPDATE `product`
					SET    `actived` = 0
					WHERE  `id` = $product_id");
}

function CheckproductExist($product_name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
											FROM   `product`
											WHERE  `name` = '$product_name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}


function Getproduct($product_id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT  `id`,
													`name`
											FROM    `product`
											WHERE   `id` = $product_id" ));

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
			<input type="hidden" id="product_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
