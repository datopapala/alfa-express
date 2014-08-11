<html>
<head>
	<script type="text/javascript">
		var aJaxURL				= "server-side/info/client.action.php";									//server side folder url
		var object_aJaxURL		= "server-side/info/client/object_list.action.php";						//server side folder url
		var person_aJaxURL		= "server-side/info/client/object_list/object_person_list.action.php";	//server side folder url
		var printer_aJaxURL		= "server-side/info/client/printer_list.action.php";					//server side folder url
		var cartridge_aJaxURL	= "server-side/info/client/cartridge_list.action.php";					//server side folder url
		var timetable_aJaxURL	= "server-side/info/client/timetable_list.action.php";					//server side folder url
		var seoyURL 			= "server-side/seoy/seoy.action.php";									//server side folder url
		var upJaxURL			= "server-side/upload/file.action.php";									//server side folder url
		var tName				= "example";															//table name
		var fName				= "add-edit-form";														//form name
		var img_name			= "0.jpg";
		
		$(document).ready(function () {
			ClearDB();
			
			LoadTable(tName);
						
			/* Add Button ID, Delete Button ID */
			GetButtons("add_button", "delete_button", "", "", "clear_button");
			
			SetEvents("add_button", "delete_button", "check-all", tName, fName, aJaxURL);
		});
		
        //SeoYyy
		$(document.body).click(function (e) {
        	$("#cartridge_name").autocomplete("close");
        	$("#obj_address").autocomplete("close");
        	$("#postal_code").autocomplete("close");        	
        });

		$(document).on("click", "#postal_code", function () {
			var add = $("#obj_address").val();
			SeoY("postal_code", seoyURL, "postal_codes", "add="+ add, 0);
		});

		$(document).on("click", "#postal_code_btn", function () {
			var add = $("#obj_address").val();
			$("#postal_code").val("");			
			SeoY("postal_code", seoyURL, "postal_codes", "add="+ add, 0);
		});
        
		function LoadTable(table){	

	
			if(table == "printer_price"){
				var total=[20,21,22,23,24];
				var menuLength	= [[10], [10]];
				var local_id	= $("#local_client_id").val();
				/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
				GetDataTable("printer_price", aJaxURL, "get_printer_price", 25, "local_id=" + local_id, 0, menuLength, 0, "desc", total);
				$("#search_printer_model").css("width", "85%");	
			}else if(table == "cartridzge_price"){	
				var total=[20,21,22,23,24];
				var menuLength	= [[10], [10]];
				var local_id	= $("#local_client_id").val();
				/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
				GetDataTable("cartridzge_price", aJaxURL, "get_cartridzge_price", 25, "local_id=" + local_id, 0, menuLength, 0, "desc", total);
				$("#search_catridge_model").css("width", "85%");			
			}else if(table == "timetable_list"){
				var menuLength	= [[10], [10]];
				var local_id	= $("#local_client_id").val();
				
				/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
				GetDataTable("timetable_list", timetable_aJaxURL, "get_list", 5, "local_id=" + local_id, 0, menuLength, 0, "desc");
			}else if(table == "object_list"){
				var menuLength	= [[10], [10]];
				var local_id	= $("#local_client_id").val();
				
				/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
				GetDataTable("object_list", object_aJaxURL, "get_list", 5, "local_id=" + local_id, 0, menuLength, 0, "desc");
			}else if(table == "person_list"){
				var menuLength	= [[10], [10]];
				var local_id	= $("#object_list_id").val();
				
				/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
				GetDataTable("person_list", person_aJaxURL, "get_list", 4, "local_id=" + local_id, 0, menuLength);
			}else if(table == "printer_list"){
				var menuLength	= [[10], [10]];
				var local_id	= $("#local_client_id").val();
				
				/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
				GetDataTable("printer_list", printer_aJaxURL, "get_list", 4, "local_id=" + local_id, 0, menuLength, 0, "desc");
			}else if(table == "cartridge_list"){
				var menuLength	= [[10], [10]];
				var local_id	= $("#local_client_id").val();
				
				/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
				GetDataTable("cartridge_list", cartridge_aJaxURL, "get_list", 3, "local_id=" + local_id, 0, menuLength, 0, "desc");
			}else{
				/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
                GetDataTable(tName, aJaxURL, "get_list", 7, "", 0, "", 0, "desc");
			}
		}
		
		function LoadDialog(form){
			switch (form) {
				case "add-edit-timetable-form":
					var buttons = {
				        "save": {
				            text: "შენახვა",
				            id: "save-timetable",
				            click: function () {
				    		}
						},						
				        "cancel": {
			                text: "დახურვა",
			                id: "cancel-dialog",
			                click: function () {
			                	$(this).dialog("close");
			                }
			            }
					};
					GetDialog("add-edit-timetable-form", 700, "auto", buttons);	

					if( $("#request_time_type").val() != "0" ){
						$("#accordion").accordion({
							collapsible: true,
							beforeActivate: function(event, ui) {
								$("#cartridge_done_hours").attr("disabled",  !$("#cartridge_done_hours").is(":disabled") );
								$("#cartridge_done_minutes").attr("disabled",  !$("#cartridge_done_minutes").is(":disabled") );
								$("#cartridge_done_hours").val("");	
								$("#cartridge_done_minutes").val("");
								$("#printer_done_hours").attr("disabled",  !$("#printer_done_hours").is(":disabled") );
								$("#printer_done_minutes").attr("disabled",  !$("#printer_done_minutes").is(":disabled") );
								$("#printer_done_hours").val("");	
								$("#printer_done_minutes").val("");				      
						    }						
						});
						$("#cartridge_done_hours").attr("disabled","true");
						$("#cartridge_done_minutes").attr("disabled","true");	
						$("#printer_done_hours").attr("disabled","true");
						$("#printer_done_minutes").attr("disabled","true");	
					}else{
						$("#accordion").accordion({
							active: false,
							collapsible: true,
							heightStyle: "content",
							beforeActivate: function(event, ui) {
								$("#cartridge_done_hours").attr("disabled",  !$("#cartridge_done_hours").is(":disabled") );
								$("#cartridge_done_minutes").attr("disabled",  !$("#cartridge_done_minutes").is(":disabled") );
								$("#cartridge_done_hours").val("");	
								$("#cartridge_done_minutes").val("");
								$("#printer_done_hours").attr("disabled",  !$("#printer_done_hours").is(":disabled") );
								$("#printer_done_minutes").attr("disabled",  !$("#printer_done_minutes").is(":disabled") );
								$("#printer_done_hours").val("");	
								$("#printer_done_minutes").val("");				      
						    }						
						});							
					}	

					var id = $("#timetable_id").val();
					if( empty(id) ){
						id = GetLocalID1();
						$("#timetable_id").val(id);
					}
								
					break;
				case "planned-form":
							var buttons = {
						        "save": {
						            text: "შენახვა",
						            id: "save-planned-quantity",
						            click: function () {
						    		}
								},
						        "cancel": {
					                text: "დახურვა",
					                id: "cancel-dialog",
					                click: function () {
					                	$(this).dialog("close");
					                }
					            }
							};
					GetDialog("planned-form", 500, "auto", buttons);
																	
					break; 
				case "add-edit-object-form":
					var id = $("#object_list_id").val();
					if(empty(id)){
						var buttons = {
					        "save": {
					            text: "ობიექტის დამატება",
					            id: "save-object",
					            click: function () {
					            }
					        }
					    };
					    
						GetButtons("add_button_person", "delete_button_person");
						$("#add_button_person").button("disable");
						$("#delete_button_person").button("disable");
						
						LoadTable("person_list");
						SetEvents("", "", "check-all-person", "person_list", "add-edit-person-form", person_aJaxURL);
					}else{
						var buttons = {
						        "save": {
						            text: "ობიექტის შენახვა",
						            id: "save-object",
						            click: function () {
						    	}
							}
						};
						
						GetButtons("add_button_person", "delete_button_person");
						LoadTable("person_list");
						SetEvents("add_button_person", "delete_button_person", "check-all-person", "person_list", "add-edit-person-form", person_aJaxURL);
					}
					GetDialog(form, 700, "auto", buttons);
					SeoY("obj_address", seoyURL, "obj_address", "", 0);
					SeoY("postal_code", seoyURL, "postal_codes", "add=", 0);
					
				break;
				case "add-edit-person-form":
					var id = $("#object_person_list_id").val();
					if(empty(id)){
						var buttons = {
					        "save": {
					            text: "საკონტაქტო პირის დამატება",
					            id: "save-person",
					            click: function () {
					            }
					        }
					    };
					    
						GetButtons("add_button_person", "delete_button_person");
						
						LoadTable("person_list");
						SetEvents("", "", "check-all-person", "person_list", "add-edit-person-form", person_aJaxURL);
					}else{
						var buttons = {
						        "save": {
						            text: "საკონტაქტო პირის შენახვა",
						            id: "save-person",
						            click: function () {
						    	}
							}
						};
						
						GetButtons("add_button_person", "delete_button_person");
						LoadTable("person_list");
						SetEvents("add_button_person", "delete_button_person", "check-all-person", "person_list", "add-edit-person-form", person_aJaxURL);
					}
					GetDialog(form, 700, "auto", buttons);
					
				break;
				case "add-edit-printer-form":
					SeoY("obj_district", seoyURL, "printer_pricelist", "", 0);
					
					var id = $("#object_list_id").val();
					if(empty(id)){
						var buttons = {
					        "save": {
					            text: "დამატება",
					            id: "save-printer",
					            click: function () {
					            }
					        }
					    };
					}else{
						var buttons = {
						        "save": {
						            text: "შენახვა",
						            id: "save-printer",
						            click: function () {
						            }
						        }
						    };
					}
					
					GetDialog(form, 500, "auto", buttons);
					SeoY("printer_name", seoyURL, "printer_pricelist", "", 0);
					break;
				case "add-edit-cartridge-form":
					var prod_name = $("#cartridge_name").val();
					var local_id = $("#local_client_id").val();
					
					SeoY("cartridge_name", seoyURL, "cartridge_pricelist", "id=" + local_id, 0);
					
					var id = $("#cartridge_list_id").val();
					if(empty(id)){
						var buttons = {
					        "save": {
					            text: "დამატება",
					            id: "save-cartridge",
					            click: function () {
					            }
					        }
					    };
					}else{
		                $("#cartridge_name").attr("disabled", "disabled");
		                $("#prod_name_btn").button("disable");
						var buttons = {
						        "save": {
						            text: "შენახვა",
						            id: "save-cartridge",
						            click: function () {
						            }
						        }
						};
					}
					
					GetPartsTable(prod_name, local_id);
					
					GetDialog(form, 500, "auto", buttons);
					break;
					default:
						ClearDB();
						
						var id			= $("#client_id").val();
						var local_id = id;
						if( empty(id) ){
							var local_id	= GetLocalID();
						}
									
						if(empty(id)){
							$("#local_client_id").val(local_id);
							var min_am = $("#min_amount").val();
							if(empty(min_am)){
								$("#min_amount").val(1);
							}
		
							$("#tabs").tabs({disabled: [1,2,3,4]});
						}else{
							$("#local_client_id").val(id);
							$("#tabs").tabs({disabled: [1]});
						}
						
						$("#choose_button").button({
				            icons: {
				                primary: "ui-icon-arrowreturnthick-1-n"
				            }
			        	});
			        	
						$("#upload_button").button({
				            icons: {
				                primary: "ui-icon-arrowreturnthick-1-n"
				            }
			        	});

						var img_url	= $("#upload_img").attr("src");
				    	img_name	= img_url.split("\/")[4]; //Get image name element 4
				    	if(img_name != "0.jpg"){
				    		$("#choose_button").button("disable");
				    	}
						
						/* Dialog Form Selector Name, Buttons Array */
						GetDialog(form, 960, "auto", "");
						
						GetTabs("tabs");

						$(document).on("click", "#3", function () {
							GetButtons("add_button_object", "delete_button_object");
							LoadTable("object_list");
							SetEvents("add_button_object", "delete_button_object", "check-all-object", "object_list", "add-edit-object-form", object_aJaxURL);			
						});	

						$(document).on("click", "#4", function () {
							GetButtons("add_button_printer", "delete_button_printer");
							LoadTable("printer_list");
							SetEvents("add_button_printer", "delete_button_printer", "check-all-printer", "printer_list", "add-edit-printer-form", printer_aJaxURL);
						});

						$(document).on("click", "#5", function () {
							GetButtons("add_button_cartridge", "delete_button_cartridge");
							LoadTable("cartridge_list");
							SetEvents("add_button_cartridge", "delete_button_cartridge", "check-all-cartridge", "cartridge_list", "add-edit-cartridge-form", cartridge_aJaxURL, "lid=" + id);
						});

						$(document).on("click", "#6", function () {
							LoadTable("cartridzge_price");
							LoadTable("printer_price");																			
						});


						$(document).on("click", "#7", function () {
							GetButtons("add_cartridge_timetable", "delete_timetable");
							GetButtons("add_printer_timetable", "");
							LoadTable("timetable_list");
							SetEvents("add_cartridge_timetable", "delete_timetable", "check-all-timetable", "timetable_list", "add-edit-timetable-form", timetable_aJaxURL, "lid=" + $("#local_client_id").val());							
						});
						
					    $("#cartridzge_price_tbody").on("dblclick", "tr", function () {
					        var nTds = $("td", this);
					        var empty = $(nTds[0]).attr("class");

					        if (empty != "dataTables_empty") {
					            var rID = $(nTds[0]).text();
						    	param = new Object();
						    	
					            //Action
						    	param.act	= "get_planned_quantity";							    
							    param.id	= rID;
							    param.cid	= $("#client_id").val();							    
							    param.t		= 0;
					            
							    $.ajax({
							        url: aJaxURL,
								    data: param,
							        success: function(data) {
										if(typeof(data.error) != "undefined"){
											if(data.error != ""){
												alert(data.error);
											}else{
												$("#planned-form").html(data.page);
												LoadDialog("planned-form");																							
											}
										}
								    }
							    });					            
					        }
					    });
					    
					    $("#printer_price_tbody").on("dblclick", "tr", function () {
					        var nTds = $("td", this);
					        var empty = $(nTds[0]).attr("class");

					        if (empty != "dataTables_empty") {
					            var rID = $(nTds[0]).text();
						    	param = new Object();
						    	
					            //Action
						    	param.act	= "get_planned_quantity";
							    param.id	= rID;
							    param.cid	= $("#client_id").val();
							    param.t		= 1;
					            				            
							    $.ajax({
							        url: aJaxURL,
								    data: param,
							        success: function(data) {
										if(typeof(data.error) != "undefined"){
											if(data.error != ""){
												alert(data.error);
											}else{
												$("#planned-form").html(data.page);	
												LoadDialog("planned-form");																						
											}
										}
								    }
							    });					            
					        }
					    });

						
			 }
		}
		
	    // Add - Save
	    $(document).on("click", "#save-dialog", function () {
	    	param = new Object();
	    	
            //Action
	    	param.act		= "save_client";		    
		    
		    param.id		= $("#client_id").val();
		    
		    param.ri		= $("#rs_id").val();
		    param.n			= $("#name").val();
		    param.a			= $("#address").val();
		    param.ls		= $("#legal_status").val();
		    param.pm		= $("#pay_method").val();
		    param.vp		= $("#vat_payer").val();
		    
		    param.cp		= $("#contact_person").val();
		    param.pn		= $("#phone_number").val();
		    param.m			= $("#mail").val();
		    
		    param.c			= $("#comment").val();
		    
            //Image
		    param.img 		= img_name;
		    
		    var allow = true;
			if(param.vp == 0){
				if(!confirm("დარწმუნებული ხართ რომ კლიენტი არ არის დღგ-ს გადამხდელი?")) {						
					allow = false;
				}
			}
			
			if(allow){
		    	if(param.ri == ""){
					alert("შეავსეთ საიდენტიფიკაციო ნომერი!");
				}else if(param.n == ""){
					alert("შეავსეთ კომპანიის სახელი!");
				}else {
				    $.ajax({
				        url: aJaxURL,
					    data: param,
				        success: function(data) {
							if(typeof(data.error) != "undefined")
							{
								if(data.error != "")
								{
									alert(data.error);
								}else
								{
									LoadTable(fName);
					        		CloseDialog(fName);
								}
							}
					    }
				    });
				}
			}
		});

	    // Add - Save Object
	    $(document).on("click", "#save-object", function () {
	    	param = new Object();
	    	
            //Action
	    	param.act		= "save_object";		    

		    param.id		= $("#object_list_id").val();
		    param.lci		= $("#local_client_id").val();
		    
		    param.on		= $("#obj_name").val();
		    param.oct		= $("#obj_city").val();
		    
		    param.oan		= $("#obj_address").val();
		    param.oat		= $("#obj_address_type").val();
		    param.oanum		= $("#obj_address_number").val();
		    param.opc		= $("#postal_code").val();

	    		    
		    param.ocp		= $("#obj_contact_person").val();
		    param.opn		= $("#obj_phone_number").val();
		    param.om		= $("#obj_mail").val();
		    param.oc		= $("#obj_comment").val();
	    	
	    	if(param.on == ""){
				alert("შეავსეთ ობიექტის სახელი!");
			}else if(param.oan == "" && param.oct == 1){
				alert("შეავსეთ ობიექტის მისამართის სახელი!");
			}else if(param.oat == 0 && param.oct == 1){
				alert("შეავსეთ ობიექტის მისამართის ტიპი!");
			}else if(param.opc == "" && param.oct == 1){
				alert("შეავსეთ ობიექტის მისამართის ტიპი!");
			}else {
			    $.ajax({
			        url: object_aJaxURL,
				    data: param,
			        success: function(data) {
						if(typeof(data.error) != "undefined")
						{
							if(data.error != "")
							{
								alert(data.error);
							}else
							{
								LoadTable("object_list");
				        		CloseDialog("add-edit-object-form");
							}
						}
				    }
			    });
			}
		});
		
	    // Add - Save Object Persons
	    $(document).on("click", "#save-person", function () {
	    	param = new Object();
	    	
            //Action
	    	param.act		= "save_person";		    

		    param.id		= $("#object_person_list_id").val();
		    param.lci		= $("#object_list_id").val();
		    
		    param.pcp		= $("#per_contact_person").val();
		    param.ppn		= $("#per_phone_number").val();
		    param.pm		= $("#per_mail").val();
		    param.pc		= $("#per_comment").val();
	    	
	    	if(param.pcp == ""){
				alert("შეავსეთ საკონტაქტო პირის სახელი!");
			}else {
			    $.ajax({
			        url: person_aJaxURL,
				    data: param,
			        success: function(data) {
						if(typeof(data.error) != "undefined")
						{
							if(data.error != "")
							{
								alert(data.error);
							}else
							{
								LoadTable("person_list");
				        		CloseDialog("add-edit-person-form");
							}
						}
				    }
			    });
			}
		});
		
	    // Add - Save Printer
	    $(document).on("click", "#save-printer", function () {
	    	param = new Object();
	    	
            //Action
	    	param.act	= "save_printer";
	    	
		    param.id	= $("#printer_list_id").val();
		    param.lid	= $("#local_client_id").val();
		    
		    param.n		= $("#printer_name").val();
		    param.t		= $("#printer_type").val();
		    param.p		= $("#printer_price_p").val();

	    	if(param.n == ""){
				alert("შეავსეთ კარტრიჯის სახელი!");
			}else if(param.t == ""){
				alert("შეავსეთ ნაწილის ტიპი!");
			}else if(param.p == ""){
				alert("შეავსეთ კარტრიჯის ფასიიიი!");
			}else {
			    $.ajax({
			        url: printer_aJaxURL,
				    data: param,
			        success: function(data) {
						if(typeof(data.error) != "undefined")
						{
							if(data.error != "")
							{
								alert(data.error);
							}else
							{
								LoadTable("printer_list");
				        		CloseDialog("add-edit-printer-form");
							}
						}
				    }
			    });
			}
		});
		
	    // Add - Save Cartridge
	    $(document).on("click", "#save-cartridge", function () {
	    	param = new Object();
	    	
            //Action
	    	param.act	= "save_cartridge";
	    	
		    param.id	= $("#cartridge_list_id").val();
		    param.lid	= $("#local_client_id").val();
		    
		    param.n		= $("#cartridge_name").val();
		    
		    var array = new Array();
			
	    	$("#parts_table td input").each(function(){
	    		array.push(Array($(this).val(), $(this).attr("parts_id")));
	    	});
	    	
	    	param.list	= JSON.stringify(array);
	    	
	    	if(param.n == ""){
				alert("შეავსეთ კარტრიჯის სახელი!");
			}else if(param.t == ""){
				alert("შეავსეთ ნაწილის ტიპი!");
			}else if(param.p == ""){
				alert("შეავსეთ კარტრიჯის ფასი!");
			}else {
			    $.ajax({
			        url: cartridge_aJaxURL,
				    data: param,
			        success: function(data) {
						if(typeof(data.error) != "undefined")
						{
							if(data.error != "")
							{
								alert(data.error);
							}else
							{
								LoadTable("cartridge_list");
				        		CloseDialog("add-edit-cartridge-form");
							}
						}
				    }
			    });
			}
		});

		$(document).on("click", "#save-planned-quantity", function () {
	    	param = new Object();
	    	
            //Action
	    	param.act	= "save_planned_quantity";		
	    	param.type  = $("#planned_quantity_type").val();    
		    var array = new Array();			
	    	$("#planned_quantity td input").each(function(){
	    		array.push(Array($(this).val(), $(this).attr("cartridge_pricelist") ));
	    	});	    	
	    	param.list	= JSON.stringify(array);
	    	
		    $.ajax({
		        url: cartridge_aJaxURL,
			    data: param,
		        success: function(data) {
					if(typeof(data.error) != "undefined"){
						if(data.error != ""){
							alert(data.error);
						}else{
							if( param.type = 0){
								GetTable("cartridzge_price");
							}else{
								GetTable("printer_price");						
							}							
			        		CloseDialog("planned-form");
						}
					}
			    }
		    });			
		});

		//Register Enter Effect
		$(document).on("keydown", "#cartridge_name", function (event) {
			if (event.keyCode == $.ui.keyCode.ENTER){
				var prod_name = $("#cartridge_name").val();
				GetPartsTable(prod_name, "");
            }
		});
		
	    $(document).on("click", "#choose_button", function () {
		    $("#choose_file").click();
		});

	    $(document).on("click", "#add_printer_timetable", function () {
            $.ajax({
                url: timetable_aJaxURL,
                type: "POST",
                data: "act=get_add_page&type=" + 1 + "&id="+ $("#local_client_id").val(),
                dataType: "json",
                success: function (data) {
                    if (typeof (data.error) != "undefined") {
                        if (data.error != "") {
                            alert(data.error);
                        } else {
                            $("#add-edit-timetable-form").html(data.page);
                            if ($.isFunction(window.LoadDialog)) {
                                //execute it
                                LoadDialog("add-edit-timetable-form");
                            }
                        }
                    }
                }
            });	    
		});

	     
	    $(document).on("click", "#save-timetable", function () {
	    	param = new Object();
	    	
            //Action
	    	param.act	= "save_timetable";		
			param.id	= $("#timetable_id").val();
			param.cid	= $("#local_client_id").val();
			if(	$("#timetable_type").val() == 1 ){
				param.pdt	= $("#printer_done_hours").val()+":"+$("#printer_done_minutes").val();			
			}else{
				param.cdt	= $("#cartridge_done_hours").val()+":"+$("#cartridge_done_minutes").val();			
			}
			param.t_type= $("#timetable_type").val();
			param.rt	= $("#request_hours").val()+":"+$("#request_minutes").val();
			param.rtt	= $("#request_time_type").val();
			param.rminq	= $("#request_min_quantity").val();
			param.rmaxq	= $("#request_max_quantity").val();
			param.dt	= $("#done_hours").val()+":"+$("#done_minute").val();
			param.d		= $("#days").val();
				    	
		    $.ajax({
		        url: timetable_aJaxURL,
			    data: param,
		        success: function(data) {
					if(typeof(data.error) != "undefined"){
						if(data.error != ""){
							alert(data.error);
						}else{										
			        		CloseDialog("add-edit-timetable-form");
			        		LoadTable("timetable_list");
						}
					}
			    }
		    });			    		    
	    });  
		
	    $(document).on("change", "#choose_file", function () {
	    	var file		= $(this).val();	    
		    var name		= uniqid();
		    var path		= "../../media/uploads/images/client/";
		    
		    var ext = file.split('.').pop().toLowerCase();
	        if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) { //echeck file type
	        	alert('This is not an allowed file type.');
                this.value = '';
	        }else{
	        	img_name = name + "." + ext;
	        	$("#choose_button").button("disable");
	        	$.ajaxFileUpload({
	    			url: upJaxURL,
	    			secureuri: false,
	    			fileElementId: "choose_file",
	    			dataType: 'json',
	    			data:{
						act: "upload_file",
						path: path,
						file_name: name,
						type: ext
					},
	    			success: function (data, status){
	    				if(typeof(data.error) != 'undefined'){
    						if(data.error != ''){
    							alert(data.error);
    						}else{
    							$("#upload_img").attr("src", "media/uploads/images/client/" + img_name);
    						}
    					}
    				},
    				error: function (data, status, e)
    				{
    					alert(e);
    				}
    			});
	        }
		});
		
	    $(document).on("click", "#view_image", function () {
		    var src = $("#upload_img").attr("src");
		    $("#view_img").attr("src", src);
			var buttons = {
				"cancel": {
		            text: "დახურვა",
		            id: "cancel-dialog",
		            click: function () {
		                $(this).dialog("close");
		            }
		        }
		    };
	    	GetDialog("image-form", "auto", "auto", buttons);
		});
		
	    $(document).on("click", "#upload_img", function () {
		    var src = $("#upload_img").attr("src");
		    $("#view_img").attr("src", src);
			var buttons = {
				"cancel": {
		            text: "დახურვა",
		            id: "cancel-dialog",
		            click: function () {
		                $(this).dialog("close");
		            }
		        }
		    };
	    	GetDialog("image-form", "auto", "auto", buttons);
		});
		
	    $(document).on("click", "#delete_image", function () {
	    	var img_url	= $("#upload_img").attr("src");
	    	img_name	= img_url.split("\/")[4];	//Get image name element 4
	    	if(img_name != "0.jpg"){
		    	param = new Object();
		    	
	            //Action
		    	param.act		= "delete_file";
		    	
		    	param.path	 	= "../../media/uploads/images/client/";
			    param.file_name	= img_name;
			    var id			= $("#client_id").val();
			    
	            $.ajax({
	                url: upJaxURL,
	                data: param,
	                success: function(data) {
	                    if (typeof(data.error) != "undefined") {
	                        if (data.error != "") {
	                            alert(data.error);
	                        } else {
	                        	$("#choose_button").button("enable");
	                        	$("#upload_img").attr("src", "media/uploads/images/client/0.jpg");
	                        	if(!empty(id)){
	                        		DeleteImage(id);
		                        }
	                        }
	                    }
	                }
	            });
			}
		});
		
	    function DeleteImage(client_id) {
            $.ajax({
                url: aJaxURL,
                data: "act=delete_image&id=" + client_id,
                success: function(data) {
                    if (typeof(data.error) != "undefined") {
                    	if (data.error != "") {
                            alert(data.error);
                        } else{
                        	img_name = "0.jpg";                        	
                        }
                    }
                }
            });
        }
		
		function GetPartsTable(prod_name, local_id) {
	    	param = new Object();
	    	
            //Action
	    	param.act	= "get_parts_table";
	    	
	    	param.pn	= prod_name;
	    	param.lid	= local_id;
	    	
            $.ajax({
                url: cartridge_aJaxURL,
                data: param,
                success: function(data) {
                    if (typeof(data.error) != "undefined") {
                        if (data.error != "") {
                            alert(data.error);
                        } else {
                        	$("#parts_table").html(data.parts_table);
                        }
                    }
            	}
        	});
        }
		
		/**
		* Get next auto incremented id
		* @return {int}   Returns a next id
		*/
		function GetLocalID(){
			var local_id;
			$.ajax({
		        url: aJaxURL,
        		async: false, //r-value
			    data: "act=get_local_id",
		        success: function(data) {
					if(typeof(data.error) != "undefined"){
						if(data.error != ""){
							alert(data.error);
						}else{
							local_id = data.local_id;
						}
					}
			    }
		    });
	      	return local_id;
		}

		function GetLocalID1(){
			var local_id;
			$.ajax({
		        url: timetable_aJaxURL,
        		async: false, //r-value
			    data: "act=get_local_id",
		        success: function(data) {
					if(typeof(data.error) != "undefined"){
						if(data.error != ""){
							alert(data.error);
						}else{
							local_id = data.local_id;
						}
					}
			    }
		    });
	      	return local_id;
		}
		
		function CheckSubList(list_id) {
            var check = false;
            $.ajax({
                url: aJaxURL,
                async: false, //r-value
                data: "act=check_sub_list&id=" + list_id,
                success: function(data) {
                    if (typeof(data.error) != "undefined") {
                        if (data.error != "") {
                            alert(data.error);
                        } else {
                            check = true;
                        }
                    }
            	}
        	});
            return check;
        }
		
        function ClearDB() {
            $.ajax({
                url: aJaxURL,
                data: "act=clear_db",
                success: function(data) {
                    if (typeof(data.error) != "undefined") {
                        if (data.error != "") {
                            alert(data.error);
                        }
                    }
                }
            });
        }

        function GetTable(table){
            switch(table){
	            case 'cartridzge_price':
		                $.ajax({
		                    url: aJaxURL,
		                    data: "act=get_cartridzge_price&id=" + $("#client_id").val(),
		                    success: function(data) {
		                        if (typeof(data.error) != "undefined") {
		                            if (data.error != "") {
		                                alert(data.error);
		                            }else{
			                            $("#cartridzge_price_tbody").html(data.page);			                            
		                            }
		                        }
		                    }
		                });		            
	                break;
	            case 'printer_price':
	                $.ajax({
	                    url: aJaxURL,
	                    data: "act=get_printer_price&id=" + $("#client_id").val(),
	                    success: function(data) {
	                        if (typeof(data.error) != "undefined") {
	                            if (data.error != "") {
	                                alert(data.error);
	                            }else{
		                            $("#printer_price_tbody").html(data.page);
	                            }
	                        }
	                    }
	                });
	                break;                  
            }
        }
		
	    $(document).on("click", "#clear_button", function () {
	    	param = new Object();
	    	
			//Action
	    	param.act	= "get_file_list";
			param.path	= "../../media/uploads/images/client/";
			var file_list = "";
            $.ajax({
                url: upJaxURL,
                data: param,
                async: false, //r-value
                success: function(data) {
                    if (typeof(data.error) != "undefined") {
                        if (data.error != "") {
                            alert(data.error);
                        } else {
                        	file_list = data.file_list;
                        }
                    }
                }
            });
            	    	
            //Action
	    	param.act	= "clear";	    	
	    	param.file	= file_list;
	    	
	    	$.ajax({
                url: aJaxURL,
                data: param,
                async: false, //r-value
                success: function(data) {
                    if (typeof(data.error) != "undefined") {
                        if (data.error != "") {
                            alert(data.error);
                        } else {
                            alert("Database Clear Successfully Completed");
                            file_list = data.file_list;
                        }
                    }
                }
            });
	    	file_list = $.parseJSON(file_list);
            
	    	for (var i = 0; i < file_list.length; i++) {		    	
		    	param.act		= "delete_file";		    	
			    param.file_name	= file_list[i];
			    			    
	            $.ajax({
	                url: upJaxURL,
	                data: param,
	                success: function(data) {
	                    if (typeof(data.error) != "undefined") {
	                        if (data.error != "") {
	                            alert(data.error);
	                        }
	                    }
	                }
	            });
		    }
		});
        
		//--SeoY
	    $(document).on("click", ".combobox", function (event) {
	    	var i = $(this).text();
			$("#" + i).autocomplete( "search", "" );
		});
    </script>
