<?php require_once(dirname(__FILE__) . '/config.php'); ?>

<?php if ($_SESSION['USERID'] == ''): ?>
	<?php require_once(ABSPATH . 'includes/login.php');?>
<?php else: ?>
	<?php require_once(ABSPATH . 'includes/functions.php');?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>Alfa-express</title>
			
			<link rel="shortcut icon" type="image/ico" href="media/images/dollar.ico" />			
			<link rel="stylesheet" type="text/css" media="screen" href="media/css/reset/reset.css" />
			<!--[if IE]>
				<link rel="stylesheet" type="text/css" media="screen" href="css/reset/ie.css" />
			<![endif]-->
			
			<?php echo GetJs();?>
			<?php echo GetCss();?>
		
			<script type="text/javascript">		        
				$(document).ready(function (){
					AjaxSetup();
				});				
			</script>				
		</head>
		<body style="background: #E2F0FD url(media/images/bg.png);">
			<div id="npm"></div>
			<?php require_once(ABSPATH . 'includes/pages.php'); ?>
			<div class="clear high"></div>
		</body>
	</html>
<?php endif;?>