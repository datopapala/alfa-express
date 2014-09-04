<?php

require_once('../../includes/classes/core.php');

//----------------------------- ცვლადი

$agent	= $_REQUEST['agent'];
$queue	= $_REQUEST['queuet'];
$start_time = $_REQUEST['start_time'];
$end_time 	= $_REQUEST['end_time'];
$day = (strtotime($end_time)) -  (strtotime($start_time));
$day_format = (int)date('d', $day);
// ----------------------------------

$row_done_blank = mysql_fetch_assoc(mysql_query("	SELECT COUNT(*) AS `count`
		FROM `incomming_call`
		WHERE DATE(date) >= '$start_time' AND DATE(date) <= '$end_time' AND phone != '' "));

mysql_close();
$conn = mysql_connect('212.72.155.176', 'root', 'Gl-1114');
mysql_select_db('stats');


$data		= array('page' => array(
										'answer_call' => '',
										'technik_info' => '',
										'report_info' => '',
										'answer_call_info' => '',
										'answer_call_by_queue' => '',
										'disconnection_cause' => '',
										'unanswer_call' => '',
										'disconnection_cause_unanswer' => '',
										'unanswered_calls_by_queue' => '',
										'totals' => '',
										'call_distribution_per_day' => '',
										'call_distribution_per_hour' => '',
										'call_distribution_per_day_of_week' => '',
										'service_level' => ''
								));


//------------------------------- ტექნიკური ინფორმაცია

	$row_answer = mysql_fetch_assoc(mysql_query("	SELECT	COUNT(*) AS `count`,
															q.queue AS `queue`
													FROM	queue_stats AS qs,
															qname AS q,
															qagent AS ag,
															qevent AS ac
													WHERE qs.qname = q.qname_id 
													AND qs.qagent = ag.agent_id 
													AND qs.qevent = ac.event_id 
													AND DATE(qs.datetime) >= '$start_time' AND DATE(qs.datetime) <= '$end_time'
													AND q.queue IN ($queue) 
													AND ag.agent in ($agent)
													AND ac.event IN ( 'COMPLETECALLER', 'COMPLETEAGENT') 
													ORDER BY ag.agent"));
	
	$row_abadon = mysql_fetch_assoc(mysql_query("	SELECT 	COUNT(*) AS `count`,
															ROUND((SUM(qs.info3) / COUNT(*))) AS `sec`
													FROM	queue_stats AS qs,
															qname AS q, 
															qagent AS ag,
															qevent AS ac
													WHERE qs.qname = q.qname_id
													AND qs.qagent = ag.agent_id
													AND qs.qevent = ac.event_id
													AND DATE(qs.datetime) >= '$start_time'
													AND DATE(qs.datetime) <= '$end_time' 
													AND q.queue IN ($queue) 
													AND ac.event IN ('ABANDON', 'EXITWITHTIMEOUT')"));
	
	
	
	
	$data['page']['technik_info'] = '
							
                    <td>ზარი</td>
                    <td>'.($row_answer[count] + $row_abadon[count]).'</td>
                    <td>'.$row_answer[count].'</td>
                    <td>'.$row_abadon[count].'</td>
                    <td>'.$row_done_blank[count].'</td>
                    <td>'.round(((($row_answer[count]) / ($row_answer[count] + $row_abadon[count])) * 100),2).' %</td>
                    <td>'.round(((($row_abadon[count]) / ($row_answer[count] + $row_abadon[count])) * 100),2).' %</td>
                    <td>'.round(((($row_done_blank[count]) / ($row_answer[count] + $row_abadon[count])) * 100),2).' %</td>
                
							';
// -----------------------------------------------------

//------------------------------- ნაპასუხები ზარები რიგის მიხედვით

	$data['page']['answer_call'] = '
							<tr><td>'.$row_answer[queue].'</td><td>'.$row_answer[count].' ზარი</td><td>'.round(((($row_answer[count]) / ($row_answer[count] + $row_abadon[count])) * 100)).' %</td></tr>
							';

//-------------------------------------------------------

//------------------------------- მომსახურების დონე(Service Level)

	
	
	$res = mysql_query("	SELECT 	qs.info1
							FROM 	queue_stats AS qs,
									qname AS q,
									qagent AS ag,
									qevent AS ac 
							WHERE 	qs.qname = q.qname_id 
							AND qs.qagent = ag.agent_id 
							AND qs.qevent = ac.event_id 
							AND DATE(qs.datetime) >= '$start_time'
							AND DATE(qs.datetime) <= '$end_time'
							AND q.queue IN ($queue)
							AND ag.agent in ($agent) 
							AND ac.event IN ('CONNECT')
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
	
	
	
	
	
	$data['page']['service_level'] = '
							
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
							';
	
//-------------------------------------------------------
	
//---------------------------------------- რეპორტ ინფო

	$data['page']['report_info'] = '
				
                    <tr>
                    <td>რიგი:</td>
                    <td>'.$queue.'</td>
                </tr>
                
                       <tr><td>საწყისი თარიღი:</td>
                       <td>'.$start_time.'</td>
                </tr>
                
                <tr>
                       <td>დასრულების თარიღი:</td>
                       <td>'.$end_time.'</td>
                </tr>
                <tr>
                       <td>პერიოდი:</td>
                       <td>'.$day_format.' დღე</td>
                </tr>

							';
	
//----------------------------------------------


//------------------------------------ ნაპასუხები ზარები

$row_transfer = mysql_fetch_assoc(mysql_query("	SELECT	COUNT(*) AS `count`
												FROM	queue_stats AS qs,
												qname AS q,
												qagent AS ag,
												qevent AS ac
												WHERE qs.qname = q.qname_id
												AND qs.qagent = ag.agent_id
												AND qs.qevent = ac.event_id
												AND DATE(qs.datetime) >= '$start_time' AND DATE(qs.datetime) <= '$end_time'
												AND q.queue IN ($queue)
												AND ag.agent in ($agent)
												AND ac.event IN ( 'TRANSFER')
												ORDER BY ag.agent"));

$row_clock = mysql_fetch_assoc(mysql_query("	SELECT	ROUND((SUM(qs.info1) / COUNT(*)),2) AS `hold`,
														ROUND((SUM(qs.info2) / COUNT(*)),2) AS `sec`,
														ROUND((SUM(qs.info2) / 60 ),2) AS `min`
												FROM 	queue_stats AS qs,
														qname AS q,
														qagent AS ag,
														qevent AS ac 
												WHERE	qs.qname = q.qname_id 
												AND qs.qagent = ag.agent_id 
												AND qs.qevent = ac.event_id 
												AND q.queue IN ($queue) 
												AND DATE(qs.datetime) >= '$start_time' AND DATE(qs.datetime) <= '$end_time'
												AND ac.event IN ('COMPLETECALLER', 'COMPLETEAGENT')
												ORDER BY qs.datetime"));




	$data['page']['answer_call_info'] = '

                   	<tr>
					<td>ნაპასუხები ზარები</td>
					<td>'.$row_answer[count].' ზარი</td>
					</tr>
					<tr>
					<td>გადამისამართებული ზარები</td>
					<td>'.$row_transfer[count].' ზარი</td>
					</tr>
					<tr>
					<td>საშ. ხანგძლივობა:</td>
					<td>'.$row_clock[sec].' წამი</td>
					</tr>
					<tr>
					<td>სულ საუბრის ხანგძლივობა:</td>
					<td>'.$row_clock[min].' წუთი</td>
					</tr>
					<tr>
					<td>ლოდინის საშ. ხანგძლივობა:</td>
					<td>'.$row_clock[hold].' წამი</td>
					</tr>

							';
	
//---------------------------------------------

/* 	$res4 = mysql_query("SELECT 	ag.agent as `agent`,
			COUNT(*) as `call`,
			ROUND((SUM(qs.info2) / 60 ),2) AS info2,
			ROUND((SUM(qs.info1) / COUNT(*)),2) AS `hold`
				
			FROM queue_stats AS qs, qname AS q,
			qagent AS ag, qevent AS ac WHERE qs.qname = q.qname_id AND qs.qagent = ag.agent_id AND
			qs.qevent = ac.event_id AND DATE(qs.datetime) >= '$start_time' AND DATE(qs.datetime) <= '$end_time' AND
			q.queue IN ($queue) AND ag.agent in ($agent) AND ac.event IN ('COMPLETECALLER', 'COMPLETEAGENT')
			GROUP BY 	qs.qagent"); */
	
 	$ress =mysql_query("SELECT ag.agent as `agent`,
			COUNT(*) as `call`,
			ROUND((SUM(qs.info2) / 60 )) AS info2,
			ROUND((SUM(qs.info1) / COUNT(*))) AS `hold`
 			  FROM queue_stats AS qs, qname AS q, 
qagent AS ag, qevent AS ac WHERE qs.qname = q.qname_id AND qs.qagent = ag.agent_id AND 
qs.qevent = ac.event_id AND DATE(qs.datetime) >= '$start_time' AND DATE(qs.datetime) <= '$end_time' AND 
q.queue IN ($queue) AND ag.agent in ($agent) AND ac.event IN ('COMPLETECALLER', 'COMPLETEAGENT') GROUP BY 	qs.qagent"); 

while($row = mysql_fetch_assoc($ress)){

	$data['page']['answer_call_by_queue'] .= '

                   	<tr>
					<td>'.$row[agent].'</td>
					<td>'.$row[call].'</td>
					<td>'.$row[info2].' %</td>
					<td>'.$row[info2].' წუთი</td>
					<td>'.$row[hold].' %</td>
					<td>'.$row[qname].' წუთი</td>
					<td>'.$row[qname].' წამი</td>
					<td>'.$row[qname].' წამი</td>
					</tr>

							';

}

//--------------------------- კავშირის გაწყვეტის მიზეზეი


$row_COMPLETECALLER = mysql_fetch_assoc(mysql_query("	SELECT	COUNT(*) AS `count`,
																	q.queue AS `queue`
												FROM	queue_stats AS qs,
														qname AS q,
														qagent AS ag,
														qevent AS ac
												WHERE qs.qname = q.qname_id
												AND qs.qagent = ag.agent_id
												AND qs.qevent = ac.event_id
												AND DATE(qs.datetime) >= '$start_time' AND DATE(qs.datetime) <= '$end_time'
												AND q.queue IN ($queue)
												AND ag.agent in ($agent)
												AND ac.event IN ( 'COMPLETECALLER')
												ORDER BY ag.agent"));

$row_COMPLETEAGENT = mysql_fetch_assoc(mysql_query("	SELECT	COUNT(*) AS `count`,
																q.queue AS `queue`
														FROM	queue_stats AS qs,
																qname AS q,
																qagent AS ag,
																qevent AS ac
														WHERE qs.qname = q.qname_id
														AND qs.qagent = ag.agent_id
														AND qs.qevent = ac.event_id
														AND DATE(qs.datetime) >= '$start_time' AND DATE(qs.datetime) <= '$end_time'
														AND q.queue IN ($queue)
														AND ag.agent in ($agent)
														AND ac.event IN (  'COMPLETEAGENT')
														ORDER BY ag.agent"));

	$data['page']['disconnection_cause'] = '

                   <tr>
					<td>ოპერატორმა გათიშა:</td>
					<td>'.$row_COMPLETEAGENT[count].' ზარი</td>
					<td>0.00 %</td>
					</tr>
					<tr>
					<td>აბონენტმა გათიშა:</td>
					<td>'.$row_COMPLETECALLER[count].' ზარი</td>
					<td>0.00 %</td>
					</tr>

							';

//-----------------------------------------------

//----------------------------------- უპასუხო ზარები


	$data['page']['unanswer_call'] = '

                   	<tr>
					<td>უპასუხო ზარების რაოდენობა:</td>
					<td>'.$row_abadon[count].' ზარი</td>
					</tr>
					<tr>
					<td>ლოდინის საშ. დრო კავშირის გაწყვეტამდე:</td>
					<td>'.$row_abadon[sec].' წამი</td>
					</tr>
					<tr>
					<td>საშ. რიგში პოზიცია კავშირის გაწყვეტამდე:</td>
					<td>1</td>
					</tr>
					<tr>
					<td>საშ. საწყისი პოზიცია რიგში:</td>
					<td>1</td>
					</tr>

							';


//--------------------------------------------

	
//----------------------------------- კავშირის გაწყვეტის მიზეზი

	$row_timeout = mysql_fetch_assoc(mysql_query("	SELECT 	COUNT(*) AS `count`
			FROM 	queue_stats AS qs,
			qname AS q,
			qagent AS ag,
			qevent AS ac
			WHERE qs.qname = q.qname_id
			AND qs.qagent = ag.agent_id
			AND qs.qevent = ac.event_id
			AND DATE(qs.datetime) >= '$start_time' AND DATE(qs.datetime) <= '$end_time'
			AND q.queue IN ($queue)
			AND ac.event IN ('EXITWITHTIMEOUT')
			ORDER BY qs.datetime"));
	

	$data['page']['disconnection_cause_unanswer'] = '

                  <tr> 
                  <td>აბონენტმა გათიშა</td>
			      <td>'.$row_abadon[count].' ზარი</td>
			      <td>'.round((($row_abadon[count] / $row_abadon[count]) * 100),2).' %</td>
		        </tr>
			    <tr> 
                  <td>დრო ამოიწურა</td>
			      <td>'.$row_timeout[count].' ზარი</td>
			      <td>'.round((($row_timeout[count] / $row_timeout[count]) * 100),2).' %</td>
		        </tr>

							';

//--------------------------------------------

//------------------------------ უპასუხო ზარები რიგის მიხედვით

	$Unanswered_Calls_by_Queue = mysql_fetch_assoc(mysql_query("	SELECT 	COUNT(*) AS `count`,
			q.queue as `queue`
			FROM 	queue_stats AS qs,
			qname AS q,
			qagent AS ag,
			qevent AS ac
			WHERE qs.qname = q.qname_id
			AND qs.qagent = ag.agent_id
			AND qs.qevent = ac.event_id
			AND DATE(qs.datetime) >= '$start_time' AND DATE(qs.datetime) <= '$end_time'
			AND q.queue IN ($queue)
			AND ac.event IN ('ABANDON')
			ORDER BY qs.datetime"));
	
	$data['page']['unanswered_calls_by_queue'] = '

                   	<tr><td>'.$Unanswered_Calls_by_Queue[queue].'</td><td>'.$Unanswered_Calls_by_Queue[count].' ზარი</td><td>'.round((($Unanswered_Calls_by_Queue[count] / $Unanswered_Calls_by_Queue[count]) * 100),2).' %</td></tr>

							';

//---------------------------------------------------

//------------------------------------------- სულ

	$data['page']['totals'] = '

                   	<tr> 
                  <td>ნაპასუხები ზარების რაოდენობა:</td>
		          <td>'.$row_answer[count].' ზარი</td>
	            </tr>
                <tr>
                  <td>უპასუხო ზარების რაოდენობა:</td>
                  <td>'.$row_abadon[count].' ზარი</td>
                </tr>
		        <tr>
                  <td>ოპერატორი შევიდა:</td>
		          <td>0</td>
	            </tr>
                <tr>
                  <td>ოპერატორი გავიდა:</td>
                  <td>0</td>
                </tr>

							';
	
//------------------------------------------------

	
	$res10 = mysql_query("SELECT DATE(qs.datetime) AS datetime, q.queue AS qname, ag.agent AS qagent, ac.event AS qevent,
	qs.info1 AS info1, qs.info2 AS info2,  qs.info3 AS info3 FROM queue_stats AS qs, qname AS q,
	qagent AS ag, qevent AS ac WHERE qs.qname = q.qname_id AND qs.qagent = ag.agent_id AND
	qs.qevent = ac.event_id AND DATE(qs.datetime) >= '$start_time' AND DATE(qs.datetime) <= '$end_time'
	AND q.queue IN ($queue,'NONE') AND ac.event IN ('ABANDON', 'EXITWITHTIMEOUT','COMPLETECALLER','COMPLETEAGENT','AGENTLOGIN','AGENTLOGOFF','AGENTCALLBACKLOGIN','AGENTCALLBACKLOGOFF') 
			GROUP BY 	DATE(qs.qagent)");
	
while($row = mysql_fetch_assoc($res10)){

	$data['page']['call_distribution_per_day'] .= '

                   	<tr class="odd">
					<td>'.$row[datetime].'</td>
					<td>'.$row[counter].'</td>
					<td>'.$row[counter].' %</td>
					<td>'.$row[counter].'</td>
					<td>'.$row[counter].' %</td>
					<td>'.$row[counter].' წუთი</td>
					<td>'.$row[counter].' წამი</td>
					<td>'.$row[counter].'</td>
					<td>'.$row[counter].'</td>
					</tr>

							';

}

while($row = mysql_fetch_assoc($res11)){

	$data['page']['call_distribution_per_hour'] = '

                   	<tr class="odd">
					<td>'.$row[counter].'</td>
					<td>'.$row[counter].'</td>
					<td>'.$row[counter].' %</td>
					<td>'.$row[counter].'</td>
					<td>'.$row[counter].'%</td>
					<td>'.$row[counter].' წამი</td>
					<td>'.$row[counter].' წამი</td>
					<td>'.$row[counter].'</td>
					<td>'.$row[counter].'</td>
					</tr>

							';

}

while($row = mysql_fetch_assoc($res12)){

	$data['page']['call_distribution_per_day_of_week'] = '

                   	<tr class="odd">
					<td>'.$row[counter].'</td>
					<td>'.$row[counter].'</td>
					<td>'.$row[counter].' %</td>
					<td>'.$row[counter].'</td>
					<td>'.$row[counter].'%</td>
					<td>'.$row[counter].' წამი</td>
					<td>'.$row[counter].' წამი</td>
					<td>'.$row[counter].'</td>
					<td>'.$row[counter].'</td>
					</tr>

							';

}

echo json_encode($data);

?>