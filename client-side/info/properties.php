<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<script type="text/javascript">
		var aJaxURL = "server-side/info/properties.action.php";		//server side folder url
		var wsdlURL = "server-side/wsdl/wsdl.action.php";
		var fName 	= "properties-form";
		
		$(document).ready(function (){
			LoadDialog();
			GetData();
		});
		
		$(document).on("click", "#save-dialog", function () {            
            var name	= $("#name").val();
            var address	= $("#address").val();
            var payer	= $("#payer").val();
            
			$.ajax({
		        url: aJaxURL,
			    data: "act=set_data&name=" + name + "&address=" + address + "&payer=" + payer,
		        success: function(data) {
			        if(typeof(data.error) != "undefined"){
						if(data.error != ""){
							alert(data.error);
						}
					}
			    }
		    });
		    
			param = new Object();
            //Action
            param.act			= "create_service_user";
            
            param.user_name		= $("#user_name").val();
            param.user_password	= $("#user_password").val();
            param.su_name		= $("#su_name").val();
            param.su			= $("#su").val();
            param.sp			= $("#sp").val();

			var create = false;
            $.ajax({
                url: wsdlURL,
                async: false, //r-value
                data: param,
                success: function(data) {
                	if (typeof(data.error) != "undefined") {
                        if (data.error != "") {
                            alert(data.error);
                        } else {
                        	create = true;
                        }
                    }
            	}
        	});
        	if(create){
	            $.ajax({
			        url: wsdlURL,
				    data: "act=chek_service_user",
			        success: function(data) {
				        if(typeof(data.error) != "undefined"){
							if(data.error != ""){
								alert(data.error);
							}else{
								alert("სიტემის მომხმარებელის შექმნა წარმატებულად დასრულდა!!!");
							}
						}
				    }
			    });
            }
			GetData();
		});
		
		function GetData(){			
			$.ajax({
		        url: aJaxURL,
			    data: "act=get_data",
		        success: function(data) {		        
		        	if(typeof(data.error) != "undefined"){
						if(data.error != ""){
							alert(data.error);
						}else{
							$("#tin").val(data.tin);
							$("#name").val(data.name);
							$("#address").val(data.address);
							$("#user_name").val(data.user_name);
							$("#user_password").val(data.user_password);
							$("#ip").val(data.ip);
							$("#su_name").val(data.su_name);
							$("#su").val(data.su);
							$("#sp").val(data.sp);
							$("#payer").val(data.payer);
						 }
					}
			    }
		    });
		}
		
		function LoadDialog(){	
			var defoult = {
		        "save": {
		            text: "შენახვა",
		            id: "save-dialog",
		            click: function () {
		            }
		        }
		    };	
		    
		    $("#" + fName).dialog({
		        resizable:     false,
		        draggable:     false,		       
		        width:         1000,
		        height:        540,
		        modal:         false,
		        stack:         true,
	            sticky:        false,
	            closeOnEscape: false,
	            dialogClass:   "pos-dialog",
	            buttons:       defoult 
		    });
		}
	</script>
</head>
<body>
	<div id="properties-form" title="ორგანიზაციის რეკვიზიტები">
		<div id="dialog-form">
			<fieldset>
				<legend>ორგანიზაციის ინფორმაცია</legend>
				<table width="52%" class="dialog-form-table">
					<tr>
						<td style="width: 170px;"><label for="tin">საიდენტიფიკაციო კოდი</label></td>
						<td style="width: 170px;"><label for="name">დასახელება</label></td>
						<td style="width: 170px;"><label for="address">მისამართი</label></td>
					</tr>
					<tr>
						<td><input type="text" id="tin" class="idle" onblur="this.className='idle'" onfocus="this.className='activeField'" value="" disabled="disabled" /></td>
						<td><input type="text" id="name" class="idle" onblur="this.className='idle'" onfocus="this.className='activeField'" value="" /></td>
						<td><input type="text" id="address" class="idle" onblur="this.className='idle'" onfocus="this.className='activeField'" value="" /></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend>ელექტრონული დეკლარირების მომხმარებლის ინფორმაცია</legend>
				<table width="35%" class="dialog-form-table">
					<tr>
						<td style="width: 170px;"><label for="user_name">მომხმარებლის  სახელი</label></td>
						<td style="width: 170px;"><label for="user_password">მომხმარებლის პაროლი</label></td>
					</tr>
					<tr>
						<td><input type="text" id="user_name" class="idle" onblur="this.className='idle'" onfocus="this.className='activeField'" value="" /></td>
						<td><input type="password" id="user_password" class="idle" onblur="this.className='idle'" onfocus="this.className='activeField'" value="" /></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend>სერვისის მომხმარებლის ინფორმაცია</legend>
				<table width="70%" class="dialog-form-table">
					<tr>
						<td style="width: 170px;"><label for="ip">IP</label></td>
						<td style="width: 170px;"><label for="su_name">ობიექტის სახელი</label></td>
						<td style="width: 170px;"><label for="su">სერვისის მომხმარებელი</label></td>
						<td style="width: 170px;"><label for="sp">სერვისის მომხმარებლის პაროლი</label></td>
					</tr>
					<tr>
						<td><input type="text" id="ip" class="idle" onblur="this.className='idle'" onfocus="this.className='activeField'" value="" disabled="disabled" /></td>
						<td><input type="text" id="su_name" class="idle" onblur="this.className='idle'" onfocus="this.className='activeField'" value="" /></td>
						<td><input type="text" id="su" class="idle" onblur="this.className='idle'" onfocus="this.className='activeField'" value="" /></td>
						<td><input type="password" id="sp" class="idle" onblur="this.className='idle'" onfocus="this.className='activeField'" value="" /></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend>დღგ-ს ვალდებულება</legend>
				<select id="payer" >
				    <option value="1">კი</option>
					<option value="0">არა</option>
				</select>
			</fieldset>
		</div>
	</div>
</body>
</html>