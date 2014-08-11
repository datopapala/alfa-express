<html>
<head>
	<script type="text/javascript">
		var aJaxURL				= "server-side/view/bank.action.php";
		var bank_aJaxURL		= "server-side/view/bank/bank.action.php";
		var c_person_aJaxURL	= "server-side/view/bank/c_person.action.php";	//server side folder url
		var tName				= "example";									//table name
		var fName				= "add-edit-form";								//form name

		$(document).ready(function () {

			LoadTable(tName);
			GetButtons("add_button", "delete_button");
			SetEvents("add_button", "delete_button", "check-all", tName, fName, aJaxURL);

		});

		function LoadDialog(form){
			switch(form){
				case fName :

					var id = $("#bank_id").val();
					if(id == ''){
					//	$("#local_bank_id").val(GetLocalID());
						$("#fiel_bank").css("display", "none")
					}

					LoadTable("obj_list");
					GetButtons("add_button_bank", "delete_button_prod");
					SetEvents("add_button_bank", "delete_button_prod", "check-all-prod", "obj_list", "add-edit-prod-form", bank_aJaxURL);
					GetDialog(fName, 600, "auto");

				break;

				case "add-edit-contact-form" :
					var buttons = {
			        				"save": {
			            					text: "დამატება",
			            					id: "contact-form",
			            					click: function () {}
			       				  }		    }
					SetEvents("add_button_c_person", "delete_button_c_person", "", 'c_perso_list', "add-edit-contact-form", c_person_aJaxURL, 'local_id='+$("#bank_object_id").val());

					GetDialog(form, 600, "auto", buttons);

				break;

				default:

					var id = $("#bank_object_id").val();

					if(id == ''){
					//	$("#local_bank_object_id").val(GetLocalID1());
						$("#bank_object_field").css("display", "none");

					}

					var buttons = {
				        "save": {
				            text: "შენახვა",
				            id: "add-object",
				            click: function () {}
				        },
						"close": {
			            text: "დახურვა",
			            id: "close-object",
			            click: function () {LoadTable('obj_list')
			            					CloseDialog('add-edit-prod-form');}
			        			}
					}

					LoadTable("c_perso_list");
					GetButtons("add_button_c_person", "delete_button_c_person");
					SetEvents("add_button_c_person", "delete_button_c_person", "", 'c_perso_list', "add-edit-contact-form", c_person_aJaxURL, 'local_id='+$("#bank_object_id").val());
					GetDialog("add-edit-prod-form", 500, "auto",buttons);

				break;
			}
		}

		function LoadTable(table){

			switch (table){
				case "obj_list":

					GetDialog(fName, 600, "auto", "");
					var local_id	= $("#bank_id").val();
					GetDataTable(table, bank_aJaxURL, "get_list", 3, "local_id=" +local_id, 0, "", 1, "asc");

				break;

				case "c_perso_list":

					var  local_id = $("#bank_object_id").val();

					$("#bank_person_id").val(local_id);
					GetDataTable("c_perso_list", c_person_aJaxURL, "get_list", 4, "local_id=" + local_id, 0, "", 1, "desc");

				break;

				default :
					GetDataTable(tName, aJaxURL, "get_list",3, "", 0, "", 1, "desc");
			}
		}

		function GetLocalID(){
			var local_id;
			$.ajax({
		        url: aJaxURL,
        		async: false,
			    data: "act=get_local_id",
		        success: function(data) {
					if(typeof(data.error) != "undefined"){
						if(data.error != ""){
							alert(data.error);
						}else{
							local_id = data.increment;
						}
					}
			    }
		    });
	      	return local_id;
		}

		function GetLocalID1(){
			var local_id;
			$.ajax({
		        url: bank_aJaxURL,
        		async: false,
			    data: "act=get_local_id",
		        success: function(data) {
					if(typeof(data.error) != "undefined"){
						if(data.error != ""){
							alert(data.error);
						}else{
							local_id = data.increment;
						}
					}
			    }
		    });
	      	return local_id;
		}

	    // Add - Save
	    $(document).on("dialogbeforeclose", "#" + "add-edit-prod-form", function( event, ui ) {
	    	/*
	    	if (confirm("დარწმუნებული ხართ, რომ არ გსურთ მონაცემების შენახვა?")) {
	    		if($(this).is(":ui-dialog") || $(this).is(":data(dialog)")){
	    			$(this).dialog("destroy");
	    		}
	    	} else {
	    		  return false;
	    	}
	    	*/
	    	if($(this).is(":ui-dialog") || $(this).is(":data(dialog)")){
				$(this).dialog("destroy");
				LoadTable('obj_list');
				//alert("esaa")
			}
		});
	    $(document).on("click", "#add-object", function () {
		    param 			        = new Object();
		    param.act		        ="save_object_id";
	    	param.id		        = $("#bank_id").val();
	    	param.bank_object_id    = $("#bank_object_id").val();
	    	param.bank_local_id     = $("#bank_local_id").val();
	    	param.trans_obj		    = $("#trans_obj").val();
	    	param.trans_address		= $("#trans_address").val();
		{
			    $.ajax({
			        url: bank_aJaxURL,
				    data: param,
			        success: function(data) {
						if(typeof(data.error) != 'undefined'){
							if(data.error != ''){
								alert(data.error);
							}else{

								if(param.bank_object_id=='') {
									$("#bank_object_id").val(data.myid)
									$("#bank_object_field").css("display", "");
									$("#add-object").css("display", "none");

								}else    		 {
									LoadTable('obj_list')
									CloseDialog('add-edit-prod-form');
									};
							}
						}
				    }
			    });
			}
		});

	    $(document).on("click", "#contact-form", function () {
		    param 			        = new Object();
		    param.act		        ="save_c_person";
	    	param.person_id	        = $("#bank_person_id").val();
	    	param.object_id	        = $("#bank_object_id").val();
	    	param.c_person	        = $("#c_person").val();
	    	param.phone		        = $("#phone").val();
	    	param.mail       		 = $("#mail").val();
		{
			    $.ajax({
			        url: c_person_aJaxURL,
				    data: param,
			        success: function(data) {
						if(typeof(data.error) != 'undefined'){
							if(data.error != ''){
								alert(data.error);
							}else{
								LoadTable('c_perso_list');
								CloseDialog('add-edit-contact-form');
							}
						}
				    }
			    });
			}
		});


	    $(document).on("click", "#save-dialog", function () {
		    param 			= new Object();

		    param.act				="save_bank";
	    	param.id				= $("#bank_id").val();
	    	param.name				= $("#name").val();
	    	param.bank_phone		= $("#bank_phone").val();
	    	param.local_bank_id  	= $("#local_bank_id").val();

			if(param.name == ""){
				alert("შეავსეთ ველი!");
			}else {
			    $.ajax({
			        url: aJaxURL,
				    data: param,
			        success: function(data) {
						if(typeof(data.error) != 'undefined'){
							if(data.error != ''){
								alert(data.error);
								return 0;
							}else{
								LoadTable();
								$("#fiel_bank").css("display", "")
								if(param.id=='') {$("#bank_id").val(data.myid)} else { CloseDialog('add-edit-form'); }

								$("#save-dialog").css("display", "none");

							}
						}
				    }
			    });
			}
		});



    </script>
