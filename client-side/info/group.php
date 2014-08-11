<html>
<head>
	<style type="text/css">
	.hidden{
		display : none;
	}
	</style>
	<script type="text/javascript">
		var aJaxURL	= "server-side/info/group.action.php";		//server side folder url
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
			GetDataTable(tName, aJaxURL, "get_list", 2, "", 0, "");
		}

		function LoadDialog(){

			/* Dialog Form Selector Name, Buttons Array */
			GetDialog(fName, 450, "auto", "");

			var group_id = $("#group_id").val();

			GetDataTable1("pages", aJaxURL, "get_pages_list&group_id=" + group_id, 2, "", 0, "", "", "", "", "280px", "true");

		}

	    // Add - Save
		$(document).on("click", "#save-dialog", function () {

		    var data = $(".check1:checked").map(function () { //Get Checked checkbox array
		        return this.value;
		    }).get();

			var pages = new Array;

 		    for (var i = 0; i < data.length; i++) {
 		    	pages.push(data[i]);
 		    }

     		param = new Object();
     	    //Action
     		param.act	   = "save_group";
 			param.nam	   = $("#group_name").val();
 			param.pag	   = JSON.stringify(pages);
 			param.group_id = $("#group_id").val();

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
 	    						$("#add-edit-form").dialog("close");
 	    						LoadTable();
 	    					}
 	    				}
 	    		    }
 	    	    });
 			}


		});

    </script>
</head>

<body>
    <div id="dt_example" class="ex_highlight_row">
        <div id="container">
            <div id="dynamic">
                <h2 align="center">ჯგუფები</h2>
	        	<div id="button_area">
	        		<button id="add_button">დამატება</button><button id="delete_button" style="visibility: hidden;">წაშლა</button>
	        	</div>
                <table class="display" id="example">
                    <thead>
                        <tr id="datatable_header">
                            <th>ID</th>
                            <th style="width: 100%">ჯგუფის სახელი</th>
                            <th class="check">#</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr class="search_header">
                            <th class="colum_hidden">
                            	<input type="text" name="search_id" value="ფილტრი" class="search_init" />
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
    <div id="image-form" class="form-dialog" title="პროდუქციის სურათი">
    	<img id="view_img" src="media/uploads/images/worker/0.jpg">
	</div>
	 <!-- jQuery Dialog -->
    <div id="add-group-form" class="form-dialog" title="ჯგუფი">
	</div>
</body>
</html>