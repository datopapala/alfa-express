<?php

require_once('../../includes/classes/core.php');

// ცვლადი

$agent	= $_REQUEST['agent'];
$queue	= $_REQUEST['queuet'];
$start_time = $_REQUEST['start_time'];
$end_time 	= $_REQUEST['end_time'];
$day = (strtotime($end_time)) -  (strtotime($start_time));
$day_format = (int)date('d', $day);


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
										'call_distribution_per_day_of_week' => ''
								));

	
mysql_query("SET @i=0;");

$res4 = mysql_query("SELECT 	ag.agent as `agent`,
								COUNT(*) as `call`,
								ROUND((SUM(qs.info2) / 60 ),2) AS info2,
								ROUND((SUM(qs.info1) / COUNT(*)),2) AS `hold`
								 
					FROM queue_stats AS qs, qname AS q, 
					qagent AS ag, qevent AS ac WHERE qs.qname = q.qname_id AND qs.qagent = ag.agent_id AND 
					qs.qevent = ac.event_id AND DATE(qs.datetime) >= '$start_time' AND DATE(qs.datetime) <= '$end_time' AND 
					q.queue IN ($queue) AND ag.agent in ($agent) AND ac.event IN ('COMPLETECALLER', 'COMPLETEAGENT')
					GROUP BY 	qs.qagent");

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

$res7 = mysql_query("SELECT	COUNT(*) as counter
					FROM	queue_stats AS qs,
							qname AS q,
							qagent AS ag,
							qevent AS ac
					WHERE qs.qname = q.qname_id AND qs.qagent = ag.agent_id AND
					qs.qevent = ac.event_id AND DATE(qs.datetime) = CURDATE() AND
					q.queue IN ('2470017') AND ag.agent in ('ALF1','ALF2','ALF3','ALF4') AND ac.event IN ('COMPLETECALLER') ORDER BY ag.agent");

$res8 = mysql_query("SELECT	COUNT(*) as counter
					FROM	queue_stats AS qs,
							qname AS q,
							qagent AS ag,
							qevent AS ac
					WHERE qs.qname = q.qname_id AND qs.qagent = ag.agent_id AND
					qs.qevent = ac.event_id AND DATE(qs.datetime) = CURDATE() AND
					q.queue IN ('2470017') AND ag.agent in ('ALF1','ALF2','ALF3','ALF4') AND ac.event IN ('COMPLETECALLER') ORDER BY ag.agent");

$res9 = mysql_query("SELECT	COUNT(*) as counter
					FROM	queue_stats AS qs,
							qname AS q,
							qagent AS ag,
							qevent AS ac
					WHERE qs.qname = q.qname_id AND qs.qagent = ag.agent_id AND
					qs.qevent = ac.event_id AND DATE(qs.datetime) = CURDATE() AND
					q.queue IN ('2470017') AND ag.agent in ('ALF1','ALF2','ALF3','ALF4') AND ac.event IN ('COMPLETECALLER') ORDER BY ag.agent");

$res10 = mysql_query("SELECT	COUNT(*) as counter
					FROM	queue_stats AS qs,
							qname AS q,
							qagent AS ag,
							qevent AS ac
					WHERE qs.qname = q.qname_id AND qs.qagent = ag.agent_id AND
					qs.qevent = ac.event_id AND DATE(qs.datetime) = CURDATE() AND
					q.queue IN ('2470017') AND ag.agent in ('ALF1','ALF2','ALF3','ALF4') AND ac.event IN ('COMPLETECALLER') ORDER BY ag.agent");

$res11 = mysql_query("SELECT	COUNT(*) as counter
					FROM	queue_stats AS qs,
							qname AS q,
							qagent AS ag,
							qevent AS ac
					WHERE qs.qname = q.qname_id AND qs.qagent = ag.agent_id AND
					qs.qevent = ac.event_id AND DATE(qs.datetime) = CURDATE() AND
					q.queue IN ('2470017') AND ag.agent in ('ALF1','ALF2','ALF3','ALF4') AND ac.event IN ('COMPLETECALLER') ORDER BY ag.agent");

$res12 = mysql_query("SELECT	COUNT(*) as counter
					FROM	queue_stats AS qs,
							qname AS q,
							qagent AS ag,
							qevent AS ac
					WHERE qs.qname = q.qname_id AND qs.qagent = ag.agent_id AND
					qs.qevent = ac.event_id AND DATE(qs.datetime) = CURDATE() AND
					q.queue IN ('2470017') AND ag.agent in ('ALF1','ALF2','ALF3','ALF4') AND ac.event IN ('COMPLETECALLER') ORDER BY ag.agent");




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
	
//-------------------------- რეპორტ ინფო

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


while($row = mysql_fetch_assoc($res4)){

	$data['page']['answer_call_by_queue'] = '

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

while($row = mysql_fetch_assoc($res9)){

	$data['page']['totals'] = '

                   	<tr> 
                  <td>ნაპასუხები ზარების რაოდენობა:</td>
		          <td>'.$row[counter].' ზარი</td>
	            </tr>
                <tr>
                  <td>უპასუხო ზარების რაოდენობა:</td>
                  <td>'.$row[counter].' ზარი</td>
                </tr>
		        <tr>
                  <td>ოპერატორი შევიდა:</td>
		          <td>'.$row[counter].'</td>
	            </tr>
                <tr>
                  <td>ოპერატორი გავიდა:</td>
                  <td>'.$row[counter].'</td>
                </tr>

							';

}

while($row = mysql_fetch_assoc($res10)){

	$data['page']['call_distribution_per_day'] = '

                   	<tr class="odd">
					<td>'.$row[counter].'</td>
					<td>'.$row[counter].'</td>
					<td>'.$row[counter].' %</td>
					<td>'.$row[counter].'</td>
					<td>'.$row[counter].' %</td>
					<td>2:'.$row[counter].' წუთი</td>
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