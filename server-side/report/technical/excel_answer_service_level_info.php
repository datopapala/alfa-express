<?php
require_once('../../../includes/classes/core.php');

$agent	= $_REQUEST['agent'];
$queue	= $_REQUEST['queuet'];
$start_time = $_REQUEST['start_time'];
$end_time 	= $_REQUEST['end_time'];

$res_service_level = mysql_query("	SELECT 	qs.info1
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

	$dat .= '
						<ss:Row>
							<ss:Cell ss:StyleID="headercell">
								<ss:Data ss:Type="String">15 წამში</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$w15.'</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String"></ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$p15.'%</ss:Data>
							</ss:Cell>
						</ss:Row>
						<ss:Row>
							<ss:Cell ss:StyleID="headercell">
								<ss:Data ss:Type="String">30 წამში</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$w30.'</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$d30.'</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$p30.'%</ss:Data>
							</ss:Cell>			
						</ss:Row>
						<ss:Row>
							<ss:Cell ss:StyleID="headercell">
								<ss:Data ss:Type="String">45 წამში</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$w45.'</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$d45.'</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$p45.'%</ss:Data>
							</ss:Cell>			
						</ss:Row>
						<ss:Row>
							<ss:Cell ss:StyleID="headercell">
								<ss:Data ss:Type="String">60 წამში</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$w60.'</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$d60.'</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$p60.'%</ss:Data>
							</ss:Cell>			
						</ss:Row>
						<ss:Row>
							<ss:Cell ss:StyleID="headercell">
								<ss:Data ss:Type="String">75 წამში</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$w75.'</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$d75.'</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$p75.'%</ss:Data>
							</ss:Cell>			
						</ss:Row>
						<ss:Row>
							<ss:Cell ss:StyleID="headercell">
								<ss:Data ss:Type="String">90 წამში</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$w90.'</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$d90.'</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$p90.'%</ss:Data>
							</ss:Cell>			
						</ss:Row>
						<ss:Row>
							<ss:Cell ss:StyleID="headercell">
								<ss:Data ss:Type="String">90+ წამში</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$w91.'</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">'.$d91.'</ss:Data>
							</ss:Cell>
							<ss:Cell>
								<ss:Data ss:Type="String">100%</ss:Data>
							</ss:Cell>
						</ss:Row>
										';

	$name = "მომსახურების დონე(Service Level)";



$data = '
<?xml version="1.0" encoding="utf-8"?><?mso-application progid="Excel.Sheet"?>
<ss:Workbook xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:o="urn:schemas-microsoft-com:office:office">
	<o:DocumentProperties>
		<o:Title>'.$name.'</o:Title>
	</o:DocumentProperties>
	<ss:ExcelWorkbook>
		<ss:WindowHeight>9000</ss:WindowHeight>
		<ss:WindowWidth>50000</ss:WindowWidth>
		<ss:ProtectStructure>false</ss:ProtectStructure>
		<ss:ProtectWindows>false</ss:ProtectWindows>
	</ss:ExcelWorkbook>
	<ss:Styles>
		<ss:Style ss:ID="Default">
			<ss:Alignment ss:Vertical="Center" ss:Horizontal="Center" ss:WrapText="1" />
			<ss:Font ss:FontName="Sylfaen" ss:Size="12" />
			<ss:Interior />
			<ss:NumberFormat />
			<ss:Protection />
			<ss:Borders>
				<ss:Border ss:Position="Top" ss:Color="#000000" ss:Weight="1" ss:LineStyle="Continuous" />
				<ss:Border ss:Position="Bottom" ss:Color="#000000" ss:Weight="1" ss:LineStyle="Continuous" />
				<ss:Border ss:Position="Left" ss:Color="#000000" ss:Weight="1" ss:LineStyle="Continuous" />
				<ss:Border ss:Position="Right" ss:Color="#000000" ss:Weight="1" ss:LineStyle="Continuous" />
			</ss:Borders>
		</ss:Style>
		<ss:Style ss:ID="title">
			<ss:Borders />
			<ss:NumberFormat ss:Format="@" />
			<ss:Alignment ss:WrapText="1" ss:Horizontal="Center" ss:Vertical="Center" />
		</ss:Style>
		<ss:Style ss:ID="headercell">
			<ss:Font ss:Bold="1" />
			<ss:Interior ss:Pattern="Solid" />
			<ss:Alignment ss:WrapText="1" ss:Horizontal="Center" ss:Vertical="Center" />
		</ss:Style>
		<ss:Style ss:ID="headercellRotated">
			<ss:Font ss:Bold="1" />
			<ss:Interior ss:Pattern="Solid" />
			<ss:Alignment ss:WrapText="1" ss:Horizontal="Center" ss:Rotate="90" ss:Vertical="Center" />
		</ss:Style>
	</ss:Styles>
	<ss:Worksheet ss:Name="'.$name.'">
		<ss:Names>
			<ss:NamedRange ss:Name="Print_Titles" ss:RefersTo="=\' '.$name.' \'!R1:R2" />
		</ss:Names>
		<ss:Table x:FullRows="1" x:FullColumns="1" ss:ExpandedColumnCount="8" ss:ExpandedRowCount="9">
			<ss:Column ss:AutoFitWidth="1" ss:Width="100" />
			<ss:Column ss:AutoFitWidth="1" ss:Width="100" />
			<ss:Column ss:AutoFitWidth="1" ss:Width="100" />
			<ss:Column ss:AutoFitWidth="1" ss:Width="100" />
			<ss:Column ss:AutoFitWidth="1" ss:Width="100" />
			<ss:Column ss:AutoFitWidth="1" ss:Width="100" />
			<ss:Column ss:AutoFitWidth="1" ss:Width="100" />
			<ss:Column ss:AutoFitWidth="1" ss:Width="100" />
			<ss:Row ss:Height="30">
				<ss:Cell ss:StyleID="title" ss:MergeAcross="3">
					<ss:Data xmlns:html="http://www.w3.org/TR/REC-html40" ss:Type="String">
						<html:B>
							<html:Font html:Size="14">'.$name.'</html:Font>
						</html:B>
					</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles" />
				</ss:Cell>
			</ss:Row>
			<ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">პასუხი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles" />
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">რაოდენობა</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles" />
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">დელტა</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles" />
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">%</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles" />
				</ss:Cell>	
			</ss:Row>
		
'; 
$data .= $dat; 
  
		
$data .='</ss:Table>
		<x:WorksheetOptions>
			<x:PageSetup>
				<x:Layout x:CenterHorizontal="1" x:Orientation="Portrait" />
				<x:Header x:Data="&amp;R&#10;&#10;&amp;D" />
				<x:Footer x:Data="Page &amp;P of &amp;N" x:Margin="0.5" />
				<x:PageMargins x:Top="0.5" x:Right="0.5" x:Left="0.5" x:Bottom="0.8" />
			</x:PageSetup>
			<x:FitToPage />
			<x:Print>
				<x:PrintErrors>Blank</x:PrintErrors>
				<x:FitWidth>1</x:FitWidth>
				<x:FitHeight>32767</x:FitHeight>
				<x:ValidPrinterInfo />
				<x:VerticalResolution>600</x:VerticalResolution>
			</x:Print>
			<x:Selected />
			<x:DoNotDisplayGridlines />
			<x:ProtectObjects>False</x:ProtectObjects>
			<x:ProtectScenarios>False</x:ProtectScenarios>
		</x:WorksheetOptions>
	</ss:Worksheet>
</ss:Workbook>
		';

if($number == '2'){
	$null= 1;
	echo json_encode($null);
}else{
	echo json_encode($data);
}
file_put_contents('excel.xls', $data);



	