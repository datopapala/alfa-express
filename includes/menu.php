<?php include('classes/core.php');?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" /> 
    
    <title>Menu</title>
    
	<link rel="stylesheet" href="media/css/menu/text.css" />
	<link rel="stylesheet" href="media/css/menu/960_fluid.css" />
	<link rel="stylesheet" href="media/css/menu/main.css" />
	<link rel="stylesheet" href="media/css/menu/bar_nav.css" />
	<link rel="stylesheet" href="media/css/menu/side_nav.css" />
	<link rel="stylesheet" href="media/css/menu/skins/theme_blue.css" />
    
    <script type="text/javascript">
			var AjaxURL = "includes/menu.server.php";
			
			function makeUL(lst, nav_class) {
			    var html = [];
			    if(empty(nav_class))
			    	html.push("<ul>");
			    else
			    	html.push("<ul class=\"" + nav_class + "\">");
			    	
			    $(lst).each(function() {
				    html.push(makeLI(this));
				});
			    html.push("</ul>");      
			    return html.join("\n");
			}
			
			function makeLI(elem) {
			    var html		= [];
			    var li_class	= elem.li_class;
			    var url			= elem.url;
			    var icon		= elem.icon;
			    var separator	= "<div class=\"separator\"></div>";
			    
			    if(empty(li_class))
			    	html.push("<li>");
			    else
			    	html.push("<li class=\"" + elem.li_class + "\">");
		    	
			    if(empty(url)){
			    	url = "index.php?pg=" + elem.page_id;
				}
				
			    if(empty(icon)){
			    	icon = "<img src=\"media/images/menu/icons/grey/Document.png\" / >";
				}else{
					icon = "<img src=\"media/images/menu/icons/grey/" + elem.icon + "\" / >";					
				}
				
			    html.push("<a href=\"" + url +"\" class=\"" + elem.url_class + "\">");
			    html.push(icon);
			    html.push("<span>" + elem.title + "</span>");
			    
			    if (elem.sub && elem.sub.length > 0){
			    	html.push("<span class=\"icon\">&nbsp;</span></a>");
			        html.push(makeUL(elem.sub));
			    }else{
			    	html.push("</a>");
				}
			    html.push("</li>");
			    return html.join("\n");
			}

			$(function() {	
				
				$.ajax({
			        url: AjaxURL,
			        data: "nav_id=1",
		            async: false, 
			        success: function(data) {
			        	nav_cont	= data.nav;
			        	nav_class	= data.nav_class;
				    }
			    });
			    $("#top_nav").html(GetMinimize("bottom") + makeUL(nav_cont, nav_class));
			    
				$.ajax({
			        url: AjaxURL,
			        data: "nav_id=2",
		            async: false, 
			        success: function(data) {
			        	nav_cont	= data.nav;
			        	nav_class	= data.nav_class;
				    }
			    });
			    //$("#side_nav").html(makeUL(nav_cont, nav_class) + GetMinimize("bottom"));


				$.ajax({
			        url: AjaxURL,
			        data: "nav_id=3",
		            async: false, 
			        success: function(data) {
			        	nav_cont	= data.nav;
			        	nav_class	= data.nav_class;
				    }
			    });
			    if(empty(nav_cont))
				    $("#footer_wrapper").css("display", "none");
			    else
			    	$("#footer").html(GetMinimize("top") + makeUL(nav_cont, nav_class));
			});

			function GetMinimize(position){
				var html;
				html =  "<a href=\"#\" class=\"minimize round_" + position + "\"><span>minimize</span></a>";
				return html;
			}
	</script>
	
    <script type="text/javascript" src="js/menu/sherpa_ui.js"></script>
</head>

<body>
	<div id="wrapper" class="container_16">
		<div id="top_nav" class="nav_down bar_nav grid_16 round_all">
		</div>
				
		<div id="side_nav" class="side_nav grid_3 push_down">
		</div>
			
		<div class="clear"></div>
		<div id="footer_wrapper" class="container_16">
			<div id="footer" class="grid_16 nav_up bar_nav round_all clearfix">
			</div>
		</div>
	</div>
	<?php 
	$user_id = $_SESSION['USERID'];
	$res = mysql_fetch_assoc(mysql_query("	SELECT 		`persons`.`name` AS `person`,
														`users`.`login_date` AS `date`
											FROM 		`users`
											LEFT JOIN 	`persons` ON `users`.`person_id` = `persons`.`id`
											WHERE 		`users`.`id` = $user_id"));
	
	?>
	<div style="margin-top:25px; position: absolute; left:5px;"><table><tr style="border-top: 1px solid black;"><td>სისტემაში შემოსულია:</td> <td><span style="font-weight:bold"><?php echo $res['person']; ?></span></td></tr> <tr style="border-bottom: 1px solid black;"><td>შემოსვლის დრო:</td><td><span style="font-weight:bold"><?php echo $res['date']; ?></span></td></tr></table></div>
</body>
</html>