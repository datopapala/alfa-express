<html>
<head>
	<script type="text/javascript">
		var aJaxURL			= "server-side/info/partner.action.php";				//server side folder url
		var a_aJaxURL		= "server-side/info/partner/accounts.action.php";		//server side folder url
		var b_aJaxURL		= "server-side/info/partner/bank.action.php";			//server side folder url
		var c_aJaxURL		= "server-side/info/partner/cadre.action.php";			//server side folder url
		var seoyURL			= "server-side/seoy/seoy.action.php";					//server side folder url		
		var tName			= "example";											//table name
		var fName			= "add-edit-form";										//form name		
		
		$(document).ready(function () {
			     
			/*    load main table  */   	
			LoadTable(tName);
			/*    Add Button ID, Delete Button ID */  
			GetButtons("add_button", "delete_button");
			SetEvents("add_button", "delete_button", "check-all", tName, fName, aJaxURL);			
		});
		
		function LoadTable(table){
			var id		= $("#partner_id").val();
			var menuLength	= [[10], [10]];			
			switch (table) {
				case "acc_details":
				  	/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
					GetDataTable(table, a_aJaxURL, "get_list", 5, "partner_id=" + id, 0, menuLength, 1, "desc");
					$("#" + table).width("100%");
			    break;
				case "bank_details":
				  	/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
					GetDataTable(table, b_aJaxURL, "get_list", 5, "partner_id=" + id, 0, menuLength, 1, "desc");
					$("#" + table).width("100%");
			    break;
				case "cadre_details":
				  	/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
					GetDataTable(table, c_aJaxURL, "get_list", 5, "partner_id=" + id, 0, menuLength, 1, "desc");
					$("#" + table).width("100%");
			    break;
				default:
				    /* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
					GetDataTable(tName, aJaxURL, "get_list", 7, "", 0, "", 1, "asc");
			}
		}
		
		function LoadDialog(form){
			switch (form) {
				case "add-edit-acc-form":
					var buttons = {
				        "save": {
				            text:	"ანგარიშწორების დამატება",
				            id: 	"save_partner_accounts",
				            click: 	function () {
				            }
				        }
				    };
					GetButtons("add_acc_button", "delete_acc_button");
					SetEvents("add_acc_button", "delete_acc_button", "check-all-account", "acc_details", "add-edit-acc-form", a_aJaxURL);
					GetDialog(form, 440, "auto", buttons);
				
					
				break;
				case "add-edit-bank-form":
					var buttons = {
				        "save": {
				            text: "ბანკის დამატება",
				            id: "save_partner_bank",
				            click: function () {
				            }
				        }
				    };
					GetButtons("add_bank_button", "delete_bank_button");
					SetEvents("add_bank_button", "delete_bank_button", "check-all-bank", "bank_details", "add-edit-bank-form", b_aJaxURL);
					GetDialog(form, 440, "auto", buttons);
	
					
				break;
				case "add-edit-cadre-form":					
					var buttons = {
					        "save": {
					            text: "კადრის დამატება",
					            id: "save_partner_cadre",
					            click: function () {
					            }
					        }
					};
					GetButtons("add_cadre_button", "delete_cadre_button");
					SetEvents("add_cadre_button", "delete_cadre_button", "check-all-cadre", "cadre_details", "add-edit-cadre-form", c_aJaxURL);
					GetDialog(form, 440, "auto", buttons);			
					
				break;
				default:
					var id = $("#partner_id").val();
					/* Dialog Form Selector Name, Buttons Array */
					GetDialog(form, 900, "auto", "");
					GetTabs("tabs");
					GetDate("partner_data");
					if(empty(id)){						
	
						$("#tabs").tabs({disabled: [1,2,3]});
					}else{						
						$("#tabs").tabs({disabled: [1]});   // EDIT TABS DISABLE

						$(document).on("click", "#2", function () {

							LoadTable("acc_details");
							GetButtons("add_acc_button", "", "export_acc_button", "");
							SetEvents("add_acc_button", "delete_acc_button", "check-all-account", "acc_details", "add-edit-acc-form", a_aJaxURL);
						});

						$(document).on("click", "#3", function () {
							LoadTable("bank_details");
							GetButtons("add_bank_button", "delete_bank_button", "export_bank_button", "");
							SetEvents("add_bank_button", "delete_bank_button", "check-all-bank", "bank_details", "add-edit-bank-form", b_aJaxURL);
						});

						$(document).on("click", "#4", function () {
							LoadTable("cadre_details");
							GetButtons("add_cadre_button", "delete_cadre_button", "export_cadre_button", "");
							SetEvents("add_cadre_button", "delete_cadre_button", "check-all-cadre", "cadre_details", "add-edit-cadre-form", c_aJaxURL);
						});							
					}
			}
		}

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

		// Add - Save Partner
		$(document).on("click", "#save-dialog", function () {
            param = new Object();

            //Action
            param.act	= "save_part";
            param.id	= $("#partner_id").val();
	    	
            param.pid	= $("#partner_identity_id").val();
            param.ps	= $("#partner_status").val();
	    	param.pn	= $("#partner_name").val();
	    	param.pia	= $("#partner_Iaddress").val();
	    	param.ppa	= $("#partner_Paddress").val();
	    	param.pm	= $("#pay_method").val();	    		    	

	    	param.py	= $("#payer").val();
	    	param.pd	= $("#partner_data").val();
	    	param.psn	= $("#partner_s_number").val();

			
			var allow = true;
			if(param.py == 0){
				if(!confirm("დარწმუნებული ხართ რომ პარტნიორი არ არის დღგ-ს გადამხდელი?")) {						
					allow = false;
				}
			}
			
			if(allow){
				if(param.pid == ""){
					alert("შეავსეთ საიდ. კოდი/ პირადი №!");
				}else if(param.pn == ""){
					alert("შეავსეთ დასახელება!");
				}else if(param.pia == ""){
					alert("შეავსეთ იურიდიული მისამართი!");
				}else if(param.pd == ""){
					alert("შეავსეთ თარიღი!");
				}else {
				    $.ajax({
				        url: aJaxURL,
					    data: param,
				        success: function(data) {
					        if(typeof(data.error) != "undefined"){
								if(data.error != ""){
									alert(data.error);
								}else{
									LoadTable(tName);
					        		CloseDialog(fName);
								}
							}
					    }
				    });
				}
			}
		});

		// Add - Save account
	    $(document).on("click", "#save_partner_accounts", function () {
	    	param = new Object();

            //Action
            param.act	= "save_acc";
            param.pid	= $("#partner_id").val();
            param.aid	= $("#acc_id").val();
	    	param.bid	= $("#partner_bank_id").val();
	    	
            param.auid	= $("#acc_user_id").val();    	
	    	param.an	= $("#acc_name").val();
	    	param.aln	= $("#acc_lname").val();
	    	param.ap	= $("#acc_position").val();
	    	param.ainfo	= $("#acc_info").val();
	    	param.ap 	= $("#acc_phone").val();
	    	param.amp 	= $("#acc_m_phone").val();	    		    	
	    		    	
	    	param.as	= $("#acc_sale").val();
	    	param.bl	= $("#acc_limit").val();
						        
			if(param.auid == ""){
				alert("შეავსეთ პირადი ნომერი!");
			}else {
			    $.ajax({
			        url: a_aJaxURL,
				    data: param,
				    success: function(data) {
			        	if(typeof(data.error) != "undefined"){
							if(data.error != ""){
								alert(data.error);
							}else{
								LoadTable("acc_details");
					        	CloseDialog("add-edit-acc-form");
							}
						}
				    }
			    });
			}
		});

		// Add - Save Bank
	    $(document).on("click", "#save_partner_bank", function () {
	    	param = new Object();

            //Action
            param.act	= "save_bank";            
	    	param.bid	= $("#partner_bank_id").val();
	    	param.pid	= $("#partner_id").val();    	
	    	
	    	param.bn	= $("#bank_name").val();
	    	param.bb	= $("#bank_branch").val();
	    	param.bc	= $("#bank_code").val();
	    	param.ba	= $("#bank_account").val();
						        
			if(param.bn == ""){
				alert("შეავსეთ ბანკის დასახელება!");
			}else {
			    $.ajax({
			        url: b_aJaxURL,
				    data: param,
				    success: function(data) {
			        	if(typeof(data.error) != "undefined"){
							if(data.error != ""){
								alert(data.error);
							}else{
								LoadTable("bank_details");
					        	CloseDialog("add-edit-bank-form");
							}
						}
				    }
			    });
			}
		});

		 // Add - Save Cadre
	    $(document).on("click", "#save_partner_cadre", function () {
	    	param = new Object();

            //Action
            param.act	= "save_cadre";            
	    	param.pcid	= $("#partner_cadre_id").val();
	    	param.pid	= $("#partner_id").val();	 
	    	
	    	param.cui	= $("#cadre_user_id").val();
	    	param.cn	= $("#cadre_name").val();
	    	param.cln	= $("#cadre_lname").val();
	    	param.cp	= $("#cadre_position").val();
	    	param.cc	= $("#cadre_contact").val();
	    	param.cph	= $("#cadre_phone").val();
	    	param.cmp	= $("#cadre_m_phone").val();
	    	
	    	param.cs	= $("#cadre_sale").val();
	    	param.cl	= $("#cadre_limit").val();

			var allow = true;
			if(param.cc == 1){
				if(!confirm("დარწმუნებული ხართ რომ კადრი არის საკონტაქტო პირი?")) {						
					allow = false;
				}
			}
			if(allow){
				if(param.cui == ""){
					alert("შეავსეთ პირადი ნომერი!");	
				}else if(param.cn == ""){
					alert("შეავსეთ სახელი!");
				}else if(param.cln == ""){
					alert("შეავსეთ გვარი!");
				}else if(param.cp == ""){
					alert("შეავსეთ პოზიცია!");
				}else if(param.cph == ""){
		    		alert("შეავსეთ ტელეფონი!");			        
		    	}else if(param.cmp == ""){
		    		alert("შეავსეთ მობილური!");			        
		    	}else{
		    		$.ajax({
				        url: c_aJaxURL,
					    data: param,
				        success: function(data) {
							if(typeof(data.error) != "undefined"){
								if(data.error != ""){
									alert(data.error);
								}else{
									LoadTable("cadre_details", param.p_id);
						        	CloseDialog("add-edit-cadre-form");
								}
							}
					    }
				    });
				}
			}
			
		});
		
    </script>
