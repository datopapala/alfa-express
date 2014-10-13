<?php
require_once('../../includes/classes/core.php');
header('Content-Type: application/json');
$start = $_REQUEST['start'];
$end   = $_REQUEST['end'];
$agent = $_REQUEST['agent'];
$queuet = $_REQUEST['queuet'];


$result = mysql_query("SELECT	COUNT(*) AS `count1`,
								CONCAT('ნაპასუხები :',' ',COUNT(*)) AS `cause` 
								FROM	queue_stats AS qs,
								qname AS q,
								qagent AS ag,
								qevent AS ac
								WHERE qs.qname = q.qname_id
								AND qs.qagent = ag.agent_id
								AND qs.qevent = ac.event_id
								AND DATE(qs.datetime) >= '$start' AND DATE(qs.datetime) <= '$end'
								AND q.queue IN ($queuet)
								AND ac.event IN ( 'COMPLETECALLER', 'COMPLETEAGENT')");

$row_done_blank = mysql_query(" SELECT 	COUNT(*) AS `count`,
		CONCAT('დამუშავებული:',' ',COUNT(*)) AS `cause1`
		FROM `incomming_call`
		WHERE DATE(date) >= '$start' AND DATE(date) <= '$end' AND phone != '' ");
					
$row = array();
$row1 = array();
$rows = array();
while($r = mysql_fetch_array($result)) {
	$r1 = mysql_fetch_array($row_done_blank);
	$row[0] = 'დაუმუშავებელი: '.($r[0] - $r1[0]);
	$row[1] = (float)($r[0] - $r1[0]);
	$row1[0] = $r1[1];
	$row1[1] = (float)$r[0];
	array_push($rows,$row);
	array_push($rows,$row1);
}

echo json_encode($rows);

?>