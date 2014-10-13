 <?php

require_once('../../includes/classes/core.php');

//----------------------------- ცვლადი

$agent	= $_REQUEST['agent'];
$queue	= $_REQUEST['queuet'];
$start_time = $_REQUEST['start_time'];
$end_time 	= $_REQUEST['end_time'];
$day = (strtotime($end_time)) -  (strtotime($start_time));
$day_format = ($day / (60*60*24)) + 1;
// ----------------------------------

$row_done_blank = mysql_fetch_assoc(mysql_query("	SELECT COUNT(*) AS `count`
		FROM `incomming_call`
		WHERE DATE(date) >= '$start_time' AND DATE(date) <= '$end_time' AND phone = '' "));

mysql_close();
$conn = mysql_connect('212.58.116.81', 'adrenali_user', 'TdGroupChat1');
mysql_select_db('adrenali_chat');
mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $conn);


$data		= array('page' => array(
										'answer_call' => '',
										'technik_info' => '',
										'report_info' => ''
								));


//------------------------------- ტექნიკური ინფორმაცია

	$row_chat = mysql_fetch_assoc(mysql_query("	SELECT 		COUNT(*) AS `unanswer_chat`,
															(
																SELECT count(DISTINCT chat_id)
																FROM `chat_messages`
																WHERE operator_name !='' 
																AND DATE(FROM_UNIXTIME(time)) >= '$start_time'
																AND DATE(FROM_UNIXTIME(time)) <= '$end_time'
															) AS `answer_chat`,
															(
																SELECT COUNT(*)
																FROM `chat_chat`
																WHERE DATE(chat_chat.cur_time) >= '$start_time'
																AND DATE(chat_chat.cur_time) <= '$end_time'
																AND chat_chat.department_name IN ($queue)
																AND chat_chat.chat_status IN (1,2,3)
															) AS `total_chat`,
															department_name,
															SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(from_unixtime(chat_chat.time_end), from_unixtime(chat_chat.time_start))))) AS `total_time`,
															SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(from_unixtime(chat_chat.time_end), from_unixtime(chat_chat.time_start))))/COUNT(*)) AS `avg_time`
												FROM   		chat_chat
												WHERE  		chat_chat.chat_status = 3
												AND DATE(chat_chat.cur_time) >= '$start_time'
												AND DATE(chat_chat.cur_time) <= '$end_time'
												AND chat_chat.department_name IN ($queue)
												"));
	
	
	$data['page']['technik_info'] = '
							
                    <td>ჩატი</td>
                    <td>'.$row_chat[total_chat].'</td>
                    <td>'.$row_chat[answer_chat].'</td>
                    <td>'.($row_chat[unanswer_chat] - $row_chat[answer_chat]).'</td>
                    <td>'.$row_done_blank[count].'</td>
                    <td>'.round((($row_chat[answer_chat] / $row_chat[total_chat]) * 100),2).' %</td>
                    <td>'.round((($row_chat[unanswer_chat] / $row_chat[total_chat]) * 100),2).' %</td>
                    <td>'.round((($row_done_blank[count] / $row_chat[answer_chat]) * 100),2).' %</td>
                
							';
// -----------------------------------------------------
	
//---------------------------------------- რეპორტ ინფო

	$data['page']['report_info'] = '
				
                <tr>
                    <td class="tdstyle">რიგი:</td>
                    <td>'.$queue.'</td>
                </tr>
                <tr>
                    <td class="tdstyle">საწყისი თარიღი:</td>
                    <td>'.$start_time.'</td>
                </tr>
                <tr>
                    <td class="tdstyle">დასრულების თარიღი:</td>
                    <td>'.$end_time.'</td>
                </tr>
                <tr>
                    <td class="tdstyle">პერიოდი:</td>
                    <td>'.$day_format.' დღე</td>
                </tr>

							';
	
//----------------------------------------------


echo json_encode($data);

?>