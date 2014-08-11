<html>
<head>
	<style type="text/css">
	.hidden{
		display : none;
	}
	</style>
	<script type="text/javascript">
		var aJaxURL	= "server-side/info/user.action.php";		//server side folder url
		var upJaxURL= "server-side/upload/file.action.php";				//server side folder url
		var tName	= "example";											//table name
		var fName	= "add-edit-form";										//form name
		var img_name		= "0.jpg";

		$(document).ready(function () {
			LoadTable();

			/* Add Button ID, Delete Button ID */
			GetButtons("add_button", "delete_button");

			SetEvents("add_button", "delete_button", "check-all", tName, fName, aJaxURL);
		});

		function LoadTable(){
			/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
			GetDataTable(tName, aJaxURL, "get_list", 5, "", 0, "");
		}

		function LoadDialog(){
			var id		= $("#pers_id").val();
			if(id != ""){
				$("#lname_fname").attr("disabled", "disabled");
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
			GetDialog(fName, 450, "auto", "");

			if( $("#position").val() == 13 ){
					$("#passwordTR").removeClass('hidden');
			}
			$( "#accordion" ).accordion({
				active: false,
				collapsible: true,
				heightStyle: "content",
				activate: function(event, ui) {
					$("#is_user").val();
				}
			});
			GetButtons("add_group", "");

		}

	    // Add - Save
		$(document).on("click", "#save-dialog", function () {
			param = new Object();

            //Action
	    	param.act	= "save_pers";

		    param.id	= $("#pers_id").val();

		    param.n		= $("#name").val();
		    param.t		= $("#tin").val();
		    param.p		= $("#position").val();
		    param.a		= $("#address").val();
		    param.pas	= $("#password").val();
		    param.h_n	= $("#home_number").val();
		    param.m_n	= $("#mobile_number").val();
		    param.comm	= $("#comment").val();

		    param.user	= $("#user").val();
		    param.userp	= $("#user_password").val();
		    param.gp	= $("#group_permission").val();
		    param.img 	= img_name;

			if(param.n == ""){
				alert("შეავსეთ სახელი და გვარი!");
			}else if(param.p == 0){
				alert("შეავსეთ თანამდებობა!");
			}else if(param.user && !param.userp){
				alert("შეავსეთ პაროლი")
			}else{
			    $.ajax({
			        url: aJaxURL,
				    data: param,
			        success: function(data) {
						if(typeof(data.error) != "undefined"){
							if(data.error != ""){
								alert(data.error);
							}else{
								LoadTable();
				        		CloseDialog(fName);
							}
						}
				    }
			    });
			}

		});

	    $(document).on("click", "#choose_button", function () {
		    $("#choose_file").click();
		});


	    $(document).on("change", "#choose_file", function () {
	    	var file		= $(this).val();
		    var name		= uniqid();
		    var path		= "../../media/uploads/images/worker/";

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
    							$("#upload_img").attr("src", "media/uploads/images/worker/" + img_name);
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

		    	param.path	 	= "../../media/uploads/images/worker/";
			    param.file_name	= img_name;
			    var id			= $("#pers_id").val();

	            $.ajax({
	                url: upJaxURL,
	                data: param,
	                success: function(data) {
	                    if (typeof(data.error) != "undefined") {
	                        if (data.error != "") {
	                            alert(data.error);
	                        } else {
	                        	$("#choose_button").button("enable");
	                        	$("#upload_img").attr("src", "media/uploads/images/worker/0.jpg");
	                        	if(!empty(id)){
	                        		DeleteImage(id);
		                        }
	                        }
	                    }
	                }
	            });
			}
		});

        $(document).on("click", "#add_group", function(){
    		param = new Object();
    	    //Action
    		param.act	= "get_add_group_page";

    	    $.ajax({
    	        url: aJaxURL,
    		    data: param,
    	        success: function(data) {
    				if(typeof(data.error) != "undefined"){
    					if(data.error != ""){
    						alert(data.error);
    					}else{
    						var buttons = {
    								"save": {
    						            text: "შენახვა",
    						            id: "save_group_dialog",
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
        					$("#add-group-form").html(data.page);
        					GetDialog("add-group-form",440, "auto", buttons);
        					GetDataTable1("pages", aJaxURL, "get_pages_list", 2, "", 0, "", "", "", "", "280px", "true");
    					}
    				}
    		    }
    	    });
		});

		$(document).on("click", "#save_group_dialog", function(){

		    var data = $(".check1:checked").map(function () { //Get Checked checkbox array
		        return this.value;
		    }).get();

			var pages = new Array;

 		    for (var i = 0; i < data.length; i++) {
 		    	pages.push(data[i]);
 		    }

     		param = new Object();
     	    //Action
     		param.act	= "save_group";
 			param.nam	= $("#group_name").val();
 			param.pag	= JSON.stringify(pages);

 			//var link	=  GetAjaxData(param);

 			if( param.nam == "" ){
 				alert("შეიყვანეთ ჯგუფის სახელი!");
 			}else{
 	    	    $.ajax({
 	    	        url: aJaxURL,
 	    		    data: param,
 	    	        success: function(data) {
 	    				if(typeof(data.error) != "undefined"){
 	    					if(data.error != ""){
 	    						alert(data.error);
 	    					}else{
 	    						$("#group_permission").html(
 	    							$("#group_permission").html() + "<option value=" + data.inserted_value + " selected=selected>" + data.inserted_name + "</option>"
 	    	    				);
 	    						$("#add-group-form").dialog("close");
 	    					}
 	    				}
 	    		    }
 	    	    });
 			}


		});

	    function DeleteImage(prod_id) {
            $.ajax({
                url: aJaxURL,
                data: "act=delete_image&id=" + prod_id,
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

    </script>
</head>

<body>
    <div id="dt_example" class="ex_highlight_row">
        <div id="container">
            <div id="dynamic">
                <h2 align="center">თანამშრომლები</h2>
	        	<div id="button_area">
	        		<button id="add_button">დამატება</button><button id="delete_button">წაშლა</button>
	        	</div>
                <table class="display" id="example">
                    <thead>
                        <tr id="datatable_header">
                            <th>ID</th>
                            <th style="width: 100%">ვინაობა</th>
                            <th class="min">პირადი ნომერი</th>
                            <th class="min">თანამდებობა</th>
                            <th class="aver">მისამართი</th>
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
                                <input type="text" name="search_tin" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_position" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_address" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                            	<input type="checkbox" name="check-all" id="check-all">
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
    <div id="add-edit-form" class="form-dialog" title="თანამშრომლები">
    	<!-- aJax -->
	</div>
    <!-- jQuery Dialog -->
    <div id="image-form" class="form-dialog" title="თანამშრომლის სურათი">
    	<img id="view_img" src="media/uploads/images/worker/0.jpg">
	</div>
	 <!-- jQuery Dialog -->
    <div id="add-group-form" class="form-dialog" title="ჯგუფი">
	</div>
</body>
</html>