</head>
<body>
    <div id="dt_example" class="ex_highlight_row" style="width: 1024px; margin: 0 auto;">
        <div id="container">
            <div id="dynamic">
            	<h2 align="center">მომსახურე ბანკები</h2>
            	<div id="button_area">
        			<button id="add_button">დამატება</button>
        			<button id="delete_button">წაშლა</button>
        		</div>
                <table class="display" id="example">
                    <thead >
                        <tr id="datatable_header">
                            <th>ID</th>
                            <th style="width: 100%;">სახელი</th>
                            <th style="width: 100%;">ტელეფონი</th>
                        	<th class="check">#</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr class="search_header">
                            <th class="colum_hidden">
                            <th>
                                <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                            </th>
                             <th>
                                <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                            </th>
                          <th>
                            	<input type="checkbox" name="check-all" id="check-all">
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- jQuery Dialog -->
    <div id="add-edit-form" class="form-dialog" title="მომსახურე ბანკები">
    	<!-- aJax -->
	</div>
	<div id="add-edit-prod-form" class="form-dialog" title="ფილიალი">
    	<!-- aJax -->
	</div>
	<div id="add-edit-contact-form" class="form-dialog" title="საკონტაქტო ინფორმაცია">
    	<!-- aJax -->
	</div>
</body>
</html>



