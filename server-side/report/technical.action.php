<?php

mysql_close();
$conn = mysql_connect('212.72.155.176', 'root', '');
mysql_select_db('stats');


	
$agent	= $_REQUEST['agent'];
$queue	= $_REQUEST['queuet'];
$start_time = $_REQUEST['start_time'];
$end_time 	= $_REQUEST['end_time'];
$data		= array('page' => array(
										'answer_call' => '',
										'technik_info' => '',
										'report_info' => '',
										'answer_call_info' => '',
										'answer_call_by_queue' => '',
										'disconnection_cause' => '',
										'unanswer_call' => ''
								));

	
mysql_query("SET @i=0;");
//foreach ()
$res = mysql_query("SELECT 		@i := @i + 1 AS `id`,
								qname.queue,
								COUNT(*) AS `quant`,
								ROUND((COUNT(*) / (SELECT COUNT(*) FROM queue_stats WHERE  qname.queue IN ($queue) AND queue_stats.qevent = 10 AND  DATE(queue_stats.datetime) >= '$start_time' AND DATE(queue_stats.datetime) <= '$end_time') * 100), 2) AS `percent`
					FROM 		`queue_stats`
					JOIN 		qname ON queue_stats.qname = qname.qname_id
					WHERE 		queue_stats.qevent = 10 AND DATE(queue_stats.datetime) >= '$start_time' AND DATE(queue_stats.datetime) <= '$end_time' AND qname.queue IN ($queue)
					GROUP BY 	queue_stats.qname"); 

$res1 = mysql_query("SELECT 	@i := @i + 1 AS `iterator`,
								qagent.agent,
								COUNT(*) AS `quant`,
								ROUND((COUNT(*) / (SELECT COUNT(*) FROM queue_stats WHERE qagent.agent IN ($agent) AND queue_stats.qevent = 10 AND  DATE(queue_stats.datetime) >= '$start_time' AND DATE(queue_stats.datetime) <= '$end_time') * 100), 2) AS `percent`
					FROM 		`queue_stats`
					JOIN 		qagent ON queue_stats.qagent = qagent.agent_id
					WHERE 		queue_stats.qevent = 10 AND DATE(queue_stats.datetime) >= '$start_time' AND DATE(queue_stats.datetime) <= '$end_time' AND qagent.agent IN ($agent)
					GROUP BY 	queue_stats.qagent");

$res2 = mysql_query("SELECT	COUNT(*) as counter
					FROM	queue_stats AS qs,
								qname AS q, 
								qagent AS ag,
								qevent AS ac
					WHERE qs.qname = q.qname_id AND qs.qagent = ag.agent_id AND 
					qs.qevent = ac.event_id AND DATE(qs.datetime) = CURDATE() AND 
					q.queue IN ('2470017') AND ag.agent in ('ALF1','ALF2','ALF3','ALF4') AND ac.event IN ( 'COMPLETEAGENT') ORDER BY ag.agent");

$res3 = mysql_query("SELECT	COUNT(*) as counter
					FROM	queue_stats AS qs,
							qname AS q,
							qagent AS ag,
							qevent AS ac
					WHERE qs.qname = q.qname_id AND qs.qagent = ag.agent_id AND
					qs.qevent = ac.event_id AND DATE(qs.datetime) = CURDATE() AND
					q.queue IN ('2470017') AND ag.agent in ('ALF1','ALF2','ALF3','ALF4') AND ac.event IN ('COMPLETECALLER') ORDER BY ag.agent");

$res4 = mysql_query("SELECT COUNT(*) as unanswer, q.queue AS qname
					FROM queue_stats AS qs, qname AS q, 
					qagent AS ag, qevent AS ac WHERE qs.qname = q.qname_id AND qs.qagent = ag.agent_id AND 
					qs.qevent = ac.event_id AND DATE(qs.datetime) = CURDATE()
					AND q.queue IN ('2470017') AND ac.event IN ('ABANDON', 'EXITWITHTIMEOUT') ORDER BY qs.datetime");

$res5 = mysql_query("SELECT COUNT(*) as unanswer, q.queue AS qname
					FROM queue_stats AS qs, qname AS q,
					qagent AS ag, qevent AS ac WHERE qs.qname = q.qname_id AND qs.qagent = ag.agent_id AND
					qs.qevent = ac.event_id AND DATE(qs.datetime) = CURDATE()
					AND q.queue IN ('2470017') AND ac.event IN ('ABANDON', 'EXITWITHTIMEOUT') ORDER BY qs.datetime");

$res6 = mysql_query("SELECT DISTINCT (SELECT count(*) FROM queue_stats AS qs, qname AS q,qagent AS ag, qevent AS ac WHERE qs.qname = q.qname_id AND qs.qagent = ag.agent_id AND 
					qs.qevent = ac.event_id AND DATE(qs.datetime) = CURDATE()
					AND q.queue IN ('2470017','NONE') AND ac.event IN ('COMPLETECALLER','COMPLETEAGENT','AGENTLOGIN','AGENTLOGOFF','AGENTCALLBACKLOGIN')) as answer,
					(SELECT count(*) FROM queue_stats AS qs, qname AS q,qagent AS ag, qevent AS ac WHERE qs.qname = q.qname_id AND qs.qagent = ag.agent_id AND 
					qs.qevent = ac.event_id AND DATE(qs.datetime) = CURDATE()
					AND q.queue IN ('2470017','NONE') AND ac.event IN ('ABANDON', 'EXITWITHTIMEOUT')) as unanswer, CURDATE() as date
					FROM queue_stats");


while($row = mysql_fetch_assoc($res)){

	$data['page']['answer_call'] = '
							<tr><td>'.$row[queue].'</td><td>'.$row[quant].' ზარები</td><td>'.$row[percent].' %</td></tr>
							';

}

while($row = mysql_fetch_assoc($res1)){

	$data['page']['technik_info'] = '
							
                    <td>ზარი</td>
                    <td>'.$row[agent].'</td>
                    <td>'.$row[agent].'</td>
                    <td>'.$row[agent].'</td>
                    <td>'.$row[agent].'</td>
                    <td>'.$row[agent].' %</td>
                    <td>'.$row[agent].' %</td>
                    <td>'.$row[agent].' %</td>
                
							';

}

while($row = mysql_fetch_assoc($res2)){

	$data['page']['report_info'] = '
				
                    <tr>
                    <td>რიგი:</td>
                    <td>'.$row[counter].'</td>
                </tr>
                
                       <tr><td>საწყისი თარიღი:</td>
                       <td>'.$row[counter].'</td>
                </tr>
                
                <tr>
                       <td>დასრულების თარიღი:</td>
                       <td>'.$row[counter].'</td>
                </tr>
                <tr>
                       <td>პერიოდი:</td>
                       <td>'.$row[counter].'</td>
                </tr>

							';

}

while($row = mysql_fetch_assoc($res3)){

	$data['page']['answer_call_info'] = '

                   	<tr>
					<td>ნაპასუხები ზარები</td>
					<td>'.$row[counter].' ზარი</td>
					</tr>
					<tr>
					<td>გადამისამართებული ზარები</td>
					<td>'.$row[counter].' ზარი</td>
					</tr>
					<tr>
					<td>საშ. ხანგძლივობა:</td>
					<td>'.$row[counter].' წამი</td>
					</tr>
					<tr>
					<td>სულ საუბრის ხანგძლივობა:</td>
					<td>'.$row[counter].' წუთი</td>
					</tr>
					<tr>
					<td>ლოდინის საშ. ხანგძლივობა:</td>
					<td>'.$row[counter].' წამი</td>
					</tr>

							';

}

while($row = mysql_fetch_assoc($res4)){

	$data['page']['answer_call_by_queue'] = '

                   	<tr>
					<td>'.$row[qname].'</td>
					<td>'.$row[qname].'</td>
					<td>'.$row[qname].' %</td>
					<td>'.$row[qname].' წუთი</td>
					<td>'.$row[qname].' %</td>
					<td>'.$row[qname].' წუთი</td>
					<td>'.$row[qname].' წამი</td>
					<td>'.$row[qname].' წამი</td>
					</tr>

							';

}

while($row = mysql_fetch_assoc($res5)){

	$data['page']['disconnection_cause'] = '

                   <tr>
					<td>ოპერატორმა გათიშა:</td>
					<td>'.$row[qname].' ზარი</td>
					<td>'.$row[qname].'%</td>
					</tr>
					<tr>
					<td>აბონენტმა გათიშა:</td>
					<td>'.$row[qname].' ზარი</td>
					<td>'.$row[qname].' %</td>
					</tr>

							';

}

while($row = mysql_fetch_assoc($res6)){

	$data['page']['unanswer_call'] = '

                   	<tr>
					<td>უპასუხო ზარების რაოდენობა:</td>
					<td>'.$row[unanswer].' ზარი</td>
					</tr>
					<tr>
					<td>ლოდინის საშ. დრო კავშირის გაწყვეტამდე:</td>
					<td>'.$row[unanswer].' წამი</td>
					</tr>
					<tr>
					<td>საშ. რიგში პოზიცია კავშირის გაწყვეტამდე:</td>
					<td>'.$row[unanswer].'</td>
					</tr>
					<tr>
					<td>საშ. საწყისი პოზიცია რიგში:</td>
					<td>'.$row[unanswer].'</td>
					</tr>

							';

}


// $disconect = '';
// while ($row = mysql_fetch_assoc($res2)) {
// 	if ($row[id]%2 ) {
// 		$odd = 'class="odd"';
// 	}

// 	$disconect .= '<tr '. $odd .'>
// 			 		<th style="width: 80px;">ოპერატორი</th>
// 		 			<th style="width: 80px;">'.$row[counter].'</th>
// 		 			<th style="width: 80px;">0</th>
// 		 		</tr>';
// }

// $distribution  = '';
// while ($row = mysql_fetch_assoc($res6)) {
// 	if ($row[id]%2 ) {
// 		$odd = 'class="odd"';
// 	}

// 	$distribution .= '<tr '. $odd .'>
// 			 		<th style="width: 80px;">'.$row[date].'</th>
// 		 			<th style="width: 80px;">'.$row[answer].'</th>
// 		 			<th style="width: 80px;">'.$row[unanswer].'</th>
// 		 		</tr>';
// }

// $disconect1 = '';
// while ($row = mysql_fetch_assoc($res3)) {
// 	if ($row[id]%2 ) {
// 		$odd = 'class="odd"';
// 	}

// 	$disconect1 .= '<tr '. $odd .'>
// 			 		<th style="width: 80px;">მომხმარებელი</th>
// 		 			<th style="width: 80px;">'.$row[counter].'</th>
// 		 			<th style="width: 80px;">0</th>
// 		 		</tr>';
// }

// $unanswer = '';
// while ($row = mysql_fetch_assoc($res4)) {
// 	if ($row[id]%2 ) {
// 		$odd = 'class="odd"';
// 	}

// 	$unanswer .= '<tr '. $odd .'>
// 			 		<th style="width: 80px;">'.$row[qname].'</th>
// 		 			<th style="width: 80px;">'.$row[unanswer].'</th>
// 		 			<th style="width: 80px;">0</th>
// 		 		</tr>';
// }

// $unanswer1 = '';
// while ($row = mysql_fetch_assoc($res5)) {
// 	if ($row[id]%2 ) {
// 		$odd = 'class="odd"';
// 	}

// 	$unanswer1 .= '<tr '. $odd .'>
// 			 		<th style="width: 80px;">User Abandon</th>
// 		 			<th style="width: 80px;">'.$row[unanswer].'</th>
// 		 			<th style="width: 80px;">0</th>
// 		 		</tr>';
// }

// $agent = '';
// while ($row = mysql_fetch_assoc($res1)) {
// 	if ($row[id]%2 ) {
// 		$odd = 'class="odd"';
// 	}

// 	$agent .= '
// 			<tr>
// 				<td>ALF1</td>
// 				<td>32</td>
// 				<td>15.24 %</td>
// 				<td>78:35 min</td>
// 				<td>15.33 %</td>
// 				<td>2:27 min</td>
// 				<td>1260 secs</td>
// 				<td>39.38 secs</td>
// 			</tr>
// 			<tr '. $odd .'>
// 			 		<th style="width: 80px;">'.$row[agent].'</th>
// 		 			<th style="width: 80px;">'.$row[quant].'</th>
// 		 			<th style="width: 80px;">'.$row[percent].'</th>
// 		 		</tr>';
// }


echo json_encode($data);

?>