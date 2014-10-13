<head>
	<style type="text/css">
		caption{
		    margin: 0;
			padding: 0;
			background: #f3f3f3;
			height: 40px;
			line-height: 40px;
			text-indent: 2px;
			font-family: "Trebuchet MS", Trebuchet, Arial, sans-serif;
			font-size: 140%;
			font-weight: bold;
			color: #000;
			text-align: left;
			letter-spacing: 1px;
			border-top: dashed 1px #c2c2c2;
			border-bottom: dashed 1px #c2c2c2;
		}
		div, caption, td, th, h2, h3, h4 {
			font-size: 12px;
			font-family: verdana,sans-serif;
			voice-family: "\"}\"";
			voice-family: inherit;
			color: #333;
		}
		tbody {
			display: table-row-group;
			vertical-align: middle;
			border-color: inherit;
		}
		tbody tr {
			background: #dfedf3;
			font-size: 110%;
		}
		tr {
			display: table-row;
			vertical-align: inherit;
			border-color: inherit;
		}
		tbody tr th, tbody tr td {
			padding: 5px;
			border: solid 1px #326e87;
			text-align: center;
			vertical-align:middle;
		}
		thead tr th {
			height: 32px;
			aline-height: 32px;
			text-align: center;
			vertical-align:middle;
			color: #1c5d79;
			background: #CBDFEE;
			border-left: solid 1px #FF9900;
			border-right: solid 1px #FF9900;
			border-collapse: collapse;
		}
		table.sortable a.sortheader {
			text-decoration: none;
			display: block;
			color: #1c5d79;
			xcolor: #000000;
			font-weight: bold;
		}
		a{
			cursor: pointer;
		}
		.tdstyle{
			text-align: left;
			vertical-align:middle;
		}
    </style>
    <script src="js/highcharts.js"></script>
     <script src="js/exporting.js"></script>
	<script type="text/javascript">
		var aJaxURL		= "server-side/report/technical.action.php";		//server side folder url
		var aJaxURL1	= "server-side/report/sales_statistics.action.php";		//server side folder url
		var tName		= "example0";										//table name
		var tbName		= "tabs";											//tabs name
		var fName		= "add-edit-form";									//form name
		var file_name 	= '';
		var rand_file 	= '';
		
		$(document).ready(function () {   
			GetTabs(tbName);   	
			GetDate("start_time");
			GetDate("end_time");
			$("#show_report").button({
	            
		    });
			$("#excel_tech_info").button({
	            
		    });
			$(".excel_report_info").button({
	            
		    });
			$("#excel_answer_call_info").button({
	            
		    });
			$("#excel_answer_call_by_agent_info").button({
	            
		    });
			$("#excel_answer_service_level_info").button({
	            
		    });
			$("#excel_answer_call_by_queue_info").button({
	            
		    });
			$("#excel_disconnection_cause_info").button({
	            
		    });
			$("#excel_unanswer_call_info").button({
	            
		    });
			$("#excel_disconnection_cause_unanswer_info").button({
	            
		    });
			$("#excel_unanswer_call_by_queue_info").button({
	            
		    });
			$("#excel_unanswer_call_by_queue_info").button({
	            
		    });
			$("#excel_total_info").button({
	            
		    });
			$("#excel_answer_call_by_day_info").button({
	            
		    });
			$("#excel_answer_call_by_time_info").button({
	            
		    });
			$("#excel_answer_call_by_weak_info").button({
	            
		    });
			
			
		});

		$(document).on("tabsactivate", "#tabs", function() {
        	var tab = GetSelectedTab(tbName);
        	if (tab == 0) {
        		drawFirstLevel();
        		drawFirstLevel1();
        		$(this).css('height','700px');
        	}else if(tab == 1){
        		getData();
       			getData11();
       			getData7();
       			getData1();
       			$(this).css('height','1600px');
            }else if(tab == 2){
            	getData5();
            	getData6();
            	$(this).css('height','800px');
            }else if(tab == 3){
            	getData4();
            	getData8();
            	getData2();
            	getData9();
            	getData3();
            	getData10();
            	$(this).css('height','1700px');
            }
        });

		function getData(){
			 var options = {
			        chart: {
			            renderTo: 'chart_container',
			            margin: [ 50, 50, 100, 80]
			        },
			        title: {
			            text: 'ნაპასუხები ზარები ოპერატორების მიხედვით',
			            x: -20 
			        },
			       
			        xAxis: {
			            categories: [],
			            labels: {
				            
			            	align: 'center'
			            }
			        },
			        yAxis: {
			            title: {
			                text: 'ზარები'
			            },
			            plotLines: [{
			                value: 0,
			                width: 1,
			                color: '#808080'
			            }]
			        },
			        tooltip: {
			        	//valueSuffix: ' áƒªáƒ�áƒšáƒ˜'
			                
			        },
			        legend: {
		                layout: 'vertical',
		                align: 'left',
		                verticalAlign: 'top',
		                borderWidth: 0
			        },
			        series: []
			    }

			 var i=0;
			
				agent	= '';
				queuet = '';
			
				var optionss = $('#myform_List_Queue_to option');
				var values = $.map(optionss ,function(option) {
					if(queuet != ""){
						queuet+=",";
						
					}
					queuet+="'"+option.value+"'";
				});
			
			var optionss = $('#myform_List_Agent_to option');
			var values = $.map(optionss ,function(option) {
				if(agent != ''){
					agent+=',';
					
				}
				agent+="'"+option.value+"'";
			});
			
			start_time = $('#start_time').val();
			end_time = $('#end_time').val();

			    $.getJSON("server-side/report/sales_statistics.action.php?start="+start_time + "&end=" + end_time + "&agent=" + agent + "&queuet=" + queuet, function(json) {
				    
			    	options.xAxis.categories = json[1]['agent'];
			    	options.tooltip.valueSuffix = json[1]['unit'];
			    	options.series[0] = {};
			    	options.series[0].name = json[1]['name'];
			    	options.series[0].data = json[1]['call_count'];
			    	options.series[0].type = "column";
			    	
			        chart = new Highcharts.Chart(options);
			        
			    });
		}
		function getData1(){
			 var options = {
			        chart: {
			            renderTo: 'chart_container1',
			            margin: [ 50, 50, 100, 80]
			        },
			        title: {
			            text: 'კავშირის გაწყვეტის მიზეზეი',
			            x: -20 
			        },
			        subtitle: {
			            text: '',
			            x: -20
			        },
			        xAxis: {
			            categories: [],
			            labels: {
			            	align: 'center'
			            }
			        },
			        yAxis: {
			            title: {
			                text: 'ზარები'
			            },
			            plotLines: [{
			                value: 0,
			                width: 1,
			                color: '#808080'
			            }]
			        },
			        tooltip: {
			        	//valueSuffix: ' áƒªáƒ�áƒšáƒ˜'
			                
			        },
			        legend: {
		                layout: 'vertical',
		                align: 'left',
		                verticalAlign: 'top',
		                borderWidth: 0
			        },
			        series: []
			    }

			 var i=0;
			
				agent	= '';
				queuet = '';
			
				var optionss = $('#myform_List_Queue_to option');
				var values = $.map(optionss ,function(option) {
					if(queuet != ""){
						queuet+=",";
						
					}
					queuet+="'"+option.value+"'";
				});
			
			var optionss = $('#myform_List_Agent_to option');
			var values = $.map(optionss ,function(option) {
				if(agent != ''){
					agent+=',';
					
				}
				agent+="'"+option.value+"'";
			});
			
			start_time = $('#start_time').val();
			end_time = $('#end_time').val();

			    $.getJSON("server-side/report/sales_statistics.action.php?start="+start_time + "&end=" + end_time + "&agent=" + agent + "&queuet=" + queuet, function(json) {
				    
			    	options.xAxis.categories = json[0]['cause'];
			    	options.tooltip.valueSuffix = json[0]['unit'];
			    	options.series[0] = {};
			    	options.series[0].name = json[0]['name'];
			    	options.series[0].data = json[0]['quantity'];
			    	options.series[0].type = "column";
			    	
			        chart = new Highcharts.Chart(options);
			        
			    });
		}
		function getData2(){
			 var options = {
			        chart: {
			            renderTo: 'chart_container2',
			            margin: [ 50, 50, 100, 80]
			        },
			        title: {
			            text: 'ნაპასუხები ზარები საათების მიხედვით',
			            x: -20 
			        },
			        subtitle: {
			            text: '',
			            x: -20
			        },
			        xAxis: {
			            categories: [],
			            labels: {
			            	align: 'center'
			            }
			        },
			        yAxis: {
			            title: {
			                text: 'ზარები'
			            },
			            plotLines: [{
			                value: 0,
			                width: 1,
			                color: '#808080'
			            }]
			        },
			        tooltip: {
			        	//valueSuffix: ' áƒªáƒ�áƒšáƒ˜'
			                
			        },
			        legend: {
		                layout: 'vertical',
		                align: 'left',
		                verticalAlign: 'top',
		                borderWidth: 0
			        },
			        series: []
			    }

			 var i=0;
			
				agent	= '';
				queuet = '';
			
				var optionss = $('#myform_List_Queue_to option');
				var values = $.map(optionss ,function(option) {
					if(queuet != ""){
						queuet+=",";
						
					}
					queuet+="'"+option.value+"'";
				});
			
			var optionss = $('#myform_List_Agent_to option');
			var values = $.map(optionss ,function(option) {
				if(agent != ''){
					agent+=',';
					
				}
				agent+="'"+option.value+"'";
			});
			
			start_time = $('#start_time').val();
			end_time = $('#end_time').val();

			    $.getJSON("server-side/report/sales_statistics.action.php?start="+start_time + "&end=" + end_time + "&agent=" + agent + "&queuet=" + queuet, function(json) {
				    
			    	options.xAxis.categories = json[2]['datetime'];
			    	options.tooltip.valueSuffix = json[2]['unit'];
			    	options.series[0] = {};
			    	options.series[0].name = json[2]['name'];
			    	options.series[0].data = json[2]['answer_count'];
			    	options.series[0].type = "column";
			    	
			        chart = new Highcharts.Chart(options);
			        
			    });
		}
		function getData3(){
			 var options = {
			        chart: {
			            renderTo: 'chart_container3',
			            margin: [ 50, 50, 100, 80]
			        },
			        title: {
			            text: 'ნაპასუხები ზარები კვირის დღეების მიხედვით',
			            x: -20 
			        },
			        subtitle: {
			            text: '',
			            x: -20
			        },
			        xAxis: {
			            categories: [],
			            labels: {
			            	align: 'center'
			            }
			        },
			        yAxis: {
			            title: {
			                text: 'ზარები'
			            },
			            plotLines: [{
			                value: 0,
			                width: 1,
			                color: '#808080'
			            }]
			        },
			        tooltip: {
			        	//valueSuffix: ' áƒªáƒ�áƒšáƒ˜'
			                
			        },
			        legend: {
		                layout: 'vertical',
		                align: 'left',
		                verticalAlign: 'top',
		                borderWidth: 0
			        },
			        series: []
			    }

			 var i=0;
			
				agent	= '';
				queuet = '';
			
				var optionss = $('#myform_List_Queue_to option');
				var values = $.map(optionss ,function(option) {
					if(queuet != ""){
						queuet+=",";
						
					}
					queuet+="'"+option.value+"'";
				});
			
			var optionss = $('#myform_List_Agent_to option');
			var values = $.map(optionss ,function(option) {
				if(agent != ''){
					agent+=',';
					
				}
				agent+="'"+option.value+"'";
			});
			
			start_time = $('#start_time').val();
			end_time = $('#end_time').val();

			    $.getJSON("server-side/report/sales_statistics.action.php?start="+start_time + "&end=" + end_time + "&agent=" + agent + "&queuet=" + queuet, function(json) {
				    
			    	options.xAxis.categories = json[3]['datetime1'];
			    	options.tooltip.valueSuffix = json[3]['unit'];
			    	options.series[0] = {};
			    	options.series[0].name = json[3]['name'];
			    	options.series[0].data = json[3]['answer_count1'];
			    	options.series[0].type = "column";
			    	
			        chart = new Highcharts.Chart(options);
			        
			    });
		}




		function getData4(){
			 var options = {
			        chart: {
			            renderTo: 'chart_container4',
			            margin: [ 50, 50, 100, 80]
			        },
			        title: {
			            text: 'ნაპასუხები ზარები დღეების მიხედვით',
			            x: -20 
			        },
			        subtitle: {
			            text: '',
			            x: -20
			        },
			        xAxis: {
			            categories: [],
			            labels: {
			            	 rotation: -45,
			            	align: 'right'
			            }
			        },
			        yAxis: {
			            title: {
			                text: 'ზარები'
			            },
			            plotLines: [{
			                value: 0,
			                width: 1,
			                color: '#808080'
			            }]
			        },
			        tooltip: {
			        	//valueSuffix: ' áƒªáƒ�áƒšáƒ˜'
			                
			        },
			        legend: {
		                layout: 'vertical',
		                align: 'left',
		                verticalAlign: 'top',
		                borderWidth: 0
			        },
			        series: []
			    }

			 var i=0;
			
				agent	= '';
				queuet = '';
			
				var optionss = $('#myform_List_Queue_to option');
				var values = $.map(optionss ,function(option) {
					if(queuet != ""){
						queuet+=",";
						
					}
					queuet+="'"+option.value+"'";
				});
			
			var optionss = $('#myform_List_Agent_to option');
			var values = $.map(optionss ,function(option) {
				if(agent != ''){
					agent+=',';
					
				}
				agent+="'"+option.value+"'";
			});
			
			start_time = $('#start_time').val();
			end_time = $('#end_time').val();

			    $.getJSON("server-side/report/sales_statistics.action.php?start="+start_time + "&end=" + end_time + "&agent=" + agent + "&queuet=" + queuet, function(json) {
				    
			    	options.xAxis.categories = json[4]['datetime2'];
			    	options.tooltip.valueSuffix = json[4]['unit'];
			    	options.series[0] = {};
			    	options.series[0].name = json[4]['name'];
			    	options.series[0].data = json[4]['answer_count2'];
			    	options.series[0].type = "column";
			    	
			        chart = new Highcharts.Chart(options);
			        
			    });
		}

		function getData5(){
			 var options = {
			        chart: {
			            renderTo: 'chart_container5',
			            margin: [ 50, 50, 100, 80]
			        },
			        title: {
			            text: 'კავშირის გაწყვეტის მიზეზი',
			            x: -20 
			        },
			        subtitle: {
			            text: '',
			            x: -20
			        },
			        xAxis: {
			            categories: [],
			            labels: {
			            	 rotation: -45,
			            	align: 'right'
			            }
			        },
			        yAxis: {
			            title: {
			                text: 'ზარები'
			            },
			            plotLines: [{
			                value: 0,
			                width: 1,
			                color: '#808080'
			            }]
			        },
			        tooltip: {
			        	//valueSuffix: ' áƒªáƒ�áƒšáƒ˜'
			                
			        },
			        legend: {
		                layout: 'vertical',
		                align: 'left',
		                verticalAlign: 'top',
		                borderWidth: 0
			        },
			        series: []
			    }

			 var i=0;
			
				agent	= '';
				queuet = '';
			
				var optionss = $('#myform_List_Queue_to option');
				var values = $.map(optionss ,function(option) {
					if(queuet != ""){
						queuet+=",";
						
					}
					queuet+="'"+option.value+"'";
				});
			
			var optionss = $('#myform_List_Agent_to option');
			var values = $.map(optionss ,function(option) {
				if(agent != ''){
					agent+=',';
					
				}
				agent+="'"+option.value+"'";
			});
			
			start_time = $('#start_time').val();
			end_time = $('#end_time').val();

			    $.getJSON("server-side/report/sales_statistics.action.php?start="+start_time + "&end=" + end_time + "&agent=" + agent + "&queuet=" + queuet, function(json) {
				    
			    	options.xAxis.categories = json[5]['cause1'];
			    	options.tooltip.valueSuffix = json[5]['unit'];
			    	options.series[0] = {};
			    	options.series[0].name = json[5]['name'];
			    	options.series[0].data = json[5]['answer_count3'];
			    	options.series[0].type = "column";
			    	
			        chart = new Highcharts.Chart(options);
			        
			    });
		}

		function getData6(){
			 var options = {
			        chart: {
			            renderTo: 'chart_container6',
			            margin: [ 50, 50, 100, 80]
			        },
			        title: {
			            text: 'უპასუხო ზარები რიგის მიხედვით',
			            x: -20 
			        },
			        subtitle: {
			            text: '',
			            x: -20
			        },
			        xAxis: {
			            categories: [],
			            labels: {
			            	 rotation: -45,
			            	align: 'right'
			            }
			        },
			        yAxis: {
			            title: {
			                text: 'ზარები'
			            },
			            plotLines: [{
			                value: 0,
			                width: 1,
			                color: '#808080'
			            }]
			        },
			        tooltip: {
			        	//valueSuffix: ' áƒªáƒ�áƒšáƒ˜'
			                
			        },
			        legend: {
		                layout: 'vertical',
		                align: 'left',
		                verticalAlign: 'top',
		                borderWidth: 0
			        },
			        series: []
			    }

			 var i=0;
			
				agent	= '';
				queuet = '';
			
				var optionss = $('#myform_List_Queue_to option');
				var values = $.map(optionss ,function(option) {
					if(queuet != ""){
						queuet+=",";
						
					}
					queuet+="'"+option.value+"'";
				});
			
			var optionss = $('#myform_List_Agent_to option');
			var values = $.map(optionss ,function(option) {
				if(agent != ''){
					agent+=',';
					
				}
				agent+="'"+option.value+"'";
			});
			
			start_time = $('#start_time').val();
			end_time = $('#end_time').val();

			    $.getJSON("server-side/report/sales_statistics.action.php?start="+start_time + "&end=" + end_time + "&agent=" + agent + "&queuet=" + queuet, function(json) {
				    
			    	options.xAxis.categories = json[6]['queue1'];
			    	options.tooltip.valueSuffix = json[6]['unit'];
			    	options.series[0] = {};
			    	options.series[0].name = json[6]['name'];
			    	options.series[0].data = json[6]['count1'];
			    	options.series[0].type = "column";
			    	
			        chart = new Highcharts.Chart(options);
			        
			    });
		}

		function getData7(){
			 var options = {
			        chart: {
			            renderTo: 'chart_container7',
			            margin: [ 50, 50, 100, 80]
			        },
			        title: {
			            text: 'ნაპასუხები ზარები რიგის მიხედვით',
			            x: -20 
			        },
			        subtitle: {
			            text: '',
			            x: -20
			        },
			        xAxis: {
			            categories: [],
			            labels: {
			            	 rotation: -45,
			            	align: 'right'
			            }
			        },
			        yAxis: {
			            title: {
			                text: 'ზარები'
			            },
			            plotLines: [{
			                value: 0,
			                width: 1,
			                color: '#808080'
			            }]
			        },
			        tooltip: {
			        	//valueSuffix: ' áƒªáƒ�áƒšáƒ˜'
			                
			        },
			        legend: {
		                layout: 'vertical',
		                align: 'left',
		                verticalAlign: 'top',
		                borderWidth: 0
			        },
			        series: []
			    }

			 var i=0;
			
				agent	= '';
				queuet = '';
			
				var optionss = $('#myform_List_Queue_to option');
				var values = $.map(optionss ,function(option) {
					if(queuet != ""){
						queuet+=",";
						
					}
					queuet+="'"+option.value+"'";
				});
			
			var optionss = $('#myform_List_Agent_to option');
			var values = $.map(optionss ,function(option) {
				if(agent != ''){
					agent+=',';
					
				}
				agent+="'"+option.value+"'";
			});
			
			start_time = $('#start_time').val();
			end_time = $('#end_time').val();

			    $.getJSON("server-side/report/sales_statistics.action.php?start="+start_time + "&end=" + end_time + "&agent=" + agent + "&queuet=" + queuet, function(json) {
				    
			    	options.xAxis.categories = json[7]['queue2'];
			    	options.tooltip.valueSuffix = json[7]['unit'];
			    	options.series[0] = {};
			    	options.series[0].name = json[7]['name'];
			    	options.series[0].data = json[7]['count2'];
			    	options.series[0].type = "column";
			    	
			        chart = new Highcharts.Chart(options);
			        
			    });
		}


		 function getData8(){
			 var options = {
			        chart: {
			            renderTo: 'chart_container8',
			            margin: [ 50, 50, 100, 80]
			        },
			        title: {
			            text: 'უპასუხო ზარები დღეების მიხედვით',
			            x: -20 
			        },
			        subtitle: {
			            text: '',
			            x: -20
			        },
			        xAxis: {
			            categories: [],
			            labels: {
			            	 rotation: -45,
			            	align: 'right'
			            }
			        },
			        yAxis: {
			            title: {
			                text: 'ზარები'
			            },
			            plotLines: [{
			                value: 0,
			                width: 1,
			                color: '#808080'
			            }]
			        },
			        tooltip: {
			        	//valueSuffix: ' áƒªáƒ�áƒšáƒ˜'
			                
			        },
			        legend: {
		                layout: 'vertical',
		                align: 'left',
		                verticalAlign: 'top',
		                borderWidth: 0
			        },
			        series: []
			    }

			 var i=0;
			
				agent	= '';
				queuet = '';
			
				var optionss = $('#myform_List_Queue_to option');
				var values = $.map(optionss ,function(option) {
					if(queuet != ""){
						queuet+=",";
						
					}
					queuet+="'"+option.value+"'";
				});
			
			var optionss = $('#myform_List_Agent_to option');
			var values = $.map(optionss ,function(option) {
				if(agent != ''){
					agent+=',';
					
				}
				agent+="'"+option.value+"'";
			});
			
			start_time = $('#start_time').val();
			end_time = $('#end_time').val();

			    $.getJSON("server-side/report/sales_statistics.action.php?start="+start_time + "&end=" + end_time + "&agent=" + agent + "&queuet=" + queuet, function(json) {
				    
			    	options.xAxis.categories = json[8]['times'];
			    	options.tooltip.valueSuffix = json[8]['unit'];
			    	options.series[0] = {};
			    	options.series[0].name = json[8]['name'];
			    	options.series[0].data = json[8]['unanswer_call'];
			    	options.series[0].type = "column";
			    	
			        chart = new Highcharts.Chart(options);
			        
			    });
		}



		 function getData9(){
			 var options = {
			        chart: {
			            renderTo: 'chart_container9',
			            margin: [ 50, 50, 100, 80]
			        },
			        title: {
			            text: 'უპასუხო ზარები საათების  მიხედვით',
			            x: -20 
			        },
			        subtitle: {
			            text: '',
			            x: -20
			        },
			        xAxis: {
			            categories: [],
			            labels: {
			            	 rotation: -45,
			            	align: 'right'
			            }
			        },
			        yAxis: {
			            title: {
			                text: 'ზარები'
			            },
			            plotLines: [{
			                value: 0,
			                width: 1,
			                color: '#808080'
			            }]
			        },
			        tooltip: {
			        	//valueSuffix: ' áƒªáƒ�áƒšáƒ˜'
			                
			        },
			        legend: {
		                layout: 'vertical',
		                align: 'left',
		                verticalAlign: 'top',
		                borderWidth: 0
			        },
			        series: []
			    }

			 var i=0;
			
				agent	= '';
				queuet = '';
			
				var optionss = $('#myform_List_Queue_to option');
				var values = $.map(optionss ,function(option) {
					if(queuet != ""){
						queuet+=",";
						
					}
					queuet+="'"+option.value+"'";
				});
			
			var optionss = $('#myform_List_Agent_to option');
			var values = $.map(optionss ,function(option) {
				if(agent != ''){
					agent+=',';
					
				}
				agent+="'"+option.value+"'";
			});
			
			start_time = $('#start_time').val();
			end_time = $('#end_time').val();

			    $.getJSON("server-side/report/sales_statistics.action.php?start="+start_time + "&end=" + end_time + "&agent=" + agent + "&queuet=" + queuet, function(json) {
				    
			    	options.xAxis.categories = json[9]['times2'];
			    	options.tooltip.valueSuffix = json[9]['unit'];
			    	options.series[0] = {};
			    	options.series[0].name = json[9]['name'];
			    	options.series[0].data = json[9]['unanswer_count1'];
			    	options.series[0].type = "column";
			    	
			        chart = new Highcharts.Chart(options);
			        
			    });
		}

		 function getData10(){
			 var options = {
			        chart: {
			            renderTo: 'chart_container10',
			            margin: [ 50, 50, 100, 80]
			        },
			        title: {
			            text: 'უპასუხო ზარები კვირის დღეების მიხედვით',
			            x: -20 
			        },
			        subtitle: {
			            text: '',
			            x: -20
			        },
			        xAxis: {
			            categories: [],
			            labels: {
			            	 rotation: -45,
			            	align: 'right'
			            }
			        },
			        yAxis: {
			            title: {
			                text: 'ზარები'
			            },
			            plotLines: [{
			                value: 0,
			                width: 1,
			                color: '#808080'
			            }]
			        },
			        tooltip: {
			        	//valueSuffix: ' áƒªáƒ�áƒšáƒ˜'
			                
			        },
			        legend: {
		                layout: 'vertical',
		                align: 'left',
		                verticalAlign: 'top',
		                borderWidth: 0
			        },
			        series: []
			    }

			 var i=0;
			
				agent	= '';
				queuet = '';
			
				var optionss = $('#myform_List_Queue_to option');
				var values = $.map(optionss ,function(option) {
					if(queuet != ""){
						queuet+=",";
						
					}
					queuet+="'"+option.value+"'";
				});
			
			var optionss = $('#myform_List_Agent_to option');
			var values = $.map(optionss ,function(option) {
				if(agent != ''){
					agent+=',';
					
				}
				agent+="'"+option.value+"'";
			});
			
			start_time = $('#start_time').val();
			end_time = $('#end_time').val();

			    $.getJSON("server-side/report/sales_statistics.action.php?start="+start_time + "&end=" + end_time + "&agent=" + agent + "&queuet=" + queuet, function(json) {
				    
			    	options.xAxis.categories = json[10]['date1'];
			    	options.tooltip.valueSuffix = json[10]['unit'];
			    	options.series[0] = {};
			    	options.series[0].name = json[10]['name'];
			    	options.series[0].data = json[10]['unanswer_count2'];
			    	options.series[0].type = "column";
			    	
			        chart = new Highcharts.Chart(options);
			        
			    });
			    
		}

		 function drawFirstLevel(){
			    var options = {
			                  chart: {
			                      renderTo: 'chart_container0',
			                      plotBackgroundColor: null,
			                      plotBorderWidth: null,
			                      plotShadow: '#FA3A3A'
			                  },
			                  title: {
			                      text: 'ტექნიკური ინფორმაცია'
			                  },
			                  tooltip: {
			                      formatter: function() {
			                          return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
			                      }
			                  },
			                  plotOptions: {
			                   pie: {
			                          allowPointSelect: true,
			                          cursor: 'pointer',
			                          dataLabels: {
			                              enabled: true,
			                              color: '#000000',
			                              connectorColor: '#FA3A3A',
			                              formatter: function() {
			                                  return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
			                              }
			                          },
			                          point: {
			                              events: {
			                                  click: function() {                   
			                                  }
			                              }
			                          }
			                      }
			                  },
			                  series: [{
			                      type: 'pie',
			                      name: 'კატეგორიები',
			                     // color: '#FA3A3A',
			                      data: []
			                  }]
			              }
			    var i=0;
			    
			    agent = '';
			    queuet = '';
			   
			    var optionss = $('#myform_List_Queue_to option');
			    var values = $.map(optionss ,function(option) {
			     if(queuet != ""){
			      queuet+=",";
			      
			     }
			     queuet+="'"+option.value+"'";
			    });
			   
			   var optionss = $('#myform_List_Agent_to option');
			   var values = $.map(optionss ,function(option) {
			    if(agent != ''){
			     agent+=',';
			     
			    }
			    agent+="'"+option.value+"'";
			   });
			   
			   start_time = $('#start_time').val();
			   end_time = $('#end_time').val();
			              $.getJSON("server-side/report/prod_category_statistics.action.php?start="+start_time + "&end=" + end_time + "&agent=" + agent + "&queuet=" + queuet, function(json) {
			                  options.series[0].data = json;
			                  chart = new Highcharts.Chart(options);
			              });
			   }
		 function drawFirstLevel1(){
			    var options = {
			                  chart: {
			                      renderTo: 'chart_container0r',
			                      plotBackgroundColor: null,
			                      plotBorderWidth: null,
			                      plotShadow: '#FA3A3A'
			                  },
			                  title: {
			                      text: 'ტექნიკური ინფორმაცია'
			                  },
			                  tooltip: {
			                      formatter: function() {
			                          return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
			                      }
			                  },
			                  plotOptions: {
			                   pie: {
			                          allowPointSelect: true,
			                          cursor: 'pointer',
			                          dataLabels: {
			                              enabled: true,
			                              color: '#000000',
			                              connectorColor: '#FA3A3A',
			                              formatter: function() {
			                                  return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
			                              }
			                          },
			                          point: {
			                              events: {
			                                  click: function() {                   
			                                  }
			                              }
			                          }
			                      }
			                  },
			                  series: [{
			                      type: 'pie',
			                      name: 'კატეგორიები',
			                     // color: '#FA3A3A',
			                      data: []
			                  }]
			              }
			    var i=0;
			    
			    agent = '';
			    queuet = '';
			   
			    var optionss = $('#myform_List_Queue_to option');
			    var values = $.map(optionss ,function(option) {
			     if(queuet != ""){
			      queuet+=",";
			      
			     }
			     queuet+="'"+option.value+"'";
			    });
			   
			   var optionss = $('#myform_List_Agent_to option');
			   var values = $.map(optionss ,function(option) {
			    if(agent != ''){
			     agent+=',';
			     
			    }
			    agent+="'"+option.value+"'";
			   });
			   
			   start_time = $('#start_time').val();
			   end_time = $('#end_time').val();
			              $.getJSON("server-side/report/prod_category_statistics1.action.php?start="+start_time + "&end=" + end_time + "&agent=" + agent + "&queuet=" + queuet, function(json) {
			                  options.series[0].data = json;
			                  chart = new Highcharts.Chart(options);
			              });
			   }
		 
		 function getData11(){
			 var options = {
			        chart: {
			            renderTo: 'chart_container11',
			            margin: [ 50, 50, 100, 80]
			        },
			        title: {
			            text: 'ნაპასუხები ზარები წამების მიხედვით',
			            x: -20 
			        },
			        subtitle: {
			            text: '',
			            x: -20
			        },
			        xAxis: {
			            categories: [],
			            labels: {
			            	 rotation: -45,
			            	align: 'right'
			            }
			        },
			        yAxis: {
			            title: {
			                text: 'ზარები'
			            },
			            plotLines: [{
			                value: 0,
			                width: 1,
			                color: '#808080'
			            }]
			        },
			        tooltip: {
			        	//valueSuffix: ' áƒªáƒ�áƒšáƒ˜'
			                
			        },
			        legend: {
		                layout: 'vertical',
		                align: 'left',
		                verticalAlign: 'top',
		                borderWidth: 0
			        },
			        series: []
			    }

			 var i=0;
			
				agent	= '';
				queuet = '';
			
				var optionss = $('#myform_List_Queue_to option');
				var values = $.map(optionss ,function(option) {
					if(queuet != ""){
						queuet+=",";
						
					}
					queuet+="'"+option.value+"'";
				});
			
			var optionss = $('#myform_List_Agent_to option');
			var values = $.map(optionss ,function(option) {
				if(agent != ''){
					agent+=',';
					
				}
				agent+="'"+option.value+"'";
			});
			
			start_time = $('#start_time').val();
			end_time = $('#end_time').val();

			    $.getJSON("server-side/report/sales_statistics.action.php?start="+start_time + "&end=" + end_time + "&agent=" + agent + "&queuet=" + queuet, function(json) {
				    
			    	options.xAxis.categories = json[11]['call_second'];
			    	options.tooltip.valueSuffix = json[11]['unit'];
			    	options.series[0] = {};
			    	options.series[0].name = json[11]['name'];
			    	options.series[0].data = json[11]['mas'];
			    	options.series[0].type = "column";
			    	
			        chart = new Highcharts.Chart(options);
			        
			    });
			    
		}
      

		function go_next(val,par){
			if(val != undefined){
				$("#myform_List_"+par+"_from option:selected").remove();
				$("#myform_List_"+par+"_to").append(new Option(val, val));
			}
		}

		function go_previous(val,par){
			if(val != undefined){
				$("#myform_List_"+par+"_to option:selected").remove();
				$("#myform_List_"+par+"_from").append(new Option(val, val));
			}
		}

		function go_last(par){
			var options = $('#myform_List_'+par+'_from option');
			$("#myform_List_"+par+"_from option").remove();
			var values = $.map(options ,function(option) {
			    $("#myform_List_"+par+"_to").append(new Option(option.value, option.value));
			});
		}

		function go_first(par){
			var options = $('#myform_List_'+par+'_to option');
			$("#myform_List_"+par+"_to option").remove();
			var values = $.map(options ,function(option) {
			    $("#myform_List_"+par+"_from").append(new Option(option.value, option.value));
			});
		}

		$(document).on("click", "#show_report", function () {
			var i=0;
			paramq 			= new Object();
			parama 			= new Object();
			parame 			= new Object();
			parame.agent	= '';
			parame.queuet = '';
			paramm		= "server-side/report/technical.action.php";
			
			drawFirstLevel();
			drawFirstLevel1();
			
			var options = $('#myform_List_Queue_to option');
			var values = $.map(options ,function(option) {
				if(parame.queuet != ""){
					parame.queuet+=",";
					
				}
				parame.queuet+="'"+option.value+"'";
			});

			
			var options = $('#myform_List_Agent_to option');
			var values = $.map(options ,function(option) {
				if(parame.agent != ''){
					parame.agent+=',';
					
				}
				parame.agent+="'"+option.value+"'";
			});
			
			parame.start_time = $('#start_time').val();
			parame.end_time = $('#end_time').val();
			parame.act = 'check';
			if(parame.queuet==''){
				alert('აირჩიე რიგი');
			}else if(parame.agent==''){
				alert('აირჩიე ოპერატორი');
			}else{
				$.ajax({
			        url: paramm,
				    data: parame,
			        success: function(data) {		        	
						$("#answer_call").html(data.page.answer_call);
						$("#technik_info").html(data.page.technik_info);
						$(".report_info").html(data.page.report_info);
						$("#answer_call_info").html(data.page.answer_call_info);
						$("#answer_call_by_queue").html(data.page.answer_call_by_queue);
						$("#disconnection_cause").html(data.page.disconnection_cause);
						$("#unanswer_call").html(data.page.unanswer_call);
						$("#disconnection_cause_unanswer").html(data.page.disconnection_cause_unanswer);
						$("#unanswered_calls_by_queue").html(data.page.unanswered_calls_by_queue);
						$("#totals").html(data.page.totals);
						$("#call_distribution_per_day").html(data.page.call_distribution_per_day);
						$("#call_distribution_per_hour").html(data.page.call_distribution_per_hour);
						$("#call_distribution_per_day_of_week").html(data.page.call_distribution_per_day_of_week);
						$("#service_level").html(data.page.service_level);
				    }
			    });
			}
        });

		$(document).on("click", "#answear_dialog", function () {
			LoadDialog();
		});
		
		$(document).on("click", "#unanswear_dialog", function () {
			LoadDialog1();
		});
		
		var record;
		function play(record){
			
			link = 'http://109.234.117.182:8181/records/' + record;
			var newWin = window.open(link, 'newWin','width=420,height=200');
            newWin.focus();
            
		}
		
		function LoadDialog(){
			parame 				= new Object();
			paramm		= "server-side/report/technical.action.php";
			parame.start_time 	= $('#start_time').val();
			parame.end_time 	= $('#end_time').val();
			parame.act 			= 'answear_dialog';
			parame.agent	= '';
			parame.queuet = '';
			
			
			var options = $('#myform_List_Queue_to option');
			var values = $.map(options ,function(option) {
				if(parame.queuet != ""){
					parame.queuet+=",";
					
				}
				parame.queuet+="'"+option.value+"'";
			});

			
			var options = $('#myform_List_Agent_to option');
			var values = $.map(options ,function(option) {
				if(parame.agent != ''){
					parame.agent+=',';
					
				}
				parame.agent+="'"+option.value+"'";
			});
			$.ajax({
		        url: paramm,
			    data: parame,
		        success: function(data) {		        	
					$("#add-edit-form").html(data.page.answear_dialog);
					var button = {
							"cancel": {
					            text: "დახურვა",
					            id: "cancel-dialog",
					            click: function () {
					                $(this).dialog("close");
					            }
					        }
						};
					GetDialog("add-edit-form", 700, "auto", button);
					/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
					GetDataTable("example", aJaxURL, "answear_dialog_table&start_time="+parame.start_time+"&end_time="+parame.end_time+"&queuet="+parame.queuet+"&agent="+parame.agent,7, "", 0, "", 1, "desc");

			    }
		    });
		}
		
		function LoadDialog1(){
			parame 				= new Object();
			paramm		= "server-side/report/technical.action.php";
			parame.start_time 	= $('#start_time').val();
			parame.end_time 	= $('#end_time').val();
			parame.act 			= 'unanswear_dialog';
			parame.agent	= '';
			parame.queuet = '';
			
			
			var options = $('#myform_List_Queue_to option');
			var values = $.map(options ,function(option) {
				if(parame.queuet != ""){
					parame.queuet+=",";
					
				}
				parame.queuet+="'"+option.value+"'";
			});

			
			var options = $('#myform_List_Agent_to option');
			var values = $.map(options ,function(option) {
				if(parame.agent != ''){
					parame.agent+=',';
					
				}
				parame.agent+="'"+option.value+"'";
			});
			$.ajax({
		        url: paramm,
			    data: parame,
		        success: function(data) {		        	
					$("#add-edit-form-unanswer").html(data.page.answear_dialog);
					var button = {
							"cancel": {
					            text: "დახურვა",
					            id: "cancel-dialog",
					            click: function () {
					                $(this).dialog("close");
					            }
					        }
						};
					GetDialog("add-edit-form-unanswer", 500, "auto", button);
					/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
					GetDataTable("example1", aJaxURL, "unanswear_dialog_table&start_time="+parame.start_time+"&end_time="+parame.end_time+"&queuet="+parame.queuet,5, "", 0, "", 1, "desc");

			    }
		    });
		}



		 $(document).on("click", "#excel_tech_info", function () {	
			 var i=0;
				
				parame 			= new Object();
				parame.agent	= '';
				parame.queuet = '';

				var options = $('#myform_List_Queue_to option');
				var values = $.map(options ,function(option) {
					if(parame.queuet != ""){
						parame.queuet+=",";
						
					}
					parame.queuet+="'"+option.value+"'";
				});

				
				var options = $('#myform_List_Agent_to option');
				var values = $.map(options ,function(option) {
					if(parame.agent != ''){
						parame.agent+=',';
						
					}
					parame.agent+="'"+option.value+"'";
				});
				
				parame.start_time = $('#start_time').val();
				parame.end_time = $('#end_time').val();

		    	$.ajax({
	 		        url: 'server-side/report/technical/excel_tech_info.php',
	 			    data: parame,
			        success: function(data) {
				        if(data == 1){
					        alert('ჩანაწერი არ მოიძებნა');
				        }else{
			        	SaveToDisk('server-side/report/technical/excel.xls', 'excel.xls');
				        }
	 			    }
	 		    });
		    	
			});

		 $(document).on("click", ".excel_report_info", function () {	
			 var i=0;
				
				parame 			= new Object();
				parame.agent	= '';
				parame.queuet = '';

				var options = $('#myform_List_Queue_to option');
				var values = $.map(options ,function(option) {
					if(parame.queuet != ""){
						parame.queuet+=",";
						
					}
					parame.queuet+="'"+option.value+"'";
				});

				
				var options = $('#myform_List_Agent_to option');
				var values = $.map(options ,function(option) {
					if(parame.agent != ''){
						parame.agent+=',';
						
					}
					parame.agent+="'"+option.value+"'";
				});
				
				parame.start_time = $('#start_time').val();
				parame.end_time = $('#end_time').val();

		    	$.ajax({
	 		        url: 'server-side/report/technical/excel_report_info.php',
	 			    data: parame,
			        success: function(data) {
				        if(data == 1){
					        alert('ჩანაწერი არ მოიძებნა');
				        }else{
			        	SaveToDisk('server-side/report/technical/excel.xls', 'excel.xls');
				        }
	 			    }
	 		    });
		    	
			});

		 $(document).on("click", "#excel_answer_call_info", function () {	
			 var i=0;
				
				parame 			= new Object();
				parame.agent	= '';
				parame.queuet = '';

				var options = $('#myform_List_Queue_to option');
				var values = $.map(options ,function(option) {
					if(parame.queuet != ""){
						parame.queuet+=",";
						
					}
					parame.queuet+="'"+option.value+"'";
				});

				
				var options = $('#myform_List_Agent_to option');
				var values = $.map(options ,function(option) {
					if(parame.agent != ''){
						parame.agent+=',';
						
					}
					parame.agent+="'"+option.value+"'";
				});
				
				parame.start_time = $('#start_time').val();
				parame.end_time = $('#end_time').val();

		    	$.ajax({
	 		        url: 'server-side/report/technical/excel_answer_call_info.php',
	 			    data: parame,
			        success: function(data) {
				        if(data == 1){
					        alert('ჩანაწერი არ მოიძებნა');
				        }else{
			        	SaveToDisk('server-side/report/technical/excel.xls', 'excel.xls');
				        }
	 			    }
	 		    });
		    	
			});

		 $(document).on("click", "#excel_answer_call_by_agent_info", function () {	
			 var i=0;
				
				parame 			= new Object();
				parame.agent	= '';
				parame.queuet = '';

				var options = $('#myform_List_Queue_to option');
				var values = $.map(options ,function(option) {
					if(parame.queuet != ""){
						parame.queuet+=",";
						
					}
					parame.queuet+="'"+option.value+"'";
				});

				
				var options = $('#myform_List_Agent_to option');
				var values = $.map(options ,function(option) {
					if(parame.agent != ''){
						parame.agent+=',';
						
					}
					parame.agent+="'"+option.value+"'";
				});
				
				parame.start_time = $('#start_time').val();
				parame.end_time = $('#end_time').val();

		    	$.ajax({
	 		        url: 'server-side/report/technical/excel_answer_call_by_agent_info.php',
	 			    data: parame,
			        success: function(data) {
				        if(data == 1){
					        alert('ჩანაწერი არ მოიძებნა');
				        }else{
			        	SaveToDisk('server-side/report/technical/excel.xls', 'excel.xls');
				        }
	 			    }
	 		    });
		    	
			});
			
		 $(document).on("click", "#excel_answer_service_level_info", function () {	
			 var i=0;
				
				parame 			= new Object();
				parame.agent	= '';
				parame.queuet = '';

				var options = $('#myform_List_Queue_to option');
				var values = $.map(options ,function(option) {
					if(parame.queuet != ""){
						parame.queuet+=",";
						
					}
					parame.queuet+="'"+option.value+"'";
				});

				
				var options = $('#myform_List_Agent_to option');
				var values = $.map(options ,function(option) {
					if(parame.agent != ''){
						parame.agent+=',';
						
					}
					parame.agent+="'"+option.value+"'";
				});
				
				parame.start_time = $('#start_time').val();
				parame.end_time = $('#end_time').val();

		    	$.ajax({
	 		        url: 'server-side/report/technical/excel_answer_service_level_info.php',
	 			    data: parame,
			        success: function(data) {
				        if(data == 1){
					        alert('ჩანაწერი არ მოიძებნა');
				        }else{
			        	SaveToDisk('server-side/report/technical/excel.xls', 'excel.xls');
				        }
	 			    }
	 		    });
		    	
			});

		 $(document).on("click", "#excel_answer_call_by_queue_info", function () {	
			 var i=0;
				
				parame 			= new Object();
				parame.agent	= '';
				parame.queuet = '';

				var options = $('#myform_List_Queue_to option');
				var values = $.map(options ,function(option) {
					if(parame.queuet != ""){
						parame.queuet+=",";
						
					}
					parame.queuet+="'"+option.value+"'";
				});

				
				var options = $('#myform_List_Agent_to option');
				var values = $.map(options ,function(option) {
					if(parame.agent != ''){
						parame.agent+=',';
						
					}
					parame.agent+="'"+option.value+"'";
				});
				
				parame.start_time = $('#start_time').val();
				parame.end_time = $('#end_time').val();

		    	$.ajax({
	 		        url: 'server-side/report/technical/excel_answer_call_by_queue_info.php',
	 			    data: parame,
			        success: function(data) {
				        if(data == 1){
					        alert('ჩანაწერი არ მოიძებნა');
				        }else{
			        	SaveToDisk('server-side/report/technical/excel.xls', 'excel.xls');
				        }
	 			    }
	 		    });
		    	
			});
			
		 $(document).on("click", "#excel_disconnection_cause_info", function () {	
			 var i=0;
				
				parame 			= new Object();
				parame.agent	= '';
				parame.queuet = '';

				var options = $('#myform_List_Queue_to option');
				var values = $.map(options ,function(option) {
					if(parame.queuet != ""){
						parame.queuet+=",";
						
					}
					parame.queuet+="'"+option.value+"'";
				});

				
				var options = $('#myform_List_Agent_to option');
				var values = $.map(options ,function(option) {
					if(parame.agent != ''){
						parame.agent+=',';
						
					}
					parame.agent+="'"+option.value+"'";
				});
				
				parame.start_time = $('#start_time').val();
				parame.end_time = $('#end_time').val();

		    	$.ajax({
	 		        url: 'server-side/report/technical/excel_disconnection_cause_info.php',
	 			    data: parame,
			        success: function(data) {
				        if(data == 1){
					        alert('ჩანაწერი არ მოიძებნა');
				        }else{
			        	SaveToDisk('server-side/report/technical/excel.xls', 'excel.xls');
				        }
	 			    }
	 		    });
		    	
			});

		 $(document).on("click", "#excel_unanswer_call_info", function () {	
			 var i=0;
				
				parame 			= new Object();
				parame.agent	= '';
				parame.queuet = '';

				var options = $('#myform_List_Queue_to option');
				var values = $.map(options ,function(option) {
					if(parame.queuet != ""){
						parame.queuet+=",";
						
					}
					parame.queuet+="'"+option.value+"'";
				});

				
				var options = $('#myform_List_Agent_to option');
				var values = $.map(options ,function(option) {
					if(parame.agent != ''){
						parame.agent+=',';
						
					}
					parame.agent+="'"+option.value+"'";
				});
				
				parame.start_time = $('#start_time').val();
				parame.end_time = $('#end_time').val();

		    	$.ajax({
	 		        url: 'server-side/report/technical/excel_unanswer_call_info.php',
	 			    data: parame,
			        success: function(data) {
				        if(data == 1){
					        alert('ჩანაწერი არ მოიძებნა');
				        }else{
			        	SaveToDisk('server-side/report/technical/excel.xls', 'excel.xls');
				        }
	 			    }
	 		    });
		    	
			});
			
		 $(document).on("click", "#excel_disconnection_cause_unanswer_info", function () {	
			 var i=0;
				
				parame 			= new Object();
				parame.agent	= '';
				parame.queuet = '';

				var options = $('#myform_List_Queue_to option');
				var values = $.map(options ,function(option) {
					if(parame.queuet != ""){
						parame.queuet+=",";
						
					}
					parame.queuet+="'"+option.value+"'";
				});

				
				var options = $('#myform_List_Agent_to option');
				var values = $.map(options ,function(option) {
					if(parame.agent != ''){
						parame.agent+=',';
						
					}
					parame.agent+="'"+option.value+"'";
				});
				
				parame.start_time = $('#start_time').val();
				parame.end_time = $('#end_time').val();

		    	$.ajax({
	 		        url: 'server-side/report/technical/excel_disconnection_cause_unanswer_info.php',
	 			    data: parame,
			        success: function(data) {
				        if(data == 1){
					        alert('ჩანაწერი არ მოიძებნა');
				        }else{
			        	SaveToDisk('server-side/report/technical/excel.xls', 'excel.xls');
				        }
	 			    }
	 		    });
		    	
			});

		 $(document).on("click", "#excel_unanswer_call_by_queue_info", function () {	
			 var i=0;
				
				parame 			= new Object();
				parame.agent	= '';
				parame.queuet = '';

				var options = $('#myform_List_Queue_to option');
				var values = $.map(options ,function(option) {
					if(parame.queuet != ""){
						parame.queuet+=",";
						
					}
					parame.queuet+="'"+option.value+"'";
				});

				
				var options = $('#myform_List_Agent_to option');
				var values = $.map(options ,function(option) {
					if(parame.agent != ''){
						parame.agent+=',';
						
					}
					parame.agent+="'"+option.value+"'";
				});
				
				parame.start_time = $('#start_time').val();
				parame.end_time = $('#end_time').val();

		    	$.ajax({
	 		        url: 'server-side/report/technical/excel_unanswer_call_by_queue_info.php',
	 			    data: parame,
			        success: function(data) {
				        if(data == 1){
					        alert('ჩანაწერი არ მოიძებნა');
				        }else{
			        	SaveToDisk('server-side/report/technical/excel.xls', 'excel.xls');
				        }
	 			    }
	 		    });
		    	
			});

		 $(document).on("click", "#excel_total_info", function () {	
			 var i=0;
				
				parame 			= new Object();
				parame.agent	= '';
				parame.queuet = '';

				var options = $('#myform_List_Queue_to option');
				var values = $.map(options ,function(option) {
					if(parame.queuet != ""){
						parame.queuet+=",";
						
					}
					parame.queuet+="'"+option.value+"'";
				});

				
				var options = $('#myform_List_Agent_to option');
				var values = $.map(options ,function(option) {
					if(parame.agent != ''){
						parame.agent+=',';
						
					}
					parame.agent+="'"+option.value+"'";
				});
				
				parame.start_time = $('#start_time').val();
				parame.end_time = $('#end_time').val();

		    	$.ajax({
	 		        url: 'server-side/report/technical/excel_total_info.php',
	 			    data: parame,
			        success: function(data) {
				        if(data == 1){
					        alert('ჩანაწერი არ მოიძებნა');
				        }else{
			        	SaveToDisk('server-side/report/technical/excel.xls', 'excel.xls');
				        }
	 			    }
	 		    });
		    	
			});

		 $(document).on("click", "#excel_answer_call_by_day_info", function () {	
			 var i=0;
				
				parame 			= new Object();
				parame.agent	= '';
				parame.queuet = '';

				var options = $('#myform_List_Queue_to option');
				var values = $.map(options ,function(option) {
					if(parame.queuet != ""){
						parame.queuet+=",";
						
					}
					parame.queuet+="'"+option.value+"'";
				});

				
				var options = $('#myform_List_Agent_to option');
				var values = $.map(options ,function(option) {
					if(parame.agent != ''){
						parame.agent+=',';
						
					}
					parame.agent+="'"+option.value+"'";
				});
				
				parame.start_time = $('#start_time').val();
				parame.end_time = $('#end_time').val();

		    	$.ajax({
	 		        url: 'server-side/report/technical/excel_answer_call_by_day_info.php',
	 			    data: parame,
			        success: function(data) {
				        if(data == 1){
					        alert('ჩანაწერი არ მოიძებნა');
				        }else{
			        	SaveToDisk('server-side/report/technical/excel.xls', 'excel.xls');
				        }
	 			    }
	 		    });
		    	
			});

		 $(document).on("click", "#excel_answer_call_by_time_info", function () {	
			 var i=0;
				
				parame 			= new Object();
				parame.agent	= '';
				parame.queuet = '';

				var options = $('#myform_List_Queue_to option');
				var values = $.map(options ,function(option) {
					if(parame.queuet != ""){
						parame.queuet+=",";
						
					}
					parame.queuet+="'"+option.value+"'";
				});

				
				var options = $('#myform_List_Agent_to option');
				var values = $.map(options ,function(option) {
					if(parame.agent != ''){
						parame.agent+=',';
						
					}
					parame.agent+="'"+option.value+"'";
				});
				
				parame.start_time = $('#start_time').val();
				parame.end_time = $('#end_time').val();

		    	$.ajax({
	 		        url: 'server-side/report/technical/excel_answer_call_by_time_info.php',
	 			    data: parame,
			        success: function(data) {
				        if(data == 1){
					        alert('ჩანაწერი არ მოიძებნა');
				        }else{
			        	SaveToDisk('server-side/report/technical/excel.xls', 'excel.xls');
				        }
	 			    }
	 		    });

			});
			
		 $(document).on("click", "#excel_answer_call_by_weak_info", function () {	
			 var i=0;
				
				parame 			= new Object();
				parame.agent	= '';
				parame.queuet = '';

				var options = $('#myform_List_Queue_to option');
				var values = $.map(options ,function(option) {
					if(parame.queuet != ""){
						parame.queuet+=",";
						
					}
					parame.queuet+="'"+option.value+"'";
				});

				
				var options = $('#myform_List_Agent_to option');
				var values = $.map(options ,function(option) {
					if(parame.agent != ''){
						parame.agent+=',';
						
					}
					parame.agent+="'"+option.value+"'";
				});
				
				parame.start_time = $('#start_time').val();
				parame.end_time = $('#end_time').val();

		    	$.ajax({
	 		        url: 'server-side/report/technical/excel_answer_call_by_weak_info.php',
	 			    data: parame,
			        success: function(data) {
				        if(data == 1){
					        alert('ჩანაწერი არ მოიძებნა');
				        }else{
			        	SaveToDisk('server-side/report/technical/excel.xls', 'excel.xls');
				        }
	 			    }
	 		    });
		    	
			});
		 

		 function SaveToDisk(fileURL, fileName) {
		        // for non-IE
		        if (!window.ActiveXObject) {
		            var save = document.createElement('a');
		            save.href = fileURL;
		            save.target = '_blank';
		            save.download = fileName || 'unknown';

		            var event = document.createEvent('Event');
		            event.initEvent('click', true, true);
		            save.dispatchEvent(event);
		            (window.URL || window.webkitURL).revokeObjectURL(save.href);
		        }
			     // for IE
		        else if ( !! window.ActiveXObject && document.execCommand)     {
		            var _window = window.open(fileURL, "_blank");
		            _window.document.close();
		            _window.document.execCommand('SaveAs', true, fileName || fileURL)
		            _window.close();
		        }
		    } 
		
    </script>
    
