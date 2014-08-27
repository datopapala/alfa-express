<?php

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$begintime = $time;
$inuse      = Array();
$dict_queue = Array();

require("config.php");
require("asmanager.php");
require("realtime_functions.php");
if(isset($_SESSION['QSTATS']['hideloggedoff'])) {
    $ocultar= $_SESSION['QSTATS']['hideloggedoff'];
} else {
    $ocultar="false";
}
if(isset($_SESSION['QSTATS']['filter'])) {
    $filter= $_SESSION['QSTATS']['filter'];
} else {
    $filter="";
}

$am=new AsteriskManager();
$am->connect($manager_host,$manager_user,$manager_secret);

$channels = get_channels ($am);
foreach($channels as $ch=>$chv) {
  list($chan,$ses) = split("-",$ch,2);
  $inuse["$chan"]=$ch;
}

$queues   = get_queues   ($am,$channels);

foreach ($queues as $key=>$val) {
  $queue[] = $key;
}

///QUEUES
echo "<h2>".$lang[$language]['agent_status']."</h2><br/>";

$color['unavailable']="#dadada";
$color['unknown']="#dadada";
$color['busy']="#d0303f";
$color['dialout']="#d0303f";
$color['ringing']="#d0d01f";
$color['not in use']="#00ff00";
$color['paused']="#000000";

$test = array(
	'0' => "2555130",
	'1' => "2551441",
	'2' => "2470017",
	'3' => "2555155"
);

foreach($test  as $qn) {
	if($filter=="" || stristr($qn,$filter)) {
		$contador=1;
		if(!isset($queues[$qn]['members'])) continue;

		foreach($queues[$qn]['members'] as $key=>$val) {
			 
			$stat="";
			$last="";
			$dur="";
			$clid="";
			$akey = $queues[$qn]['members'][$key]['agent'];
			$aname = $queues[$qn]['members'][$key]['name'];
			$aval = $queues[$qn]['members'][$key]['type'];
			if(array_key_exists($key,$inuse)) {
				if($aval=="not in use") {
					$aval = "dialout";
				}
				if($channels[$inuse[$key]]['duration']=='') {
					$newkey = $channels[$inuse[$key]]['bridgedto'];
					$dur = $channels[$newkey]['duration_str'];
					$clid = $channels[$newkey]['callerid'];
				} else {
					$newkey = $channels[$inuse[$key]]['bridgedto'];
					$clid = $channels[$newkey]['callerid'];
					$dur = $channels[$inuse[$key]]['duration_str'];
				}
			}
			$stat = $queues[$qn]['members'][$key]['status'];
			$last = $queues[$qn]['members'][$key]['lastcall'];

			if(($aval == "unavailable" || $aval == "unknown") && $ocultar=="true") {
				// Skip
			} else {
				if($contador==1) {
					echo "<table width='520' cellpadding=3 cellspacing=3 border=0 id='box-table-b'>\n";
					echo "<thead>";
					echo "<tr>";
					echo "<th style='width: 10%;'>".$lang[$language]['queue']."</th>";
					echo "<th style='width: 8%;'>".$lang[$language]['agent']."</th>";
					echo "<th style='width: 35%;'>".$lang[$language]['state']."</th>";
					echo "<th style='width: 10%;'>".$lang[$language]['durat']."</th>";
					echo "<th style='width: 40%;'>".$lang[$language]['clid']."</th>";
					echo "<th>".$lang[$language]['last_in_call']."</th>";
					echo "</tr>\n";
					echo "</thead><tbody>\n";
				}

				if($contador%2) {
					$odd="class='odd'";
				} else {
					$odd="";
				}

				if($last<>"") {
					$last=$last." ".$lang[$language]['min_ago'];
				} else {
					$last = $lang[$language]['no_info'];
				}

				$agent_name = agent_name($aname);

				echo "<tr $odd>";
				echo "<td width=200>$qn</td>";
				echo "<td width=200>$agent_name</td>";

				if($stat<>"") {
				$aval="paused";
			}

			if(!array_key_exists($key,$inuse)) {
					if($aval=="busy") $aval="not in use";
			}

			$aval2 = ereg_replace(" ","_",$aval);
			$mystringaval = $lang[$language][$aval2];

			if($mystringaval=="") $mystringaval = $aval;
			echo "<td><div style='float: left; background: ".$color[$aval]."; width: 1em;'>&nbsp;</div>&nbsp; $mystringaval</td>";
			echo "<td>$dur</td>";
			echo "<td style='cursor: pointer;' id='cid' class='number' number='$clid'>$clid</td>";
			echo "<td>$last</td>";
			echo "</tr>";
			$contador++;
			}
			}
		if($contador>1) {
		echo "</tbody>";
		echo "</table><br/>\n";
		}
	}
}

///QUEUE details
echo "<BR><h2>".$lang[$language]['calls_waiting_detail']."</h2><BR>";
			
foreach($test as $qn) {
	$position=1;
	if(!isset($queues[$qn]['calls']))  continue;

	foreach($queues[$qn]['calls'] as $key=>$val) {
		if($position==1) {
			echo "<table width='520' cellpadding=3 cellspacing=3 border=0 class='sortable' id='box-table-b' >\n";
			echo "<thead>";
			echo "<tr>";
			echo "<th>".$lang[$language]['queue']."</th>";
			echo "<th>".$lang[$language]['position']."</th>";
			echo "<th>".$lang[$language]['callerid']."</th>";
			echo "<th>".$lang[$language]['wait_time']."</th>";
			echo "</tr>\n";
			echo "</thead>\n";
			echo "<tbody>\n";
		}

		if($position%2) {
			$odd="class='odd'";
		} else {
			$odd="";
		}
			
		echo "<tr $odd>";
		echo "<td>$qn</td><td>$position</td>";
		echo "<td>".$queues[$qn]['calls'][$key]['chaninfo']['callerid']."</td>";
		echo "<td>".$queues[$qn]['calls'][$key]['chaninfo']['duration_str']." წუთი</td>";
        echo "</tr>";
		$position++;
	}
			
	if($position>1) {
	echo "</tbody>\n";
	echo "</table>\n";
	}
}

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$endtime = $time;
$totaltime = ($endtime - $begintime);

?>

