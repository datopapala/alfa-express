<?php
mysql_close();
$conn = mysql_connect('212.72.155.175', 'root', 'Gl-1114');
mysql_select_db('asteriskcdrdb');

header('Content-Type: application/json');
$start = $_REQUEST['start'];
$end   = $_REQUEST['end'];
$agent = $_REQUEST['agent'];
$queuet = $_REQUEST['queuet'];

$quantity = array();
$cause = array();
$cause1 = array();

$name = array();
$agent = array();

$call_count = array();
$answer_count = array();

$datetime[] = array();
$answer_count1 = array();

$datetime1[] = array();
$agent = array();

$name[]     = 'რაოდენობა';


//-----------------------კავშირის გაწყვეტის მიზეზი
				$res = mysql_query("		SELECT	COUNT(*) AS `count`,
																	'ოპერატორმა გათიშა' AS `cause`
												FROM	queue_stats AS qs,
														qname AS q,
														qagent AS ag,
														qevent AS ac
												WHERE qs.qname = q.qname_id
												AND qs.qagent = ag.agent_id
												AND qs.qevent = ac.event_id
												AND DATE(qs.datetime) >= '$start' AND DATE(qs.datetime) <= '$end'
												AND ac.event IN ( 'COMPLETEAGENT')
												

													UNION ALL

												SELECT	COUNT(*) AS `count`,
																'აბონენტმა გათიშა' AS `cause`
														FROM	queue_stats AS qs,
																qname AS q,
																qagent AS ag,
																qevent AS ac
														WHERE qs.qname = q.qname_id
														AND qs.qagent = ag.agent_id
														AND qs.qevent = ac.event_id
														AND DATE(qs.datetime) >= '$start' AND DATE(qs.datetime) <= '$end'
														AND ac.event IN (  'COMPLETECALLER')
														");

while($row = mysql_fetch_assoc($res)){	
	
	$quantity[] = (float)$row[count];
	$cause[]		= $row[cause];
}

//-----------------------ნაპასუხები ზარები ოპერატორების მიხედვით

											$ress =mysql_query("SELECT 	ag.agent as `agent`,
																	count(ev.event) AS `num`
																FROM queue_stats AS qs, qname AS q, qevent AS ev, qagent AS `ag` WHERE ag.agent_id = qs.qagent AND
																qs.qname = q.qname_id and qs.qevent = ev.event_id
																AND DATE(qs.datetime) >= '$start'
																AND DATE(qs.datetime) <= '$end'
																AND ev.event IN ('COMPLETECALLER', 'COMPLETEAGENT')
																GROUP BY ag.agent");
			
while($row1 = mysql_fetch_assoc($ress)){

	$call_count[] = (float)$row1[num];
	$agent[]		= $row1[agent];
}
//------------------------------ ნაპასუხები ზარები კვირის დღეების მიხედვით
		$res3 =mysql_query("SELECT  CASE
										WHEN DAYOFWEEK(qs.datetime) = 1 THEN 'კვირა'
										WHEN DAYOFWEEK(qs.datetime) = 2 THEN 'ორშაბათი'
										WHEN DAYOFWEEK(qs.datetime) = 3 THEN 'სამშაბათი'
										WHEN DAYOFWEEK(qs.datetime) = 4 THEN 'ოთხშაბათი'
										WHEN DAYOFWEEK(qs.datetime) = 5 THEN 'ხუთშაბათი'
										WHEN DAYOFWEEK(qs.datetime) = 6 THEN 'პარასკევი'
										WHEN DAYOFWEEK(qs.datetime) = 7 THEN 'შაბათი'
									END AS `date`,
									COUNT(*) AS `answer_count1`
							FROM 	queue_stats AS qs,
									qname AS q, 
									qagent AS ag,
									qevent AS ac 
							WHERE qs.qname = q.qname_id
							AND qs.qagent = ag.agent_id 
							AND qs.qevent = ac.event_id
							AND DATE(qs.datetime) >= '$start'
							AND DATE(qs.datetime) <= '$end'
							AND q.queue IN ($queuet,'NONE')
							AND ac.event IN ('COMPLETECALLER','COMPLETEAGENT')
							GROUP BY DAYOFWEEK(qs.datetime)");
									
		while($row3 = mysql_fetch_assoc($res3)){

		$answer_count1[] = (float)$row3[answer_count1];
		$datetime1[]		= $row3[date];
	}
	
	
	//------------------------------ უპასუხო ზარები კვირის დღეების მიხედვით
		
	$res10 =mysql_query("SELECT CASE
				
									WHEN DAYOFWEEK(qs.datetime) = 1 THEN 'კვირა'
									WHEN DAYOFWEEK(qs.datetime) = 2 THEN 'ორშაბათი'
									WHEN DAYOFWEEK(qs.datetime) = 3 THEN 'სამშაბათი'
									WHEN DAYOFWEEK(qs.datetime) = 4 THEN 'ოთხშაბათი'
									WHEN DAYOFWEEK(qs.datetime) = 5 THEN 'ხუთშაბათი'
									WHEN DAYOFWEEK(qs.datetime) = 6 THEN 'პარასკევი'
									WHEN DAYOFWEEK(qs.datetime) = 7 THEN 'შაბათი'
									END AS `date`,
									COUNT(*) AS `unanswer_count`
									FROM 	queue_stats AS qs,
									qname AS q,
									qagent AS ag,
									qevent AS ac
									WHERE qs.qname = q.qname_id
									AND qs.qagent = ag.agent_id
									AND qs.qevent = ac.event_id
									AND DATE(qs.datetime) >= '$start'
									AND DATE(qs.datetime) <= '$end'
									AND q.queue IN ($queuet,'NONE')
									AND ac.event IN ('ABANDON','EXITWITHTIMEOUT')
									GROUP BY DAYOFWEEK(qs.datetime)
									");
	
	while($row10 = mysql_fetch_assoc($res10)){
	
		$unanswer_call2[] = (float)$row10[unanswer_count];
		$date1[]		= $row10[date];
	}		
	//------------------------------ კავშირის გაწყვეტის მიზეზი

$res5 =mysql_query("SELECT 	COUNT(*) AS `answer_count3`,
							'ოპერატორმა გათიშა' AS `cause1`
					FROM 	queue_stats AS qs,
							qname AS q,
							qagent AS ag,
							qevent AS ac
					WHERE qs.qname = q.qname_id
					AND qs.qagent = ag.agent_id
					AND qs.qevent = ac.event_id
					AND DATE(qs.datetime) >= '$start' AND DATE(qs.datetime) <= '$end'
					AND q.queue IN ($queuet)
					AND ac.event IN ('EXITWITHTIMEOUT')
					
			UNION ALL
					
					
					SELECT 	COUNT(*) AS `answer_count3`,
					'აბონენტმა გათიშა' AS `cause1`
					FROM	queue_stats AS qs,
					qname AS q,
					qagent AS ag,
					qevent AS ac
					WHERE qs.qname = q.qname_id
					AND qs.qagent = ag.agent_id
					AND qs.qevent = ac.event_id
					AND DATE(qs.datetime) >= '$start'
					AND DATE(qs.datetime) <= '$end'
					AND q.queue IN ($queuet)
					AND ac.event IN ('ABANDON', 'EXITWITHTIMEOUT')");
					
		while($row5 = mysql_fetch_assoc($res5)){
			
		$answer_count3[] = (float)$row5[answer_count3];
		$cause1[]		= $row5[cause1];
}
//------------------------------ კავშირის გაწყვეტის მიზეზი
	$res6 =mysql_query("SELECT 	COUNT(*) AS `count1`,
								q.queue as `queue1`
								FROM 	queue_stats AS qs,
								qname AS q,
								qagent AS ag,
								qevent AS ac
								WHERE qs.qname = q.qname_id
								AND qs.qagent = ag.agent_id
								AND qs.qevent = ac.event_id
								AND DATE(qs.datetime) >= '$start' AND DATE(qs.datetime) <= '$end'
								AND q.queue IN ($queuet)
								AND ac.event IN ('ABANDON')
								ORDER BY qs.datetime");

		while($row6 = mysql_fetch_assoc($res6)){
		
		$count1[] = (float)$row6[count1];
		$queue1[]		= $row6[queue1];
		}
//------------------------------ ნაპასუხები ზარები რიგის მიხედვით
		$res7 =mysql_query("SELECT	COUNT(*) AS `count2`,
									q.queue AS `queue2`
							FROM	queue_stats AS qs,
									qname AS q,
									qagent AS ag,
									qevent AS ac
							WHERE qs.qname = q.qname_id
							AND qs.qagent = ag.agent_id
							AND qs.qevent = ac.event_id
							AND DATE(qs.datetime) >= '$start' AND DATE(qs.datetime) <= '$end'
							AND q.queue IN ($queuet)
							AND ac.event IN ( 'COMPLETECALLER', 'COMPLETEAGENT')
							ORDER BY ag.agent");

		while($row7 = mysql_fetch_assoc($res7)){

		$count2[] = (float)$row7[count2];
		$queue2[]		= $row7[queue2];
}
			
//------------------------------ უპასუხო ზარები რიგის მიხედვით
$res8 =mysql_query("SELECT 	DATE(qs.datetime) AS `datetime`,
							COUNT(*) AS `unanswer_call`
		
					FROM 	queue_stats AS qs,
							qname AS q,
							qagent AS ag,
							qevent AS ac
					WHERE 	qs.qname = q.qname_id
					AND 	qs.qagent = ag.agent_id
					AND 	qs.qevent = ac.event_id
					AND 	DATE(qs.datetime) >= '$start'
					AND 	DATE(qs.datetime) <= '$end'
					AND 	q.queue IN ($queuet,'NONE')
					AND 	ac.event IN ('ABANDON', 'EXITWITHTIMEOUT')
					GROUP BY DATE(qs.datetime)
					");
		
		while($row8 = mysql_fetch_assoc($res8)){
		
			$unanswer_call[] = (float)$row8[unanswer_call];
			$times[]		= $row8[datetime];
		}
		
		
//------------------------------ ნაპასუხები ზარები დღეების მიხედვით		
			
$res4 =mysql_query("SELECT 	DATE(qs.datetime) AS `datetime`,
						COUNT(*) AS `answer_count2`
						FROM 	queue_stats AS qs,
						qname AS q,
						qagent AS ag,
						qevent AS ac
						WHERE qs.qname = q.qname_id
						AND qs.qagent = ag.agent_id
						AND qs.qevent = ac.event_id
						AND DATE(qs.datetime) >= '$start'
						AND DATE(qs.datetime) <= '$end'
						AND q.queue IN ($queuet,'NONE')
						AND ac.event IN ('COMPLETECALLER','COMPLETEAGENT')
						GROUP BY DATE(qs.datetime)");

		while($row4 = mysql_fetch_assoc($res4)){

		$answer_count2[] = (float)$row4[answer_count2];
		$datetime2[]		= $row4[datetime];
			}
//------------------------------ უპასუხო ზარები საათების მიხედვით			
	$res9 =mysql_query("SELECT  CASE	WHEN HOUR(qs.datetime) >= 0 AND HOUR(qs.datetime) < 1 THEN '00:00'
										WHEN HOUR(qs.datetime) >= 1 AND HOUR(qs.datetime) < 2 THEN '01:00'
										WHEN HOUR(qs.datetime) >= 2 AND HOUR(qs.datetime) < 3 THEN '02:00'
										WHEN HOUR(qs.datetime) >= 3 AND HOUR(qs.datetime) < 4 THEN '03:00'
										WHEN HOUR(qs.datetime) >= 4 AND HOUR(qs.datetime) < 5 THEN '04:00'
										WHEN HOUR(qs.datetime) >= 5 AND HOUR(qs.datetime) < 6 THEN '05:00'
										WHEN HOUR(qs.datetime) >= 6 AND HOUR(qs.datetime) < 7 THEN '06:00'
										WHEN HOUR(qs.datetime) >= 7 AND HOUR(qs.datetime) < 8 THEN '07:00'
										WHEN HOUR(qs.datetime) >= 8 AND HOUR(qs.datetime) < 9 THEN '08:00'
										WHEN HOUR(qs.datetime) >= 9 AND HOUR(qs.datetime) < 10 THEN '10:00'
										WHEN HOUR(qs.datetime) >= 10 AND HOUR(qs.datetime) < 11 THEN '11:00'
										WHEN HOUR(qs.datetime) >= 11 AND HOUR(qs.datetime) < 12 THEN '12:00'
										WHEN HOUR(qs.datetime) >= 12 AND HOUR(qs.datetime) < 13 THEN '13:00'
										WHEN HOUR(qs.datetime) >= 13 AND HOUR(qs.datetime) < 14 THEN '14:00'
										WHEN HOUR(qs.datetime) >= 14 AND HOUR(qs.datetime) < 15 THEN '15:00'
										WHEN HOUR(qs.datetime) >= 15 AND HOUR(qs.datetime) < 16 THEN '16:00'
										WHEN HOUR(qs.datetime) >= 16 AND HOUR(qs.datetime) < 17 THEN '17:00'
										WHEN HOUR(qs.datetime) >= 17 AND HOUR(qs.datetime) < 18 THEN '18:00'
										WHEN HOUR(qs.datetime) >= 18 AND HOUR(qs.datetime) < 19 THEN '19:00'
										WHEN HOUR(qs.datetime) >= 19 AND HOUR(qs.datetime) < 20 THEN '20:00'
										WHEN HOUR(qs.datetime) >= 20 AND HOUR(qs.datetime) < 21 THEN '21:00'
										WHEN HOUR(qs.datetime) >= 21 AND HOUR(qs.datetime) < 22 THEN '22:00'
										WHEN HOUR(qs.datetime) >= 22 AND HOUR(qs.datetime) < 23 THEN '23:00'
										WHEN HOUR(qs.datetime) >= 23 AND HOUR(qs.datetime) < 24 THEN '24:00'

									END AS `times`,
									COUNT(*) AS `unanswer_count`
										
									FROM 	queue_stats AS qs,
									qname AS q,
									qagent AS ag,
									qevent AS ac
									WHERE qs.qname = q.qname_id
									AND qs.qagent = ag.agent_id
									AND qs.qevent = ac.event_id
									AND DATE(qs.datetime) >= '$start'
									AND DATE(qs.datetime) <= '$end'
									AND q.queue IN ($queuet,'NONE')
									AND ac.event IN ('ABANDON','EXITWITHTIMEOUT')
									GROUP BY HOUR(qs.datetime)
");
	
	while($row9 = mysql_fetch_assoc($res9)){
	
		$unanswer_count1[] = (float)$row9[unanswer_count];
		$times2[]		= $row9[times];
	}
	//------------------------------ ნაპასუხები ზარები საათების მიხედვით
	$res2 =mysql_query("SELECT  CASE
										WHEN HOUR(qs.datetime) >= 0 AND HOUR(qs.datetime) < 1 THEN '00:00'
										WHEN HOUR(qs.datetime) >= 1 AND HOUR(qs.datetime) < 2 THEN '01:00'
										WHEN HOUR(qs.datetime) >= 2 AND HOUR(qs.datetime) < 3 THEN '02:00'
										WHEN HOUR(qs.datetime) >= 3 AND HOUR(qs.datetime) < 4 THEN '03:00'
										WHEN HOUR(qs.datetime) >= 4 AND HOUR(qs.datetime) < 5 THEN '04:00'
										WHEN HOUR(qs.datetime) >= 5 AND HOUR(qs.datetime) < 6 THEN '05:00'
										WHEN HOUR(qs.datetime) >= 6 AND HOUR(qs.datetime) < 7 THEN '06:00'
										WHEN HOUR(qs.datetime) >= 7 AND HOUR(qs.datetime) < 8 THEN '07:00'
										WHEN HOUR(qs.datetime) >= 8 AND HOUR(qs.datetime) < 9 THEN '08:00'
										WHEN HOUR(qs.datetime) >= 9 AND HOUR(qs.datetime) < 10 THEN '10:00'
										WHEN HOUR(qs.datetime) >= 10 AND HOUR(qs.datetime) < 11 THEN '11:00'
										WHEN HOUR(qs.datetime) >= 11 AND HOUR(qs.datetime) < 12 THEN '12:00'
										WHEN HOUR(qs.datetime) >= 12 AND HOUR(qs.datetime) < 13 THEN '13:00'
										WHEN HOUR(qs.datetime) >= 13 AND HOUR(qs.datetime) < 14 THEN '14:00'
										WHEN HOUR(qs.datetime) >= 14 AND HOUR(qs.datetime) < 15 THEN '15:00'
										WHEN HOUR(qs.datetime) >= 15 AND HOUR(qs.datetime) < 16 THEN '16:00'
										WHEN HOUR(qs.datetime) >= 16 AND HOUR(qs.datetime) < 17 THEN '17:00'
										WHEN HOUR(qs.datetime) >= 17 AND HOUR(qs.datetime) < 18 THEN '18:00'
										WHEN HOUR(qs.datetime) >= 18 AND HOUR(qs.datetime) < 19 THEN '19:00'
										WHEN HOUR(qs.datetime) >= 19 AND HOUR(qs.datetime) < 20 THEN '20:00'
										WHEN HOUR(qs.datetime) >= 20 AND HOUR(qs.datetime) < 21 THEN '21:00'
										WHEN HOUR(qs.datetime) >= 21 AND HOUR(qs.datetime) < 22 THEN '22:00'
										WHEN HOUR(qs.datetime) >= 22 AND HOUR(qs.datetime) < 23 THEN '23:00'
										WHEN HOUR(qs.datetime) >= 23 AND HOUR(qs.datetime) < 24 THEN '24:00'
								
										END AS `times`,
										COUNT(*) AS `answer_count`
										FROM 	queue_stats AS qs,
										qname AS q,
										qagent AS ag,
										qevent AS ac
										WHERE 	qs.qname = q.qname_id
										AND 	qs.qagent = ag.agent_id
										AND 	qs.qevent = ac.event_id
										AND 	DATE(qs.datetime) >= '$start'
										AND 	DATE(qs.datetime) <= '$end'
										AND 	q.queue IN ($queuet,'NONE')
										AND 	ac.event IN ('COMPLETECALLER','COMPLETEAGENT')
										GROUP BY HOUR(qs.datetime)");
				
			while($row2 = mysql_fetch_assoc($res2)){
	
			$answer_count[] = (float)$row2[answer_count];
			$datetime[] 	= $row2[times];
			}
			
			//------------------------------- მომსახურების დონე(Service Level)
			
			
			
			$res_service_level = mysql_query("	SELECT 	qs.info1
					FROM 	queue_stats AS qs,
					qname AS q,
					qagent AS ag,
					qevent AS ac
					WHERE 	qs.qname = q.qname_id
					AND qs.qagent = ag.agent_id
					AND qs.qevent = ac.event_id
					AND DATE(qs.datetime) >= '$start'
					AND DATE(qs.datetime) <= '$end'
					AND q.queue IN ($queuet)
					AND ac.event IN ('CONNECT')
					");
			$w15 = 0;
			$w30 = 0;
			$w45 = 0;
			$w60 = 0;
			$w75 = 0;
			$w90 = 0;
			$w91 = 0;
			
			
			
			
			while ($res_service_level_r = mysql_fetch_assoc($res_service_level)) {
			
			if ($res_service_level_r['info1'] < 15) {
			$w15++;
			}
			
			if ($res_service_level_r['info1'] < 30){
			$w30++;
			}
			
		if ($res_service_level_r['info1'] < 45){
					$w45++;
			}
			
			if ($res_service_level_r['info1'] < 60){
			$w60++;
			}
			
			if ($res_service_level_r['info1'] < 75){
			$w75++;
			}
			
			if ($res_service_level_r['info1'] < 90){
			$w90++;
			}
			
			$w91++;
			
			}	
$mas = array($w15,$w30,$w45,$w60,$w75,$w90,$w91);
$call_second=array('15 წამში','30 წამში','45წამში','60 წამში','75 წამში','90 წამში','90+წამში');			
							
$unit[]="ზარი";
$series[] = array('name' => $name, 'unit' => $unit, 'quantity' => $quantity, 'cause' => $cause);
$series[] = array('name' => $name, 'unit' => $unit, 'call_count' => $call_count, 'agent' => $agent);
$series[] = array('name' => $name, 'unit' => $unit, 'answer_count' => $answer_count, 'datetime' => $datetime);
$series[] = array('name' => $name, 'unit' => $unit, 'answer_count1' => $answer_count1, 'datetime1' => $datetime1);
$series[] = array('name' => $name, 'unit' => $unit, 'answer_count2' => $answer_count2, 'datetime2' => $datetime2);
$series[] = array('name' => $name, 'unit' => $unit, 'answer_count3' => $answer_count3, 'cause1' => $cause1);
$series[] = array('name' => $name, 'unit' => $unit, 'count1' => $count1, 'queue1' => $queue1);
$series[] = array('name' => $name, 'unit' => $unit, 'count2' => $count2, 'queue2' => $queue2);
$series[] = array('name' => $name, 'unit' => $unit, 'unanswer_call' => $unanswer_call, 'times' => $times);
$series[] = array('name' => $name, 'unit' => $unit, 'unanswer_count1' => $unanswer_count1, 'times2' => $times2);
$series[] = array('name' => $name, 'unit' => $unit, 'unanswer_count2' => $unanswer_call2, 'date1' => $date1);
$series[] = array('name' => $name, 'unit' => $unit, 'mas' => $mas, 'call_second' => $call_second);

echo json_encode($series);

?>