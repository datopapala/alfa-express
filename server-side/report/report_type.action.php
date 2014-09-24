<?php
include('../../includes/classes/core.php');
$start  	= $_REQUEST['start'];
$end    	= $_REQUEST['end'];
$count 		= $_REQUEST["count"];
$action 	= $_REQUEST['act'];
$departament= $_REQUEST['departament'];
$type       = $_REQUEST['type'];
$category   = $_REQUEST['category'];
$s_category = $_REQUEST['sub_category'];
$done 		= $_REQUEST['done']%4;
$name 		= $_REQUEST['name'];
$title 		= $_REQUEST['title'];
$text[0] 	= "ზარის შეფასება";
$text[1] 	= "'$type' ტიპის ზარები  კატეგორიის მიხედვით  მიხედვით";
$text[2] 	= "'$type - $category' ტიპის ზარები  ქვე–კატეგორიის მიხედვით";
$text[3] 	= "'$type - $category - $s_category' შემოსული ზარები ქვე–კატეგორიის მიხედვით";
$c="3 or incomming_call.call_vote=0";
if ($type=="პოზიტიური")  $c=1;
elseif ($type=="ნეგატიური") $c=2;
//------------------------------------------------query-------------------------------------------
switch ($done){
	case  1:
		$result = mysql_query("SELECT  info_category.`name` AS d_name,
								COUNT(incomming_call.id),
								CONCAT(ROUND(COUNT(incomming_call.id)/(SELECT COUNT(incomming_call.id) FROM info_category
																JOIN incomming_call ON incomming_call.information_category_id=info_category.id AND DATE(`incomming_call`.`date`) >= '$start' AND DATE(`incomming_call`.`date`) <= '$end' and (incomming_call.call_vote=$c)
																WHERE 	info_category.parent_id=0 AND info_category.actived=1)*100,2),'%')
								FROM 		info_category
								JOIN incomming_call ON incomming_call.information_category_id=info_category.id AND DATE(`incomming_call`.`date`) >= '$start' AND DATE(`incomming_call`.`date`) <= '$end' and (incomming_call.call_vote=$c)
								WHERE 	info_category.parent_id=0 AND info_category.actived=1
								GROUP BY 	d_name");
		$text[0]=$text[1];
	break;
	 case 2:
			$result = mysql_query("SELECT info_category.`name` AS d_name,
								COUNT(incomming_call.id),
								CONCAT(ROUND(COUNT(incomming_call.id)/(SELECT 		COUNT(incomming_call.id)
																	FROM 			info_category
																	JOIN 			info_category AS par ON par.id=info_category.parent_id AND par.`name`='$category'
																	JOIN incomming_call  ON incomming_call.information_sub_category_id=info_category.id
																	AND DATE(`incomming_call`.`date`) >= '$start' AND DATE(`incomming_call`.`date`) and (incomming_call.call_vote=$c) <= '$end')*100,2),'%')
								FROM info_category
								JOIN info_category AS par ON par.id=info_category.parent_id AND par.`name`='$category'
								JOIN incomming_call  ON incomming_call.information_sub_category_id=info_category.id
								AND DATE(`incomming_call`.`date`) >= '$start' AND DATE(`incomming_call`.`date`) <= '$end' and (incomming_call.call_vote=$c)
								GROUP BY d_name");
		$text[0]=$text[2];
	break;
		case 3:
	$result = mysql_query("SELECT results.`name` AS d_name,
							COUNT(incomming_call.id),
							CONCAT(ROUND(COUNT(incomming_call.id)/(
																SELECT 		COUNT(incomming_call.id)
																FROM info_category
																JOIN incomming_call  ON incomming_call.information_sub_category_id=info_category.id
																JOIN results  ON incomming_call.results_id=results.id
																where info_category.`name`='$s_category' AND DATE(`incomming_call`.`date`) >= '$start' AND DATE(`incomming_call`.`date`) <= '$end' and (incomming_call.call_vote=$c)
							)*100,2),'%')
							FROM info_category
							JOIN incomming_call  ON incomming_call.information_sub_category_id=info_category.id
							JOIN results  ON incomming_call.results_id=results.id
							where info_category.`name`='$s_category' AND DATE(`incomming_call`.`date`) >= '$start' AND DATE(`incomming_call`.`date`) <= '$end' and (incomming_call.call_vote=$c)
							GROUP BY d_name");
		$text[0]=$text[3];
		break;
	default:
	$result = mysql_query(" SELECT IF(call_vote=1,'პოზიტიური', IF(call_vote=2,'ნეგატიური','ნეიტრალური'))AS vote,
							COUNT(*),
							CONCAT(ROUND(COUNT(*)/(
							SELECT COUNT(*) FROM incomming_call where DATE(`incomming_call`.`date`) >= '$start' AND DATE(`incomming_call`.`date`) <= '$end'
							)*100,2),'%')
							FROM incomming_call
							where DATE(`incomming_call`.`date`) >= '$start' AND DATE(`incomming_call`.`date`) <= '$end'
							GROUP BY vote");

		break;
}
///----------------------------------------------act------------------------------------------
switch ($action) {
	case "get_list":
		$data = array("aaData"	=> array());
		while ( $aRow = mysql_fetch_array( $result ) )
		{	$row = array();
			for ( $i = 0 ; $i < $count ; $i++ )
			{
				$row[0] = '0';

				$row[$i+1] = $aRow[$i];
			}
			$data['aaData'][] =$row;
		}
		echo json_encode($data); return 0;
		break;
	case 'get_category' :
		$rows = array();
		while($r = mysql_fetch_array($result)) {
			$row[0] = $r[0];
			$row[1] = (float) $r[1];
			$rows['data'][]=$row;
		}
		$rows['text']=$text[0];
		echo json_encode($rows);
		break;
	default :
		echo "Action Is Null!";
		break;

}



?>
