<html>
<head>
	<script type="text/javascript">
		var aJaxURL				= "server-side/info/calls.action.php";									//server side folder url
		var object_aJaxURL		= "server-side/info/calls/object_list.action.php";						//server side folder url
		var object_calls_aJaxURL= "server-side/info/calls/object_calls/object_calls_list.action.php";	//server side folder url
		var seoyURL				= "server-side/seoy/seoy.action.php";										//server side folder url
		var tName				= "example";															//table name
		var fName				= "add-edit-form";														//form name
		
		$(document).ready(function () {
			
			LoadTable(tName);	
			GetButtons("add_client_call", "");			
			SetEventsOwn("add_client_call",tName, fName, aJaxURL);
		});

        //SeoYyy
		$(document.body).click(function (e) {
        	$("#per_contact_person").autocomplete("close");
        	$("#client_object").autocomplete("close");
        	     	
        });
        
		//SeoYyy
        $(document).on("click", ".combobox", function (event) {
	    	var i = $(this).text();
			$("#" + i).autocomplete( "search", "" );
		});
        
		function LoadTable(table){
			switch(table){
				case 'object_list':
					var id	= $("#client_id").val();
					var date= $("#date").val();
					
					/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
					GetDataTable("object_list", object_aJaxURL, "get_list", 6, "id=" + id+ "&date="+date, 0, "", 0, "desc");
					break;
				default:
					/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
	                GetDataTable(tName, aJaxURL, "get_list", 5, "", 0, "", 0, "desc");	
			}
		}
		
		function LoadDialog(form){
			switch (form) {
				case "add-edit-call-form" :
					var buttons = {
				        "save": {
				            text: "შენახვა",
				            id: "save-client_call",
				            click: function () {
				            }
				    	},						
				        "cancel": {
				            text: "დახურვა",
				            id: "cancel-object",
				            click: function () {
					            $(this).dialog("close");
				    		}
						}
					};						
					/* Dialog Form Selector Name, Buttons Array */
					GetDialog("add-edit-call-form", 550, "auto", buttons);
					SeoY("client_object", seoyURL, "client_objects", "cid="+$("#client_id").val(), 0);				
	
			    	SeoY("per_contact_person", seoyURL, "per_contact_person", "cid=" + $("#client_id").val(), 0);
			    	GetDateTimes("call_date");
					$("#per_phone_number").attr("disabled", "disabled");
					$("#per_mail").attr("disabled", "disabled");

					$(document).on("click", "#per_contact_person-widget", function () {
						var pers_name = $("#per_contact_person").val();
					    $.ajax({
					        url: object_aJaxURL,
						    data: "act=getperson&n="+pers_name,
					        success: function(data) {
								if(typeof(data.error) != "undefined"){
									if(data.error != ""){
										alert(data.error);
									}else{
										$("#per_phone_number").val(data.phone);
										$("#per_mail").val(data.email);			
										SeoY("per_contact_person", seoyURL, "per_contact_person", "cid=" + "თიბისი ბანკი(თამარ მეფის ფილიალი)", 0);						
									}
								}
						    }
					    });
						
					});		    
					
											
					break;
				default:
					var buttons = {
					        "save": {
					            text: "შენახვა",
					            id: "save-client",
					            click: function () {
					            }
					    	},						
					        "cancel": {
					            text: "დახურვა",
					            id: "cancel-object",
					            click: function () {
						            $(this).dialog("close");
						            LoadTable(tName);
					    	}
						}
					};						
					/* Dialog Form Selector Name, Buttons Array */
					GetDialog(form, 960, "auto", buttons);
					LoadTable("object_list");
					SetEditEnvent("object_list", "add-edit-call-form", aJaxURL);
					GetButtons("add_object_call", "");

				    $("#"+form).on( "dialogclose", function() {
				    	$("#client_id").val("");						
				    });
										
			 }
		}

		function SetEventsOwn(add, tname, fname, aJaxURL) {
		    $("#" + add).on("click", function () {
		        $.ajax({
		            url: aJaxURL,
		            type: "POST",
		            data: "act=get_add_page",
		            dataType: "json",
		            success: function (data) {
		                if (typeof (data.error) != "undefined") {
		                    if (data.error != "") {
		                        alert(data.error);
		                    } else {
		                        $("#add-edit-call-form").html(data.page);
		                        if ($.isFunction(window.LoadDialog)) {
		                            //execute it
		                            LoadDialog("add-edit-call-form");
		                        }
		                    }
		                }
		            }
		        });
		    });

		    /* Edit Event */
		    $("#" + tname + " tbody").on("dblclick", "tr", function () {
		        var nTds = $("td", this);
		        var empty = $(nTds[0]).attr("class");

		        if (empty != "dataTables_empty") {
		            var rID 	= $(nTds[0]).text();
		            var rDate	= $(nTds[1]).text();

		            $.ajax({
		                url: aJaxURL,
		                type: "POST",
		                data: "act=get_edit_page&id=" + rID + "&date=" + rDate,
		                dataType: "json",
		                success: function (data) {
		                    if (typeof (data.error) != "undefined") {
		                        if (data.error != "") {
		                            alert(data.error);
		                        } else {
		                            $("#" + fname).html(data.page);
		                            if ($.isFunction(window.LoadDialog)) {
		                                //execute it
		                                LoadDialog(fname);
		                            }
		                        }
		                    }
		                }
		            });
		        }
		    });
		}

		function GetClientID(client_name){
			var client_id;
			$.ajax({
		        url: aJaxURL,
        		async: false,
			    data: "act=get_client_id&cn=" + client_name,
		        success: function(data) {
					if(typeof(data.error) != "undefined"){
						if(data.error != ""){
							alert(data.error);
						}else{
							client_id = data.client_id;
						}
					}
			    }
		    });
	      	return client_id;
		}

		function SetEditEnvent(tname, fname, aJaxURL){
		    /* Edit Event */
		    $("#" + tname + " tbody").on("dblclick", "tr", function () {
		        var nTds = $("td", this);
		        var empty = $(nTds[0]).attr("class");

		        if (empty != "dataTables_empty") {
		            var rID 	= $(nTds[0]).text();

		            $.ajax({
		                url: aJaxURL,
		                type: "POST",
		                data: "act=get_add_page&id="+rID,
		                dataType: "json",
		                success: function (data) {
		                    if (typeof (data.error) != "undefined") {
		                        if (data.error != "") {
		                            alert(data.error);
		                        } else {
		                            $("#" + fname).html(data.page);
		                            if ($.isFunction(window.LoadDialog)) {
		                                //execute it
		                                LoadDialog(fname);
		                            }
		                        }
		                    }
		                }
		            });
		        }
		    });			
		}	

	    // Add - Save Object
	    $(document).on("click", "#save-client_call", function () {
	    	param = new Object();
	    	
            //Action
	    	param.act	= "save_client_call";		    

		    param.id	= $("#servise_degree_id").val();
		    param.cobn	= $("#client_object").val();  
		    param.p		= $("#persons").val();
		    
		    param.d		= $("#service_degree").val();
		    param.dat	= $("#call_date").val();
		    param.c		= $("#per_comment").val();
		    
		    if(param.dat == ""){
		    	alert("შეავსეთ ზარის თარიღი!");
	    	}else if(param.cobn == ""){
				alert("შეავსეთ ობიექტის სახელი!");
			}else if(param.p == ""){
				alert("შეავსეთ საკონტაქტო პირის სახელი!");
			}else if(param.d == "" && param.d == 0){
				alert("შეავსეთ მომსახურების ხარისხი!");
			}else{
			    $.ajax({
			        url: object_aJaxURL,
				    data: param,
			        success: function(data) {
						if(typeof(data.error) != "undefined"){
							if(data.error != ""){
								alert(data.error);
							}else{
				        		$("#add-edit-call-form").dialog("close");
								LoadTable("object_list");				        		
			        			LoadTable(tName);
							}
						}
				    }
			    });
			}
		});

    	$(document).on("click", "#add_object_call", function () {		    
	        $.ajax({
	            url: aJaxURL,
	            type: "POST",
	            data: "act=get_add_page",
	            dataType: "json",
	            success: function (data) {
	                if (typeof (data.error) != "undefined") {
	                    if (data.error != "") {
	                        alert(data.error);
	                    } else {
	                        $("#add-edit-call-form").html(data.page);
	                        if ($.isFunction(window.LoadDialog)) {
	                            //execute it
	                            LoadDialog("add-edit-call-form");
	                        }
	                    }
	                }
	            }
	        });
	    });

		

    	$(document).on("click", "#save-client", function () {
	    	param = new Object();
	    	
            //Action
	    	param.act	= "save_client";		    

		    param.cid	= $("#client_id").val();
		    param.ccom	= $("#client_comment").val();  
		    param.date	= $("#date").val();	
		        
	        $.ajax({
	            url: aJaxURL,
	            type: "POST",
	            data: param,
	            dataType: "json",
	            success: function (data) {
	                if (typeof (data.error) != "undefined") {
	                    if (data.error != ""){
	                        alert(data.error);
	                    }else{
	                    	$("#"+fName).dialog("close");
	                    	LoadTable(tName);		                    
	                    }
	                }
	            }
	        });
	    });

    	$(document).on("autocompleteclose", "#client_object_seoy", function (event, ui) {

				var client_name = $("#client_object").val();
				
				$("#client_name").val(client_name);

				GetClientPersons(client_name);
				$(".inner-table").find(":input").prop("disabled", false);
				$("#client_object").attr("disabled", "disabled");	
				$("#client_object_btn").attr("disabled", "disabled");												
				event.preventDefault();	
		});

    	function GetClientPersons(client_name){
			$.ajax({
		        url: aJaxURL,
			    data: "act=get_client_persons&cn=" + client_name,
		        success: function(data) {
					if(typeof(data.error) != "undefined"){
						if(data.error != ""){
							alert(data.error);
						}else{
							$("#persons").html(data.client_persons);
						}
					}
			    }
		    });
		}

    	$(document).on("change", "#persons", function() {
    		var person_id =  $("#persons").val();
    		
    		$.ajax({
		        url: aJaxURL,
			    data: "act=get_client_persons_info&person_id=" + person_id,
		        success: function(data) {
					if(typeof(data.error) != "undefined"){
						if(data.error != ""){
							alert(data.error);
						}else{
							$("#per_phone_number").val(data.client_info[0]);
							$("#per_mail").val(data.client_info[1]);
						}
					}
			    }
		    });
  		});

    </script>
