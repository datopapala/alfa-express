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
			padding: 6px;
			border: solid 1px #326e87;
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
    </style>
	<script type="text/javascript">
		var aJaxURL		= "server-side/report/technical.action.php";		//server side folder url
		var tName		= "example0";										//table name
		var tbName		= "tabs";											//tabs name
		var fName		= "add-edit-form";									//form name
		var file_name = '';
		var rand_file = '';
		
		$(document).ready(function () {     
			GetTabs(tbName);   	
			GetDate("start_time");
			GetDate("end_time");
			$("#show_report").button({
	            
		    });
		});

		$(document).on("tabsactivate", "#tabs", function() {
        	var tab = GetSelectedTab(tbName);
        	if (tab == 0) {
        		
        	}else if(tab == 1){
        		
            }else if(tab == 2){
            	
            }else if(tab == 3){
            	
            }
        });

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
			    }
		    });
        });
    </script>
    
</head>

<body>

<div id="tabs" style="width: 95%; margin: 0 auto; min-height: 768px; margin-top: 50px;">
		<ul>
			<li><a href="#tab-0">მთავარი</a></li>
			<li><a href="#tab-1">ნაპასუხები</a></li>
			<li><a href="#tab-2">უპასუხო</a></li>
			<li><a href="#tab-3">ზარების განაწილება</a></li>
		</ul>
		<div id="tab-0">
			<div style="width: 48%; float:left;">
			<span>აირჩიე რიგი</span>
			<hr>
			
			    <table border="0" cellspacing="0" cellpadding="8">
					<tbody>
					<tr>
					   	<td>
							ხელმისაწვდომია<br>
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
							არჩეული<br>
						    <select size="10" name="List_Queue[]" multiple="multiple" style="height: 100px;width: 125px;" id="myform_List_Queue_to">
								
						    </select>
					   </td>
					</tr> 
					</tbody>
				</table>
			</div>
			<div style="width: 50%; float:left; margin-left:20px;">
				<span>აირჩიე ოპერატორი</span>
				<hr>
				<table border="0" cellspacing="0" cellpadding="8">
					<tbody><tr>
					   <td>
						ხელმისაწვდომია<br>
					    <select size="10" name="List_Agent_available" multiple="multiple" id="myform_List_Agent_from" style="height: 100px;width: 125px;">
							<option value="ALF1">ALF1</option>
							<option value="ALF2">ALF2</option>
							<option value="ALF3">ALF3</option>
							<option value="ALF4">ALF4</option>    
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
						არჩეული<br>
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
	            	<div class="left" style="width: 250px;">
	            		<label for="search_start" class="left" style="margin: 5px 0 0 9px;">დასაწყისი</label>
	            		<input type="text" name="search_start" id="start_time" class="inpt right"/>
	            	</div>
	            	<div class="right" style="">
	            		<label for="search_end" class="left" style="margin: 5px 0 0 9px;">დასასრული</label>
	            		<input type="text" name="search_end" id="end_time" class="inpt right" />
            		</div>	
            	</div>
            	
            		<input style="margin-left: 15px;" id="show_report" name="show_report" type="submit" value="რეპორტების ჩვენება">
            	
				
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top: 50px">
                <caption>ტექნიკური ინფორმაცია</caption>
                <tbody>
                <tr>
                	<th></th>
                    <th>სულ</th>
                    <th>ნაპასუხები</th>
                    <th>უპასუხო</th>
                    <th>დამუშავებული *</th>
                    <th>ნაპასუხებია</th>
                    <th>უპასუხოა</th>
                    <th>დამუშავებულია</th>
                </tr>
                <tr id="technik_info">
                    <td>ზარი</td>
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
		</div>
		 </div>
		<div id="tab-1">
		   <table width="99%" cellpadding="3" cellspacing="3" border="0">
        <thead>
        <tr>
            <td valign="top" width="50%" style="padding:0 5px 0 0;">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <caption>რეპორტ ინფო</caption>
                <tbody class="report_info">
                <tr>
                    <td>რიგი:</td>
                    <td></td>
                </tr>
                
                       <tr><td>საწყისი თარიღი:</td>
                       <td></td>
                </tr>
                
                <tr>
                       <td>დასრულების თარიღი:</td>
                       <td></td>
                </tr>
                <tr>
                       <td>პერიოდი:</td>
                       <td></td>
                </tr>
                </tbody>
                </table>

            </td>
            <td valign="top" width="50%">

                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <caption>ნაპასუხები ზარები</caption>
                <tbody id="answer_call_info">
                <tr> 
                  <td>ნაპასუხები ზარები</td>
                  <td></td>
                </tr>
                <tr> 
                  <td>გადამისამართებული ზარები</td>
                  <td></td>
                </tr>
                <tr>
                  <td>საშ. ხანგძლივობა:</td>
                  <td></td>
                </tr>
                <tr>
                  <td>სულ საუბრის ხანგძლივობა:</td>
                  <td> </td>
                </tr>
                <tr>
                  <td>ლოდინის საშ. ხანგძლივობა:</td>
                  <td></td>
                </tr>
                </tbody>
              </table>

            </td>
        </tr>
        </thead>
        </table>
        <br>
        <table width="99%" cellpadding="3" cellspacing="3" border="0" class="sortable" id="table1">
        <caption>ნაპასუხები ზარები ოპერატორების მიხედვით</caption>
            <thead>
            <tr>
                  <th><a  class="sortheader" onclick="ts_resortTable(this, 0);return false;">ოპერატორი<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                  <th><a  class="sortheader" onclick="ts_resortTable(this, 1);return false;">ზარები<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                  <th><a  class="sortheader" onclick="ts_resortTable(this, 2);return false;">% ზარები<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                  <th><a  class="sortheader" onclick="ts_resortTable(this, 3);return false;">ზარის დრო<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                  <th><a  class="sortheader" onclick="ts_resortTable(this, 4);return false;">% ზარის დრო<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                  <th><a  class="sortheader" onclick="ts_resortTable(this, 5);return false;">საშ. ზარის ხანგძლივობა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                  <th><a  class="sortheader" onclick="ts_resortTable(this, 6);return false;">ლოდინის დრო<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                  <th><a  class="sortheader" onclick="ts_resortTable(this, 7);return false;">საშ. ლოდისნის ხანგძლივობა<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
            </tr>
            </thead>
            <tbody id="answer_call_by_queue">
                
			</tbody>
        </table>
        <br>
        <table width="99%" cellpadding="3" cellspacing="3" border="0">
            <caption>ნაპასუხები ზარები რიგის მიხედვით</caption>
            <thead>
            <tr>
            <td valign="top" width="50%" bgcolor="#fffdf3">
                <table width="99%" cellpadding="1" cellspacing="1" border="0" class="sortable" id="table3">
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
            <table width="99%" cellpadding="3" cellspacing="3" border="0">
            <caption>კავშირის გაწყვეტის მიზეზეი</caption>
            <thead>
            <tr>
            <td valign="top" width="50%" bgcolor="#fffdf3">
                <table width="50%" cellpadding="1" cellspacing="1" border="0" class="sortable" id="table4">
                <thead>
                <tr>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 0);return false;">მიზეზი<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 1);return false;">სულ<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                    <th><a  class="sortheader" onclick="ts_resortTable(this, 2);return false;">სულ<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
                </tr>
                </thead>
                <tbody id="disconnection_cause">
	                <tr>
						<td>ოპერატორმა გათიშა:</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>აბონენტმა გათიშა:</td>
						<td></td>
						<td></td>
					</tr>
                </tbody>
              </table>
            </td>
           
            </tr>
            </thead>
            </table>
		 </div>
		 <div id="tab-2">
		    <table width="99%" cellpadding="3" cellspacing="3" border="0">
		<thead>
		<tr>
			<td valign="top" width="50%" style="padding: 0 5px 0 0;">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<caption>რეპორტ ინფო</caption>
				<tbody class="report_info">
				<tr>
                    <td>რიგი:</td>
                    <td></td>
                </tr>
                
                       <tr><td>საწყისი თარიღი:</td>
                       <td></td>
                </tr>
                
                <tr>
                       <td>დასრულების თარიღი:</td>
                       <td></td>
                </tr>
                <tr>
                       <td>პერიოდი:</td>
                       <td></td>
                </tr>
				</tbody>
				</table>

			</td>
			<td valign="top" width="50%">

				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<caption>უპასუხო ზარები</caption>
				<tbody id="unanswer_call">
		        <tr> 
                  <td>უპასუხო ზარების რაოდენობა:</td>
		          <td></td>
	            </tr>
                <tr>
                  <td>ლოდინის საშ. დრო კავშირის გაწყვეტამდე:</td>
                  <td></td>
                </tr>
		        <tr>
                  <td>საშ. რიგში პოზიცია კავშირის გაწყვეტამდე:</td>
		          <td></td>
	            </tr>
                <tr>
                  <td>საშ. საწყისი პოზიცია რიგში:</td>
                  <td></td>
                </tr>
				</tbody>
	          </table>

			</td>
		</tr>
		</thead>
		</table>
		<br>
		<table width="99%" cellpadding="3" cellspacing="3" border="0">
		<caption>კავშირის გაწყვეტის მიზეზი</caption>
			<thead>
			<tr>
			<td valign="top" width="50%" bgcolor="#fffdf3">
				<table width="50%" cellpadding="1" cellspacing="1" border="0">
				<thead>
				<tr>
					<th>მიზეზი</th>
					<th>სულ</th>
					<th>%</th>
				</tr>
				</thead>
				<tbody>
                <tr> 
                  <td>აბონენტმა გათიშა</td>
			      <td>78 calls</td>
			      <td>
					  100.00 
                   %</td>
		        </tr>
			    <tr> 
                  <td>დრო ამოიწურა</td>
			      <td>0 calls</td>
			      <td>
					  0.00 
					%</td>
		        </tr>
				</tbody>
			  </table>
			</td>
			</tr>
			</thead>
			</table>
			<br>
			<table width="99%" cellpadding="3" cellspacing="3" border="0">
			<caption>უპასუხო ზარები რიგის მიხედვით</caption>
			<thead>
			<tr>
			<td valign="top" width="50%" bgcolor="#fffdf3">
				<table width="50%" cellpadding="1" cellspacing="1" border="0">
				<thead>
                <tr> 
				   	<th>რიგი</th>
					<th>სულ</th>
					<th>%</th>
                </tr>
				</thead>
				<tbody>
				<tr><td>2470017</td><td>78 calls</td><td>100.00 %</td></tr>
			  </tbody>
			  </table>
			</td>
			
			</tr>
			</thead>
			</table>
		 </div>
		 <div id="tab-3">
		    <table width="99%" cellpadding="3" cellspacing="3" border="0">
		<thead>
		<tr>
			<td valign="top" width="50%" style="padding: 0 5px 0 0;">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<caption>რეპორტ ინფო</caption>
				<tbody class="report_info">
				<tr>
                    <td>რიგი:</td>
                    <td></td>
                </tr>
                
                       <tr><td>საწყისი თარიღი:</td>
                       <td></td>
                </tr>
                
                <tr>
                       <td>დასრულების თარიღი:</td>
                       <td></td>
                </tr>
                <tr>
                       <td>პერიოდი:</td>
                       <td></td>
                </tr>
				</tbody>
				</table>

			</td>
			<td valign="top" width="50%">

				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<caption>სულ</caption>
				<tbody>
		        <tr> 
                  <td>ნაპასუხები ზარების რაოდენობა:</td>
		          <td>210 calls</td>
	            </tr>
                <tr>
                  <td>უპასუხო ზარების რაოდენობა:</td>
                  <td>78 calls</td>
                </tr>
		        <tr>
                  <td>ოპერატორი შევიდა:</td>
		          <td>0</td>
	            </tr>
                <tr>
                  <td>ოპერატორი გავიდა:</td>
                  <td>0</td>
                </tr>
				</tbody>
	          </table>

			</td>
		</tr>
		</thead>
		</table>
		<br>
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
				<tbody>
				<tr class="odd">
					<td>2014-09-01</td>
					<td>210</td>
					<td>100.00 %</td>
					<td>78</td>
					<td>100.00%</td>
					<td>2:26 min</td>
					<td>35 secs</td>
					<td></td>
					<td></td>
					</tr>
				</tbody>
			</table>
			<br>
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
				<tbody>
				<tr class="odd">
					<td>00</td>
					<td>0</td>
					<td>0.00%</td>
					<td>1</td>
					<td>1.28%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr>
					<td>01</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr class="odd">
					<td>02</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr>
					<td>03</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr class="odd">
					<td>04</td>
					<td>0</td>
					<td>0.00%</td>
					<td>1</td>
					<td>1.28%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr>
					<td>05</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr class="odd">
					<td>06</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr>
					<td>07</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr class="odd">
					<td>08</td>
					<td>0</td>
					<td>0.00%</td>
					<td>2</td>
					<td>2.56%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr>
					<td>09</td>
					<td>0</td>
					<td>0.00%</td>
					<td>6</td>
					<td>7.69%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr class="odd">
					<td>10</td>
					<td>12</td>
					<td>5.71%</td>
					<td>19</td>
					<td>24.36%</td>
					<td>198 secs</td>
					<td>90 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr>
					<td>11</td>
					<td>23</td>
					<td>10.95%</td>
					<td>5</td>
					<td>6.41%</td>
					<td>217 secs</td>
					<td>34 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr class="odd">
					<td>12</td>
					<td>23</td>
					<td>10.95%</td>
					<td>4</td>
					<td>5.13%</td>
					<td>135 secs</td>
					<td>34 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr>
					<td>13</td>
					<td>19</td>
					<td>9.05%</td>
					<td>5</td>
					<td>6.41%</td>
					<td>95 secs</td>
					<td>33 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr class="odd">
					<td>14</td>
					<td>22</td>
					<td>10.48%</td>
					<td>3</td>
					<td>3.85%</td>
					<td>139 secs</td>
					<td>28 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr>
					<td>15</td>
					<td>29</td>
					<td>13.81%</td>
					<td>7</td>
					<td>8.97%</td>
					<td>140 secs</td>
					<td>37 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr class="odd">
					<td>16</td>
					<td>22</td>
					<td>10.48%</td>
					<td>10</td>
					<td>12.82%</td>
					<td>127 secs</td>
					<td>32 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr>
					<td>17</td>
					<td>22</td>
					<td>10.48%</td>
					<td>4</td>
					<td>5.13%</td>
					<td>122 secs</td>
					<td>16 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr class="odd">
					<td>18</td>
					<td>13</td>
					<td>6.19%</td>
					<td>7</td>
					<td>8.97%</td>
					<td>182 secs</td>
					<td>44 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr>
					<td>19</td>
					<td>11</td>
					<td>5.24%</td>
					<td>2</td>
					<td>2.56%</td>
					<td>133 secs</td>
					<td>35 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr class="odd">
					<td>20</td>
					<td>13</td>
					<td>6.19%</td>
					<td>1</td>
					<td>1.28%</td>
					<td>155 secs</td>
					<td>38 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr>
					<td>21</td>
					<td>1</td>
					<td>0.48%</td>
					<td>0</td>
					<td>0.00%</td>
					<td>38 secs</td>
					<td>7 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr class="odd">
					<td>22</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr>
					<td>23</td>
					<td>0</td>
					<td>0.00%</td>
					<td>1</td>
					<td>1.28%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
			</tbody>
			</table>
			<br>
			<table width="99%" cellpadding="1" cellspacing="1" border="0" class="sortable" id="table3">
			<caption>ზარის განაწილება კვირების მიხედვით</caption>
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
				<tbody>
				<tr class="odd">
					<td>Sunday</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr>
					<td>Monday</td>
					<td>210</td>
					<td>100.00%</td>
					<td>78</td>
					<td>100.00%</td>
					<td>146 secs</td>
					<td>35 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr class="odd">
					<td>Tuesday</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr>
					<td>Wednesday</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr class="odd">
					<td>Thursday</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr>
					<td>Friday</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
					</tr>
					<tr class="odd">
					<td>Saturday</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0</td>
					<td>0.00%</td>
					<td>0 secs</td>
					<td>0 secs</td>
					<td>0</td>
					<td>0</td>
				</tr>
			</tbody>
			</table>
		 </div>
		 
</div>
<!-- jQuery Dialog -->
<div id="add-edit-form" class="form-dialog" title="დავალების ფორმირება">
<!-- aJax -->
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