<html>
<head>
	<style type="text/css">
		.warehou{
			display: none;
		}
	</style>
	<script type="text/javascript">
		var aJaxURL	= "server-side/info/object.action.php";			//server side folder url
		var seoyURL	= "server-side/seoy/seoy.action.php";			//server side folder url
		var tName	= "example";
		var fName	= "add-edit-form";								//form name													//table name
		
		$(document).ready(function () {     
			/*    load main table  */   	
			LoadTable();
			/*    Add Button ID, Delete Button ID */  
			GetButtons("add_button", "delete_button");
			SetEvents("add_button", "delete_button", "check-all", tName, fName, aJaxURL);			
		});

		$(document).on("change", "#type",function(){
			if(this.value == 5){
				$("#warehouseID").removeClass('warehou');
			}else{
				$("#warehouseID").addClass('warehou');
			}
        });
        
		
		
		function LoadTable(){
			/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
			GetDataTable(tName, aJaxURL, "get_list", 5, "", 0, "", 1, "asc");
		}
		
		function LoadDialog(){
			
			if($("#type").val() == 5){
					$("#warehouseID").removeClass("warehou");
			}

			/* Dialog Form Selector Name, Buttons Array */
			GetDialog(fName, 500, "auto", "");
			
		}

		$(document).on("click", "#save-dialog", function () {
			param = new Object();
	    	
            //Action
	    	param.act	= "save_object";    	
		    param.id	= $("#object_id").val();
		    param.na    = $("#object_name").val();
		    param.t		= $("#type").val();
		    param.p		= $("#parent").val();
		    param.a 	= $("#address").val();
		    param.w		= $("#warehouse").val();	
					        
			if(param.na == ''){
				alert("შეავსეთ ობიექტის სახელი!");
			}else if(param.t == ''){
				alert("შეავსეთ ობიექტის ტიპი!");				
			}else if(param.a == ''){
				alert("შეავსეთ ობიექტის მისამართი!");				
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
    </script>
</head>

<body>    
    <div id="dt_example" class="ex_highlight_row">
        <div id="container">
            <div id="dynamic">
            	<h2 align="center">ობიექტები</h2>
            	<div id="button_area">
	        		<button id="add_button">ობიექტის დამატება</button><button id="delete_button">წაშლა</button>
	        	</div>
                <table class="display" id="example">
                    <thead>
                        <tr id="datatable_header">
                            <th>ID</th>
                            <th style="width: 246px;">დასახელება</th>
                            <th style="width: 245px;">ტიპი</th>
                            <th style="width: 246px;">მშობელი ობიექტი</th>
                            <th style="width: 246px;">მისამართი</th>
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
                                <input type="text" name="search_location" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_location" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_location" value="ფილტრი" class="search_init" />
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
    <div id="add-edit-form" class="form-dialog" title="ობიექტის დამატება">
    	<!-- aJax -->
	</div>
	
</body>
</html>