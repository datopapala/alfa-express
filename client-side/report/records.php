<head>
	<script type="text/javascript">
		var aJaxURL	= "server-side/report/records.action.php";	//server side folder url
		var tName	= "example";											//table name
		
		$(document).ready(function () {
			LoadTable();
		});

		function LoadTable(){
			/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
			GetDataTable(tName, aJaxURL, "get_list", 6, "", 0, "", 1, "desc");
		}

		
		var record;
		function play(record){
			
			link = 'http://212.72.155.176:8181/records/' + record;
			var newWin = window.open(link, 'newWin','width=420,height=200');
            newWin.focus();
            
		}
	    
    </script>
</head>

<body>
    <div id="dt_example" class="ex_highlight_row">
        <div id="container">        	
            <div id="dynamic">
            	<h2 align="center">ჩანაწერები</h2>
                <table class="display" id="example">
                    <thead>
                        <tr id="datatable_header">
                            <th>ID</th>
                            <th style="width: 100%;">თარიღი</th>
                             <th style="width: 120px;">წყარო</th>
                            <th style="width: 120px;">ადრესატი</th>
                            <th style="width: 120px;">დრო</th>
                            <th style="width: 100%;">ქმედება</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr class="search_header">
                            <th class="colum_hidden">
                            	<input type="text" name="search_id" value="ფილტრი" class="search_init" style=""/>
                            </th>
                            <th>
                            	<input type="text" name="search_number" value="ფილტრი" class="search_init hidden-input" style=""></th>
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
            </div>
            <div class="spacer">
            </div>
        </div>
    </div>
</body>