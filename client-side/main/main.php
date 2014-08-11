<?php
require_once("AsteriskManager/config.php");
include("AsteriskManager/sesvars.php");
if(isset($_SESSION['QSTATS']['hideloggedoff'])) {
    $ocultar=$_SESSION['QSTATS']['hideloggedoff'];
} else {
    $ocultar="false";
}
?>


<head>
	<script type="text/javascript">
		var aJaxURL	= "server-side/call.action.php";		//server side folder url
		var tName	= "example";										//table name
		var fName	= "add-edit-form";									//form name
		
		$(document).ready(function () {   
			runAjax();     	
			LoadTable();	
			
			/* Add Button ID, Delete Button ID */
			GetButtons("add_button", "delete_button","");			
			SetEvents("add_button", "delete_button", "check-all", tName, fName, aJaxURL);
		});
        
		function LoadTable(){
			/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
			GetDataTable(tName, aJaxURL, "get_list",5, "", 0, "", 1, "desc");
		}
		
		function LoadDialog(){
			
			/* Dialog Form Selector Name, Buttons Array */
			GetDialog(fName, 1000, 430, "");
			var requester = $("input[name=requester]:radio:checked").val();
			
			var id = $("#req_id").val();
			
		}
		
		function CloseDialog(){
			$("#" + fName).dialog("close");
		}
		
	    // Add - Save
	    $(document).on("click", "#save-dialog", function () {
		    param 			= new Object();

		    param.act		= "save_request";
	    	param.id		= $("#req_id").val();
	    	
	    	param.req_num		= $("#req_num").val();
	    	param.req_data		= $("#req_data").val();
	    	param.req_phone		= $("#req_phone").val();
	    	param.info_category	= $("input[name=info_category]:radio:checked").val();
	    	param.first_name	= $("#first_name").val();
	    	param.last_name		= $("#last_name").val();
	    	param.phone			= $("#phone").val();
	    	param.content		= $("#content").val();
	    	
			if(param.req_phone == ""){
				alert("შეავსეთ ტელეფონის ნომერი!");
			}else {
			    $.ajax({
			        url: aJaxURL,
				    data: param,
			        success: function(data) {       
						if(typeof(data.error) != 'undefined'){
							if(data.error != ''){
								alert(data.error);
							}else{
								LoadTable();
				        		CloseDialog();
				        		console.log(data.error);
							}
						}
				    }
			    });
			}
		});

	    function run(number){

	    	param 			= new Object();
		 	param.act		= "get_add_page";
		 	param.number	= number;
		 	
	    	$.ajax({
		        url: aJaxURL,
			    data: param,
		        success: function(data) {       
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							$("#add-edit-form").html(data.page);
							LoadDialog();
						}
					}
			    }
		    });			
		    }

	    function runAjax() {
            $.ajax({
            	async: true,
            	dataType: "html",
		        url: 'AsteriskManager/auxstate_helper.php',
			    data: 'sesvar=hideloggedoff&value=true',
		        success: function(data) {
							$("#jq").html(data);						
			    }
            }).done(function(data) { 
                setTimeout(runAjax, 1000);
            });
        }

	    $(document).on("click", ".number", function () {
	    	var number = $(this).attr("number");
	    	if(number != ""){
	    		run(number);
	    		console.log(number);
		    } 
	    });
		
    </script>
</head>

<body>

<table style="width: 1140px; margin: 0 auto;">
		<tr>
			<td>
			
		    <div id="dt_example" class="ex_highlight_row">
		        <div id="container" style="width: 550px !important;">        	
		            <div id="dynamic" >
		            	<h2 align="center">მომართვები</h2>
		            	<div id="button_area">
		        			<button id="add_button">დამატება</button>
		        			<button id=delete_button>წაშლა</button>
		        		</div>
		        	
		                <table class="display" id="example" style="width: 550px;">
		                    <thead>
		                        <tr id="datatable_header">
		                            <th>ID</th>
		                            <th style="width: 25px;">№</th>
		                            <th style="width: 100px;">თარიღი</th>
		                            <th style="width: 140px;">ტელეფონი</th>
		                            <th style="width: 100%;">კატეგორია</th>
		                             <th class="check">#</th>
		                              
		                        </tr>
		                    </thead>
		                    <thead>
		                        <tr class="search_header">
		                            <th class="colum_hidden">
		                            	<input type="text" name="search_id" value="ფილტრი" class="search_init" style="width: 60px"/>
		                            </th>
		                            <th><input type="text" name="search_number" value="ფილტრი" class="search_init hidden-input" style="width: 60px"></th>
		                            <th>
		                                <input type="text" name="search_date" value="ფილტრი" class="search_init" style="width: 60px"/>
		                            </th>
		                            <th>
		                                <input type="text" name="search_phone" value="ფილტრი" class="search_init" style="width: 60px"/>
		                            </th>
		                            <th>
		                                <input type="text" name="search_category" value="ფილტრი" class="search_init" style="width: 60px" />
		                            </th>
		                            <th>
										<input type="checkbox" name="check-all" id="check-all-in"/>
									</th>
		                        </tr>
		                    </thead>
		                </table>
		            </div>
		            <div class="spacer">
		            </div>
		        </div>
		    </div>
		    
	    <td style="width: 5px;">		
		   &nbsp;
		</td>					
		<td>		
		   <div id="jq" style="height: 520px;  width: 456px;"></div>
		</td>			
	</tr>
</table>
    
    <!-- jQuery Dialog -->
    <div  id="add-edit-form" class="form-dialog" title="პროდუქტის კატეგორიები">
    	<!-- aJax -->
	</div>
</body>