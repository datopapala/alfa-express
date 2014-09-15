<?php

mysql_connect('212.72.155.175', 'root', 'Gl-1114');
mysql_select_db('asteriskcdrdb');

//require_once '../../includes/classes/asteriskcore.php';

$res = mysql_query("SELECT queue_stats.info1
					FROM   queue_stats
					WHERE  DATE(queue_stats.datetime) = CURDATE() AND queue_stats.qevent = 10
					"); 
$w15 = 0;
$w30 = 0;
$w45 = 0;
$w60 = 0;
$w75 = 0;
$w90 = 0;
$w91 = 0;




while ($row = mysql_fetch_assoc($res)) {
	
	if ($row['info1'] < 15) {
		$w15++;
	}
	
 	if ($row['info1'] < 30){
 		$w30++;
 	}
 	
	if ($row['info1'] < 45){
 		$w45++;
 	}
 	
	if ($row['info1'] < 60){
		$w60++;
	}
	
	if ($row['info1'] < 75){
		$w75++;
	}
	
	if ($row['info1'] < 90){
		$w90++;
	}
	
	$w91++;
	
}

$d30 = $w30 - $w15;
$d45 = $w45 - $w30;
$d60 = $w60 - $w45;
$d75 = $w75 - $w60;
$d90 = $w90 - $w75;
$d91 = $w91 - $w90;


$p15 = round($w15 * 100 / $w91);
$p30 = round($w30 * 100 / $w91);
$p45 = round($w45 * 100 / $w91);
$p60 = round($w60 * 100 / $w91);
$p75 = round($w75 * 100 / $w91);
$p90 = round($w90 * 100 / $w91);

 $data = '
 		<table id="box-table-b">
 			<thead>
		 		<tr>
			 		<th style="width: 80px;">პასუხი</th>
		 			<th style="width: 80px;">რაოდ.</th>
		 			<th style="width: 80px;">დელტა</th>
		 			<th>%</th>
		 		</tr>
 			<thead>
 			<tbody>
	 			<tr class="odd">
			 		<td>15 წამში</td>
		 			<td>'.$w15.'</td>
		 			<td></td>
		 			<td>'.$p15.'%</td>
		 		</tr>
	 			<tr>
			 		<td>30 წამში</td>
		 			<td>'.$w30.'</td>
		 			<td>'.$d30.'</td>
		 			<td>'.$p30.'%</td>
		 		</tr>
	 			<tr class="odd">
			 		<td>45 წამში</td>
		 			<td>'.$w45.'</td>
		 			<td>'.$d45.'</td>
		 			<td>'.$p45.'%</td>
		 		</tr>
	 			<tr>
			 		<td>60 წამში</td>
		 			<td>'.$w60.'</td>
		 			<td>'.$d60.'</td>
		 			<td>'.$p60.'%</td>
		 		</tr>
	 			<tr class="odd">
			 		<td>75 წამში</td>
		 			<td>'.$w75.'</td>
		 			<td>'.$d75.'</td>
		 			<td>'.$p75.'%</td>
		 		</tr>
		 		<tr>
			 		<td>90 წამში</td>
		 			<td>'.$w90.'</td>
		 			<td>'.$d90.'</td>
		 			<td>'.$p90.'%</td>
		 		</tr>
		 		<tr class="odd">
			 		<td>90+ წამში</td>
		 			<td>'.$w91.'</td>
		 			<td>'.$d91.'</td>
		 			<td>100%</td>
		 		</tr>
	 		<tbody>
 		</table>
 		';
 
 echo  $data;