</head>

<body>
    <div id="dt_example" class="ex_highlight_row">
        <div id="container">
            <div id="dynamic">
            	<h2 align="center">კლიენტები</h2>
	        	<div id="button_area">
	        		<button id="add_button">დამატება</button><button id="delete_button">წაშლა</button>
	        	</div>
                <table class="display" id="example">
                    <thead>
                        <tr id="datatable_header">
                            <th>ID</th>
                            <th style="width: 196px">დასახელება</th>
                            <th class="min">საიდენტ. ნომერი</th>
                            <th class="min">მისამართი</th>
                            <th class="min">ტელეფონი</th>
                            <th style="width: 165px">საკონტაქტო პირი</th>
                            <th class="min">ანგ. ფორმა</th>
                            <th class="check">#</th>
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
                                <input type="text" name="search_rs_id" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_address" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_phone" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_contact" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_pay_method" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                            	<input type="checkbox" name="check-all" id="check-all">
                            </th>
                        </tr>
                    </thead>
                </table>
	        	<div id="bottom_button_area">
	        		<button id="clear_button" class="right">clear</button>
	        	</div>
            </div>
            <div class="spacer">
            </div>
        </div>
    </div>
    
    <!-- jQuery Dialog -->
    <div id="add-edit-form" class="form-dialog" title="კლიენტები">
    	<!-- aJax -->
	</div>
    <div id="planned-form" class="form-dialog" title="გეგმიური ბრუნვა">
    	<!-- aJax -->
	</div>	
    <!-- jQuery Dialog -->
    <div id="image-form" class="form-dialog" title="პროდუქციის სურათი">
    	<img id="view_img" src="media/uploads/images/client/0.jpg">
	</div>
</body>
</html>