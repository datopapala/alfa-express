<html>
<head>
	<script type="text/javascript">
		var aJaxURL	= "server-side/view/category.action.php";		//server side folder url
		var tName	= "example";													//table name
		var fName	= "add-edit-form";												//form name
		    	
		$(document).ready(function () {        	
			LoadTable();	
						
			/* Add Button ID, Delete Button ID */
			GetButtons("add_button", "delete_button");			
			SetEvents("add_button", "delete_button", "check-all", tName, fName, aJaxURL);
		});
        
		function LoadTable(){
			var menuLength = [[ -1, 15, 30, 50], [ "ყველა", 15, 30, 50]];
			
			/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
			GetDataTable(tName, aJaxURL, "get_list", 3, "", 0, menuLength, 1, "desc");
    		LoadTree();
		}
		
		function LoadDialog(){
			var id		= $("#cat_id").val();
			
			/* Dialog Form Selector Name, Buttons Array */
			GetDialog(fName, 600, "auto", "");
		}
		
	    // Add - Save
	    $(document).on("click", "#save-dialog", function () {
		    param 			= new Object();

		    param.act		="save_category";
	    	param.id		= $("#cat_id").val();
	    	param.cat		= $("#category").val();
	    	param.par_id	= $("#parent_id").val();
			
			if(param.cat == ""){
				alert("შეავსეთ პროდუქტის კატეგორია!");
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
				        		CloseDialog(fName);
							}
						}
				    }
			    });
			}
		});

	    function LoadTree(){
	    	param 			= new Object();
		    param.act		="get_tree";
			$.ajax({
		        url: aJaxURL,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							$("#tree").html(data.page);
						}
					}
			    }
		    });
		}
    </script>
</head>

<body>
    <div id="dt_example" class="ex_highlight_row" style="width: 1024px; margin: 0 auto;">
        <div id="container" class="category_table" style="float: left;">        	
            <div id="dynamic">
            	<h2 align="center">კატეგორიები</h2>
            	<div id="button_area">
        			<button id="add_button">დამატება</button>
        			<button id="delete_button">წაშლა</button>
        		</div>
                <table class="display" id="example">
                    <thead>
                        <tr id="datatable_header">
                            <th>ID</th>
                            <th style="width: 100%">ქვე კატეგორია</th>
                            <th style="width: 100%">კატეგორია</th>
                            <th class="check">#</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr class="search_header">
                            <th class="colum_hidden">
                            	<input type="text" name="search_id" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                            </th>
                            <th>
                                <input type="text" name="search_sub_category" value="ფილტრი" class="search_init" />
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
		<!-- jQuery Tree -->
		<div  id="tree" style="float: right; margin-right: 200px; cursor:pointer; margin-top: 100px;">
	    	<!-- aJax -->
		</div>
    </div>
    
    <!-- jQuery Dialog -->
    <div id="add-edit-form" class="form-dialog" title="კატეგორიები">
    	<!-- aJax -->
	</div>
</body>
</html>