</head>

<body>

<div id="tabs" style="width: 99%; margin: 0 auto; height:800px; margin-top: 50px;">
		<ul>
			<li><a href="#tab-0">მთავარი</a></li>
			<li><a href="#tab-1">ნაპასუხები</a></li>
			<li><a href="#tab-2">უპასუხო</a></li>
			<li><a href="#tab-3">ზარების განაწილება</a></li>
		</ul>
		<div id="tab-0">
			<div style="width: 27%; float:left;">
			<span>აირჩიე რიგი</span>
			<hr>
			
			    <table border="0" cellspacing="0" cellpadding="8">
					<tbody>
					<tr>
					   	<td>
							ხელმისაწვდომია<br><br>
						    <select name="List_Queue_available" multiple="multiple" id="myform_List_Queue_from" size="10" style="height: 100px;width: 125px;" >
								<option value="2470017">2470017</option>
						    </select>
						</td>
						<td align="left">
							<a onclick="go_next($('#myform_List_Queue_from option:selected').val(),'Queue')"><img src="media/images/go-next.png" width="16" height="16" border="0"></a>
							<a onclick="go_previous($('#myform_List_Queue_to option:selected').val(),'Queue')"><img src="media/images/go-previous.png" width="16" height="16" border="0"></a>
							<br>
							<br>
							<a  onclick="go_last('Queue')"><img src="media/images/go-last.png" width="16" height="16" border="0"></a>
							<a  onclick="go_first('Queue')"><img src="media/images/go-first.png" width="16" height="16" border="0"></a>
						</td>
						<td>
							არჩეული<br><br>
						    <select size="10" name="List_Queue[]" multiple="multiple" style="height: 100px;width: 125px;" id="myform_List_Queue_to">
								
						    </select>
					   </td>
					</tr> 
					</tbody>
				</table>
			</div>
			<div style="width: 27%; float:left; margin-left:20px;">
				<span>აირჩიე ოპერატორი</span>
				<hr>
				<table border="0" cellspacing="0" cellpadding="8">
					<tbody><tr>
					   <td>ხელმისაწვდომია<br><br>
					    <select size="10" name="List_Agent_available" multiple="multiple" id="myform_List_Agent_from" style="height: 100px;width: 125px;">
							<option value="ALF1">ALF1</option>
							<option value="ALF2">ALF2</option>
							<option value="ALF3">ALF3</option>
							<option value="ALF4">ALF4</option>    
							<option value="ALF5">ALF5</option>    
						</select>
					</td>
					<td align="left">
							<a  onclick="go_next($('#myform_List_Agent_from option:selected').val(),'Agent')"><img src="media/images/go-next.png" width="16" height="16" border="0"></a>
							<a  onclick="go_previous($('#myform_List_Agent_to option:selected').val(),'Agent')"><img src="media/images/go-previous.png" width="16" height="16" border="0"></a>
							<br>
							<br>
							<a  onclick="go_last('Agent')"><img src="media/images/go-last.png" width="16" height="16" border="0"></a>
							<a  onclick="go_first('Agent')"><img src="media/images/go-first.png" width="16" height="16" border="0"></a>
					</td>
					<td>
						არჩეული<br><br>
					    <select size="10" name="List_Agent[]" multiple="multiple" style="height: 100px;width: 125px;" id="myform_List_Agent_to" >
					
					    </select>
					   </td>
					</tr> 
					</tbody>
				</table>
			</div>
			<div id="rest" style="margin-top: 200px; width: 100%; float:none;">
				<h2>თარიღის ამორჩევა</h2>
				<hr>
				<div id="button_area">
	            	<div class="left" style="width: 180px;">
	            		<label for="search_start" class="left" style="margin: 6px 0 0 9px; font-size: 12px;">დასაწყისი</label>
	            		<input type="text" name="search_start" id="start_time" class="inpt right" style="width: 80px; height: 16px;"/>
	            	</div>
	            	<div class="right" style="width: 190px;">
	            		<label for="search_end" class="left" style="margin: 6px 0 0 9px; font-size: 12px;">დასასრული</label>
	            		<input type="text" name="search_end" id="end_time" class="inpt right" style="width: 80px; height: 16px;"/>
            		</div>	
            	</div>
            	
            		<input style="margin-left: 15px;" id="show_report" name="show_report" type="submit" value="რეპორტების ჩვენება">
            	
				<div style="margin-top: 55px; position: absolute; right: 30px;"><button id="excel_tech_info">Excel</button></div>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top: 50px">
                <caption>ტექნიკური ინფორმაცია</caption> 
                <tbody>
                <tr>
                	<th></th>
                    <th>სულ</th>
                    <th style="background: #538DD5; color: #FFFFFF">ნაპასუხები</th>
                    <th style="background: #FA3A3A; color: #FFFFFF">უპასუხო</th>
                    <th style="background: #76933C; color: #FFFFFF">დამუშავებული *</th>
                    <th>ნაპასუხებია</th>
                    <th>უპასუხოა</th>
                    <th>დამუშავებულია</th>
                </tr>
                <tr id="technik_info">
                    <td>ზარი</td>
                    <td></td>
                    <td id="answear_dialog" style="cursor: pointer; text-decoration: underline;"></td>
                    <td id="unanswear_dialog" style="cursor: pointer; text-decoration: underline;"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
                </table>
                <br>
                <div id="chart_container0" style="float:left;" width: 50%; height: 300px;"></div>
                 <div id="chart_container0r" style="float:left;" width: 50%; height: 300px;"></div>
		</div>
		 </div>
		<div id="tab-1">
		<div style="margin-top: 5px; position: absolute; left: 580px;"><button class="excel_report_info">Excel</button></div>
		   <table width="99%" cellpadding="3" cellspacing="3" border="0">
        <thead>
        <tr>
            <td valign="top" width="50%" style="padding:0 5px 0 0;">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <caption>რეპორტ ინფო</caption>
                <tbody class="report_info">
                <tr>
                    <td class="tdstyle">რიგი:</td>
                    <td></td>
                </tr>
                <tr>
	                	<td class="tdstyle">საწყისი თარიღი:</td>
	                    <td></td>
                </tr>
                
                <tr>
                       <td class="tdstyle">დასრულების თარიღი:</td>
                       <td></td>
                </tr>
                <tr>
                       <td class="tdstyle">პერიოდი:</td>
                       <td></td>
                </tr>
                </tbody>
                </table>

            </td>
            <td valign="top" width="50%">
				<div style="margin-top: 5px; position: absolute; right: 40px;"><button id="excel_answer_call_info">Excel</button></div>
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <caption>ნაპასუხები ზარები</caption>
                <tbody id="answer_call_info">
                <tr> 
                  <td class="tdstyle">ნაპასუხები ზარები</td>
                  <td></td>
                </tr>
                <tr> 
                  <td class="tdstyle">გადამისამართებული ზარები</td>
                  <td></td>
                </tr>
                <tr>
                  <td class="tdstyle">საშ. ხანგძლივობა:</td>
                  <td></td>
                </tr>
                <tr>
                  <td class="tdstyle">სულ საუბრის ხანგძლივობა:</td>
                  <td> </td>
                </tr>
                <tr>
                  <td class="tdstyle">ლოდინის საშ. ხანგძლივობა:</td>
                  <td></td>
                </tr>
                </tbody>
              </table>

            </td>
        </tr>
        </thead>
        </table>
        <br>
        <div style="margin-top: 5px; position: absolute; right: 40px;"><button id="excel_answer_call_by_agent_info">Excel</button></div>
        <table width="99%" cellpadding="3" cellspacing="3" border="0" class="sortable" id="table1">
        <caption>ნაპასუხები ზარები ოპერატორების მიხედვით</caption>
            <thead>
            <tr>
                  <th><a  class="sortheader">ოპერატორი<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                  <th><a  class="sortheader">ზარები<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                  <th><a  class="sortheader">% ზარები<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                  <th><a  class="sortheader">ზარის დრო<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                  <th><a  class="sortheader">% ზარის დრო<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                  <th><a  class="sortheader">საშ. ზარის ხანგძლივობა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                  <th><a  class="sortheader">ლოდინის დრო<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                  <th><a  class="sortheader">საშ. ლოდისნის ხანგძლივობა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
            </tr>
            </thead>
            <tbody id="answer_call_by_queue">
                
			</tbody>
        </table>
        <br>
          <div id="chart_container" style="float:left; width: 90%; height: 300px; margin-left: 20px;"></div>
      <br>
      <div style="margin-top: 290px; position: absolute; left: 550px;"><button id="excel_answer_service_level_info">Excel</button></div>
        <table width="47%" cellpadding="3" cellspacing="3" border="0" style="float:left;">
            <caption>მომსახურების დონე(Service Level)</caption>
            <thead>
            <tr>
            <td valign="top" width="100%" bgcolor="#fffdf3">
                <table width="100%" cellpadding="1" cellspacing="1" border="0" class="sortable" id="table3">
                <thead>
                <tr> 
                    <th><a  class="sortheader">პასუხი<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader">რაოდენობა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader">დელტა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader">%<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                </tr>
                </thead>
                <tbody id="service_level">
                
              </tbody>
              </table>
            </td>
            <td valign="top" width="50%" align="center" bgcolor="#fffdf3">
                            </td>
            </tr>
            </thead>
            </table>
            <div id="chart_container11" bgcolor="#fffdf3" style="float:left; width: 50%; height: 300px;"></div>
        <br>
        <div style="margin-top: 580px; position: absolute; left: 550px;"><button id="excel_answer_call_by_queue_info">Excel</button></div>
        <table width="47%" cellpadding="3" cellspacing="3" border="0" style="float:left;">
            <caption>ნაპასუხები ზარები რიგის მიხედვით</caption>
            <thead>
            <tr>
            <td valign="top" width="100%" bgcolor="#fffdf3">
                <table width="100%" cellpadding="1" cellspacing="1" border="0" class="sortable" id="table3">
                <thead>
                <tr> 
                       <th><a  class="sortheader" onclick="ts_resortTable(this, 0);return false;">რიგი<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 1);return false;">სულ<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 2);return false;">%<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                </tr>
                </thead>
                <tbody id="answer_call">
                
              </tbody>
              </table>
            </td>
            <td valign="top" width="50%" align="center" bgcolor="#fffdf3">
                            </td>
            </tr>
            </thead>
            </table>
            <br>
            <div id="chart_container7" bgcolor="#fffdf3" style="float:left; width: 50%; height: 300px;"></div>
            <br>
            <div style="margin-top: 855px; position: absolute; left: 550px;"><button id="excel_disconnection_cause_info">Excel</button></div>
            <table width="47%" cellpadding="3" cellspacing="3" border="0" style="float:left;">
            <caption>კავშირის გაწყვეტის მიზეზეი</caption>
            <thead>
            <tr>
            <td valign="top" width="100%" bgcolor="#fffdf3">
                <table width="100%" cellpadding="1" cellspacing="1" border="0" class="sortable" id="table4">
                <thead>
                <tr>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 0);return false;">მიზეზი<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 1);return false;">სულ<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 2);return false;">სულ<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                </tr>
                </thead>
                <tbody id="disconnection_cause">
	                <tr>
						<td class="tdstyle">ოპერატორმა გათიშა:</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td class="tdstyle">აბონენტმა გათიშა:</td>
						<td></td>
						<td></td>
					</tr>
                </tbody>
              </table>
            </td>
           
            </tr>
            </thead>
            </table>
            <br>
              <div id="chart_container1" style="float:left; width: 50%; height: 300px;"></div>
		 </div>
		 <div id="tab-2">
		 <div style="margin-top: 5px; position: absolute; left: 580px;"><button class="excel_report_info">Excel</button></div>
		    <table width="99%" cellpadding="3" cellspacing="3" border="0">
		<thead>
		<tr>
			<td valign="top" width="50%" style="padding: 0 5px 0 0;">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<caption>რეპორტ ინფო</caption>
				<tbody class="report_info">
				<tr>
                    <td class="tdstyle">რიგი:</td>
                    <td></td>
                </tr>
                
                       <tr><td class="tdstyle">საწყისი თარიღი:</td>
                       <td></td>
                </tr>
                
                <tr>
                       <td class="tdstyle">დასრულების თარიღი:</td>
                       <td></td>
                </tr>
                <tr>
                       <td class="tdstyle">პერიოდი:</td>
                       <td></td>
                </tr>
				</tbody>
				</table>

			</td>
			<td valign="top" width="50%">
			<div style="margin-top: 5px; position: absolute; right: 40px;"><button id="excel_unanswer_call_info">Excel</button></div>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<caption>უპასუხო ზარები</caption>
				<tbody id="unanswer_call">
		        <tr> 
                  <td class="tdstyle">უპასუხო ზარების რაოდენობა:</td>
		          <td></td>
	            </tr>
                <tr>
                  <td class="tdstyle">ლოდინის საშ. დრო კავშირის გაწყვეტამდე:</td>
                  <td></td>
                </tr>
		        <tr>
                  <td class="tdstyle">საშ. რიგში პოზიცია კავშირის გაწყვეტამდე:</td>
		          <td></td>
	            </tr>
                <tr>
                  <td class="tdstyle">საშ. საწყისი პოზიცია რიგში:</td>
                  <td></td>
                </tr>
				</tbody>
	          </table>

			</td>
		</tr>
		</thead>
		</table>
		<br>
		<div style="margin-top: 5px; position: absolute; left: 550px;"><button id="excel_disconnection_cause_unanswer_info">Excel</button></div>
		<table width="47%" cellpadding="3" cellspacing="3" border="0" style="float:left;">
		<caption>კავშირის გაწყვეტის მიზეზი</caption>
			<thead>
			<tr>
			<td valign="top" width="100%" bgcolor="#fffdf3">
				<table width="100%" cellpadding="1" cellspacing="1" border="0">
				<thead>
				<tr>
					<th>მიზეზი</th>
					<th>სულ</th>
					<th>%</th>
				</tr>
				</thead >
				<tbody id="disconnection_cause_unanswer">
                <tr> 
                  <td class="tdstyle">აბონენტმა გათიშა</td>
			      <td></td>
			      <td></td>
		        </tr>
			    <tr> 
                  <td class="tdstyle">დრო ამოიწურა</td>
			      <td></td>
			      <td></td>
		        </tr>
				</tbody>
			  </table>
			</td>
			</tr>
			</thead>
			</table>
			<div id="chart_container5" style="float:left; width: 50%; height: 300px;"></div>
			<br>
			<div style="margin-top: 290px; position: absolute; left: 550px;"><button id="excel_unanswer_call_by_queue_info">Excel</button></div>
			<table width="47%" cellpadding="3" cellspacing="3" border="0" style="float:left;">
			<caption>უპასუხო ზარები რიგის მიხედვით</caption>
			<thead>
			<tr>
			<td valign="top" width="100%" bgcolor="#fffdf3">
				<table width="100%" cellpadding="1" cellspacing="1" border="0">
				<thead>
                <tr> 
				   	<th>რიგი</th>
					<th>სულ</th>
					<th>%</th>
                </tr>
				</thead>
				<tbody id="unanswered_calls_by_queue">
				<tr><td></td><td></td><td></td></tr>
			  </tbody>
			  </table>
			</td>
			
			</tr>
			</thead>
			</table>
			<br>
			  <div id="chart_container6" style="float:left; width: 50%; height: 300px;"></div>
		 </div>
		 <div id="tab-3">
		 <div style="margin-top: 5px; position: absolute; left: 580px;"><button class="excel_report_info">Excel</button></div>
		    <table width="99%" cellpadding="3" cellspacing="3" border="0">
		<thead>
		<tr>
			<td valign="top" width="50%" style="padding: 0 5px 0 0;">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<caption>რეპორტ ინფო</caption>
				<tbody class="report_info">
				<tr>
                    <td class="tdstyle">რიგი:</td>
                    <td></td>
                </tr>
                
                       <tr><td class="tdstyle">საწყისი თარიღი:</td>
                       <td></td>
                </tr>
                
                <tr>
                       <td class="tdstyle">დასრულების თარიღი:</td>
                       <td></td>
                </tr>
                <tr>
                       <td class="tdstyle">პერიოდი:</td>
                       <td></td>
                </tr>
				</tbody>
				</table>

			</td>
			<td valign="top" width="50%">
				<div style="margin-top: 5px; position: absolute; right: 40px;"><button id="excel_total_info">Excel</button></div>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<caption>სულ</caption>
				<tbody id="totals">
		        <tr> 
                  <td class="tdstyle">ნაპასუხები ზარების რაოდენობა:</td>
		          <td></td>
	            </tr>
                <tr>
                  <td class="tdstyle">უპასუხო ზარების რაოდენობა:</td>
                  <td></td>
                </tr>
		        <tr>
                  <td class="tdstyle">ოპერატორი შევიდა:</td>
		          <td></td>
	            </tr>
                <tr>
                  <td class="tdstyle">ოპერატორი გავიდა:</td>
                  <td></td>
                </tr>
				</tbody>
	          </table>

			</td>
		</tr>
		</thead>
		</table>
		<br>
		<div style="margin-top: 5px; position: absolute; right: 40px;"><button id="excel_answer_call_by_day_info">Excel</button></div>
		<table width="99%" cellpadding="1" cellspacing="1" border="0" class="sortable" id="table1">
			<caption>ზარის განაწილება დღეების მიხედვით</caption>
				<thead>
				<tr>
					<th><a  class="sortheader" onclick="ts_resortTable(this, 0);return false;">თარირი<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
					<th><a  class="sortheader" onclick="ts_resortTable(this, 1);return false;">ნაპასუხები<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
					<th><a  class="sortheader" onclick="ts_resortTable(this, 2);return false;">% ნაპასუხები<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
					<th><a  class="sortheader" onclick="ts_resortTable(this, 3);return false;">უპასუხო<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
					<th><a  class="sortheader" onclick="ts_resortTable(this, 4);return false;">% უპასუხო<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
					<th><a  class="sortheader" onclick="ts_resortTable(this, 5);return false;">საშ. ხანგძლივობა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
					<th><a  class="sortheader" onclick="ts_resortTable(this, 6);return false;">საშ. ლოდინის ხანგძლივობა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
					<th><a  class="sortheader" onclick="ts_resortTable(this, 7);return false;">შესვლა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
					<th><a  class="sortheader" onclick="ts_resortTable(this, 8);return false;">გასვლა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
				</tr>
				</thead>
				<tbody id="call_distribution_per_day">
				<tr class="odd">
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					</tr>
				</tbody>
			</table>
			<br>
			<div id="chart_container4" style="float:left; width: 47%; height: 300px; margin-left: 20px;"></div>
			<div id="chart_container8" style="float:right; width: 47%; height: 300px; margin-left: 20px;"></div>
			<br>
			<div style="margin-top: 295px; position: absolute; right: 40px;"><button id="excel_answer_call_by_time_info">Excel</button></div>
			<table width="99%" cellpadding="1" cellspacing="1" border="0" class="sortable" id="table2">
			<caption>ზარის განაწილება საათების მიხედვით</caption>
				<thead>
				<tr>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 0);return false;">საათი<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 1);return false;">ნაპასუხები<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 2);return false;">% ნაპასუხები<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 3);return false;">უპასუხო<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 4);return false;">% უპასუხო<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 5);return false;">საშ. ხანგძლივობა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 6);return false;">საშ. ლოდინის ხანგძლივობა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 7);return false;">შესვლა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 8);return false;">გასვლა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
				</tr>
				</thead>
				<tbody id="call_distribution_per_hour">
				<tr class="odd">
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					</tr>
			</tbody>
			</table>
			<div id="chart_container2" style="float:left; width: 47%; height: 300px; margin-left: 20px;"></div>
			  <div id="chart_container9" style="float:right; width: 47%; height: 300px; margin-left: 20px;"></div>
			<br>
			<div style="margin-top: 295px; position: absolute; right: 40px;"><button id="excel_answer_call_by_weak_info">Excel</button></div>
			<table width="99%" cellpadding="1" cellspacing="1" border="0" class="sortable" id="table3">
			<caption>ზარის განაწილება კვირის დღეების მიხედვით</caption>
				<thead>
				<tr>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 0);return false;">დღე<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 1);return false;">ნაპასუხები<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 2);return false;">% ნაპასუხები<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 3);return false;">უპასუხო<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 4);return false;">% უპასუხო<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 5);return false;">საშ. ხანგძლივობა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 6);return false;">საშ. ლოდინის ხანგძლივობა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 7);return false;">შესვლა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 8);return false;">გასვლა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
				</tr>
				</thead>
				<tbody id="call_distribution_per_day_of_week">
				<tr class="odd">
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					</tr>
			</tbody>
			</table>
			<br>
			 <div id="chart_container3" style="float:left; width: 47%; height: 300px; margin-left: 20px;"></div>
			<div id="chart_container10" style="float:right; width: 47%; height: 300px; margin-left: 20px;"></div>
		 </div>
		 
</div>
<!-- jQuery Dialog -->
<div id="add-edit-form" class="form-dialog" title="ნაპასუხები ზარები">
<div id="test"></div>
</div>

<!-- jQuery Dialog -->
<div id="add-edit-form-unanswer" class="form-dialog" title="უპასუხო ზარები">
<div id="test"></div>
</div>

<!-- jQuery Dialog -->
<div id="add-edit-form1" class="form-dialog" title="გამავალი ზარი">
<!-- aJax -->
</div>

<!-- jQuery Dialog -->
<div id="add-edit-form2" class="form-dialog" title="გამავალი ზარი">
<!-- aJax -->
</div>

<div id="add-responsible-person" class="form-dialog" title="პასუხისმგებელი პირი">
<!-- aJax -->
</div>
</body>