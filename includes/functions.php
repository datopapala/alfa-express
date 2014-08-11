<?php

/*
 *
 * Get css imports
 *
 */

function GetCss(){
	$data = '
	<!-- Main -->
	<link rel="stylesheet" href="media/css/datatable.css" type="text/css" />
	<link rel="stylesheet" href="media/css/datatable_forms.css" type="text/css" />
	<link rel="stylesheet" href="media/css/datatable_inner_tables.css" type="text/css" />
	<link rel="stylesheet" href="media/css/datatable_loading.css" type="text/css" />
	<link rel="stylesheet" href="media/css/datatable_pos.css" type="text/css" />
	<link rel="stylesheet" href="media/css/datatable_seoy.css" type="text/css" />
	<link rel="stylesheet" href="media/css/datatable_table.css" type="text/css" />
	<link rel="stylesheet" href="media/css/datatable_tree.css" type="text/css" />
	
	<!-- Plugins -->
	<link rel="stylesheet" href="media/css/TableTools_JUI.css" type="text/css" />
	<link rel="stylesheet" href="media/css/jquery-ui-timepicker-addon.css" type="text/css" />
	<link rel="stylesheet" href="media/css/jNotify.jquery.css" type="text/css" />
	
	<!-- jQuery UI -->
	<link rel="stylesheet" href="media/themes/jQuery-UI/ui-lightness/jquery-ui.min.css" type="text/css" />
	<link rel="stylesheet" href="media/themes/jQuery-UI/ui-lightness/jquery-ui.datatable.css" type="text/css" />
	<!-- <link rel="stylesheet" href="media/themes/jQuery-Mobile/default/jquery.mobile-1.2.0.css" type="text/css" /> -->
	';
	return $data;
}

/*
 *
 * Get javascript imports
 *
 */
function GetJs(){
	$data = '
	<script type="text/javascript" language="javascript" src="js/jquery-1.9.1.min.js"></script>
	
	<script type="text/javascript" language="javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
	<!-- <script type="text/javascript" language="javascript" src="js/jquery.mobile-1.2.0.min.js"></script> -->
	
	<script type="text/javascript" language="javascript" src="js/jquery.ui.datepicker-ka.min.js"></script>
	
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="js/TableTools.min.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.columnFilter.js"></script>
	<script type="text/javascript" language="javascript" src="js/ColReorderWithResize.js"></script>
	<script type="text/javascript" language="javascript" src="js/dataTables.action.js"></script>
	
	<!-- Plugins -->
	<script type="text/javascript" language="javascript" src="js/jquery-ui-timepicker-addon.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery-ui-timepicker-ka.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery-ui-sliderAccess.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.cookie.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.hoverIntent.minified.js"></script>
	
	<script type="text/javascript" language="javascript" src="js/ajaxfileupload.js"></script>
	<script type="text/javascript" language="javascript" src="js/phpjs.js"></script>
	
	<script type="text/javascript" src="js/jNotify.jquery.min.js"></script>
	';
	return $data;
}
?>