</head>

<body>
    <div id="dt_example" class="ex_highlight_row">
        <div id="container">
            <div id="dynamic">
            	<h2 align="center">ზარები</h2>
	        	<div id="button_area">
	        		<button id="add_client_call">დამატება</button>
	        	</div>
                <table class="display" id="example">
                    <thead>
                        <tr id="datatable_header">
                            <th>ID</th>
                            <th class="min">თარიღი</th>
                            <th style="width: 100%">კლიენტი</th>                            
                            <th style="width: 100%">შენიშვნა</th>
                            <th class="min">ზოგადი<br>მომსახურების<br>ხარისხი</th>                            
                        </tr>
                    </thead>
                    <thead>
                        <tr class="search_header">
                            <th class="colum_hidden">
                            	<input type="text" name="id" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_name" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_address" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_phone" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                            </th>                            
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="spacer">
            </div>
        </div>
    </div>
    
    <!-- jQuery Dialog -->
    <div id="add-edit-form" class="form-dialog" title="ობიექტები">
    	<!-- aJax -->
	</div>
	<!-- jQuery Dialog -->
    <div id="add-edit-object-form" class="form-dialog" title="ზარები">
        <!-- aJax -->
    </div>	
    <div id="add-edit-call-form" class="form-dialog" title="განხორციელებული ზარი">
        <!-- aJax -->
    </div>	    		
</body>
</html>