</head>

<body>    
    <div id="dt_example" class="ex_highlight_row">
        <div id="container">
            <div id="dynamic">
            	<h2 align="center">პარტნიორები</h2>
            	<div id="button_area">
	        		<button id="add_button">დამატება</button><button id="delete_button">წაშლა</button>
	        	</div>
                <table class="display" id="example">
                    <thead>
                        <tr id="datatable_header">
                            <th>ID</th>
                            <th style="width: 100%">დასახელება</th>
                            <th class="min">საიდ. კოდი</th>
                            <th class="min">ფაქტ. მისამართი</th>
                            <th class="min">ტელეფონი</th>
                            <th style="width: 100%">სახელი, გვარი</th>
                            <th class="min">ანგ. ფორმა</th>
                            <th class="check">#</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr class="search_header">
                            <th class="colum_hidden">
                            	<input type="text" name="search_id" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_name" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_ident_code" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_address" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_phone" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_lname" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_pay_form" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                            	<input type="checkbox" name="check-all" id="check-all">
                            </th>
                    </thead>
                </table>
            </div>
            <div class="spacer">
            </div>
        </div>
    </div>
        
    <!-- jQuery Dialog -->
    <div id="add-edit-form" class="form-dialog" title="პარტნიორები">
    	<!-- aJax -->
	</div>
	
</body>
</html>