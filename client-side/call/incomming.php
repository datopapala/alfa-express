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
<style type="text/css">
.hidden{
	display: none;
}

#box-table-b
{
	font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
	font-size: 12px;
	text-align: center;
	border-collapse: collapse;
	border-top: 7px solid #8FC122;
	border-bottom: 7px solid #8FC122;
}
#box-table-b th
{
	font-size: 13px;
	font-weight: normal;
	padding: 8px;
	background: #F2FBE6;
	border-right: 1px solid #8FC122;
	border-left: 1px solid #8FC122;
	color: #039;
}
#box-table-b td
{
	padding: 8px;
	background: #F2FBE6; 
	border-right: 1px solid #8FC122;
	border-left: 1px solid #8FC122;
	color: #669;
}
</style>
<script type="text/javascript">
		var aJaxURL	= "server-side/call/incomming.action.php";		//server side folder url
		var upJaxURL		= "server-side/upload/file.action.php";	
		var tName	= "example";										//table name
		var fName	= "add-edit-form";									//form name
		var file_name = '';
		var rand_file = '';

		$(document).ready(function () {
		

			runAjax();
			LoadTable();

			/* Add Button ID, Delete Button ID */
			GetButtons("add_button", "","");
			SetEvents("add_button", "", "", tName, fName, aJaxURL);

		});

		$(document).on("click", "#button_calls", function () {
			LoadDialogCalls();
			$('#refresh-dialog').click(); 
		});

		function LoadDialogCalls(){
			var button = {
               		"save": {
               			text: "განახლება",
               			id: "refresh-dialog",
               			click: function () {
               			}
               		}
				};

			/* Dialog Form Selector Name, Buttons Array */
			GetDialogCalls('last_calls', 330, 550, button);
		}

		function LoadTable(){
			/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
			GetDataTable(tName, aJaxURL, "get_list",9, "", 0, "", 1, "desc");
		}

		$(document).on("click", ".download", function () {
            var link = $(this).attr("str");
            link = 'http://212.72.155.175:8181/records/' + link + '.wav';

            window.open(link, 'chatwindow', "width=420,height=230,resizable=yes,scrollbars=yes,status=yes");
            
        });

		function LoadDialog(){

			/* Dialog Form Selector Name, Buttons Array */
			GetDialog(fName, 1200, "auto", "mail");
			var id = $("#incomming_id").val();
			var cat_id = $("#category_parent_id").val();
			$( ".calls" ).button({
			      icons: {
			        primary: " ui-icon-contact"
			      }
			});
			$( ".download" ).button({
			    
			});
		}

		function CloseDialog(){
			$("#" + fName).dialog("close");
		}

	    // Add - Save
	    $(document).on("click", "#save-dialog", function () {
		    param 			= new Object();

		    param.act						= "save_incomming";

	    	param.id_p							= $("#id").val();
	    	param.id_h							= $("#h_id").val();
	    	param.c_date						= $("#c_date").val();
	    	param.phone							= $("#phone").val();
	    	param.person_name					= $("#person_name").val();
	    	param.type							= $("input[name='x']:checked").val();
	    	param.call_vote						= $("input[name='xx']:checked").val();
	    	param.results_id					= $("#results_id").val();
	    	param.information_category_id		= $("#information_category_id").val();
	    	param.information_sub_category_id	= $("#information_sub_category_id").val();
	    	param.content_id					= $("#content_id").val();
	    	param.product_id					= $("#product_id").val();
	    	param.forward_id					= $("#forward_id").val();
	    	param.connect						= $("#connect:checked").val();
	    	param.results_comment				= $("#results_comment").val();
	    	param.content						= $("#content").val();
	    	param.task_type_id					= $("#task_type_id").val();
	    	param.task_department_id			= $("#task_department_id").val();
	    	param.persons_id					= $("#persons_id").val();
	    	param.comment						= $("#comment").val();
	    	param.source_id						= $("#source_id").val();

	    	// Personal Info
	    	param.personal_phone				= $("#personal_phone").val();
	    	param.personal_id					= $("#personal_id").val();
	    	param.personal_contragent			= $("#personal_contragent").val();
	    	param.personal_mail					= $("#personal_mail").val();
	    	param.personal_addres				= $("#personal_addres").val();
	    	param.personal_status				= $("#personal_status").val();
	    	
	    	
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

		 // Send - Mail
	    $(document).on("click", "#send_mail", function () {
		    param 			= new Object();

		    param.act						= "send_mail";

	    	param.id_p							= $("#id").val();
	    	param.c_date						= $("#c_date").val();
	    	param.phone							= $("#phone").val();
	    	param.person_name					= $("#person_name").val();
	    	param.type							= $("input[name='x']:checked").val();
	    	param.call_vote						= $("input[name='xx']:checked").val();
	    	param.results_id					= $("#results_id").val();
	    	param.information_category_id		= $("#information_category_id").val();
	    	param.information_sub_category_id	= $("#information_sub_category_id").val();
	    	param.content_id					= $("#content_id").val();
	    	param.product_id					= $("#product_id").val();
	    	param.forward_id					= $("#forward_id").val();
	    	param.connect						= $("#connect:checked").val();
	    	param.results_comment				= $("#results_comment").val();
	    	param.content						= $("#content").val();
	    	param.task_type_id					= $("#task_type_id").val();
	    	param.task_department_id			= $("#task_department_id").val();
	    	param.persons_id					= $("#persons_id").val();
	    	param.comment						= $("#comment").val();
	    	param.source_id						= $("#source_id").val();
	    	
	    	// Personal Info
	    	param.personal_phone				= $("#personal_phone").val();
	    	param.personal_id					= $("#personal_id").val();
	    	param.personal_contragent			= $("#personal_contragent").val();
	    	param.personal_mail					= $("#personal_mail").val();
	    	param.personal_addres				= $("#personal_addres").val();
	    	param.personal_status				= $("#personal_status").val();
	    	
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
		        url: 'AsteriskManager/liveState.php',
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

	    $(document).on("click", ".insert", function () {
	    	var phone = $(this).attr("number");
	    	console.log(phone);
	    	if(phone != ""){
	    		$('#phone').val(phone);
		    }
	    });


	    $(document).on("change", "#information_category_id",function(){
		    var information_category_id = $("#information_category_id").val();
		    param 			= new Object();
		    param.act		= "category_change";
		    param.information_category_id_check		= information_category_id;
 	    	$.ajax({
 		    url: aJaxURL,
 			data: param,
 		    success: function(data) {
 				if(typeof(data.error) != 'undefined'){
 					if(data.error != ''){
 						alert(data.error);
 					}else{
 						$("#information_sub_category_id").html(data.cat);
 					}
 				}
 			}
 		    });
        });

    	$(document).on("change", "#category_parent_id",function(){
     	 	param 			= new Object();
 		 	param.act		= "sub_category";
 		 	param.cat_id   	= this.value;
 	    	$.ajax({
 		        url: aJaxURL,
 			    data: param,
 		        success: function(data) {
 					if(typeof(data.error) != 'undefined'){
 						if(data.error != ''){
 							alert(data.error);
 						}else{
 							$("#category_id").html(data.cat);
 						}
 					}
 			    }
 		    });
        });

    	$(document).on("change", "#category_id",function(){
			if(this.value == 423){
				$(".friend").removeClass('hidden');
			}else{
				$(".friend").addClass('hidden');
			}
        });

	    $(document).on("click", "#refresh-dialog", function () {
    	 	param 			= new Object();
		 	param.act		= "get_calls";

	    	$.ajax({
		        url: aJaxURL,
			    data: param,
		        success: function(data) {
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							$("#last_calls").html(data.calls);
							$( ".insert" ).button({
							      icons: {
							        primary: "ui-icon-plus"
							      }
							});
						}
					}
			    }
		    });

	    });
//
	    $(document).on("keydown", "#personal_pin", function(event) {
            if (event.keyCode == $.ui.keyCode.ENTER) {

            	param 			= new Object();
    		 	param.act		= "get_add_info";
    		 	param.pin		= $("#personal_pin").val();

    	    	$.ajax({
    		        url: aJaxURL,
    			    data: param,
    		        success: function(data) {
    					if(typeof(data.error) != 'undefined'){
    						if(data.error != ''){
    							alert(data.error);
    						}else{
    							$("#additional_info").html(data.info);
    						}
    					}
    			    }
    		    });
                
                event.preventDefault();
            }
        });
//
	    $(document).on("keydown", "#personal_id", function(event) {
            if (event.keyCode == $.ui.keyCode.ENTER) {

            	param 					= new Object();
    		 	param.act				= "get_add_info1";
    		 	param.personal_id		= $("#personal_id").val();

    	    	$.ajax({
    		        url: aJaxURL,
    			    data: param,
    		        success: function(data) {
    					if(typeof(data.error) != 'undefined'){
    						if(data.error != ''){
    							alert(data.error);
    						}else{
    							$("#additional_info").html(data.info1);
    						}
    					}
    			    }
    		    });
                
                event.preventDefault();
            }
        });

	    function LoadDialogCalls(){
			var button = {
               		"save": {
               			text: "განახლება",
               			id: "refresh-dialog",
               			click: function () {
               			}
               		}
				};

			/* Dialog Form Selector Name, Buttons Array */
			GetDialogCalls('last_calls', 330, 550, button);
		}

    </script>
</head>

<body>

<table style="width: 1100px; margin: 0 0 0 100px; padding-top:25px; display: block;">
		<tr style="width: 800px">
			<td>
            	<h2 align="center">შემომავალი ზარები</h2>
            	<div id="button_area">
        			<button id="add_button">დამატება</button>
        		</div>
                <table class="display" id="example">
                    <thead>
                        <tr id="datatable_header">
                            <th>ID</th>
                            <th style="width: 50px;" >№</th>
                            <th style="width: 150px;">თარიღი</th>
                            <th style="width: 150px;">კატეგორია</th>
                            <th style="width: 150px;">ტელეფონი</th>
                            <th style="width: 150px;">შინაარსი</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr class="search_header">
                            <th class="colum_hidden">
                            	<input type="text" name="search_id" value="ფილტრი" class="search_init" style=""/>
                            </th>
                            <th>
                            	<input style="width: 30px;" type="text" name="search_number" value="" class="search_init" style=""></th>
                            <th>
                                <input type="text" name="search_date" value="ფილტრი" class="search_init" style="width: 100px;"/>
                            </th>                            
                            <th>
                                <input type="text" name="search_category" value="ფილტრი" class="search_init" style="width: 80px;" />
                            </th>
                            <th>
                                <input type="text" name="search_phone" value="ფილტრი" class="search_init" style="width: 90px;"/>
                            </th>
                            <th>
                                <input type="text" name="search_category" value="ფილტრი" class="search_init" style="width: 90px;" />
                            </th>
                            
                        </tr>
                    </thead>
                </table>
	            <div class="spacer">
	         </div>
	    <td style="width: 20px;">
		   &nbsp;
		</td>
		<td style="width: 450px;">
		   <div id="jq" style="width: 450px; position: fixed;"></div>
		</td>
	</tr>
</table>

    <!-- jQuery Dialog -->
    <div  id="add-edit-form" class="form-dialog" title="შემომავალი ზარი">
	</div>

	<!-- jQuery Dialog -->
	<div id="last_calls" title="ბოლო ზარები">
	</div>
</body>
