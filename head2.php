<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8"/>

<title>PLO Database v.5.23</title>

<link rel="shortcut icon" href="images/iloveyou-icon.png"/>
<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox-1.3.3.css" media="screen" />
<link href="css/stylesheet.css" rel="stylesheet" type="text/css"/>
<link href="css/menu.css" rel="stylesheet" type="text/css"/>
<link href="facebox/facebox.css" media="screen" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="fancybox/jquery.fancybox-1.3.3.pack.js"></script>
<script type="text/javascript" src="scripts/jquery-1.7.1.min.js" ></script>
<script src="facebox/facebox.js" type="text/javascript"></script>
<script type="text/javascript">
	jQuery(document).ready(function($) {
	  $('a[rel*=facebox]').facebox({
		loadingImage : 'facebox/loading.gif',
		closeImage   : 'facebox/closelabel.png'
	  })
	})
</script>

<script type="text/javascript" src="fancybox/jquery.fancybox-1.3.3.pack.js"></script>
<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox-1.3.3.css" media="screen" />

<script>
	if (window.XMLHttpRequest)
		xmlhttp=new XMLHttpRequest();
	else
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");				
	function getID(id){
		return document.getElementById(id);
	}
	function conf(){
		return confirm("Are you Sure?");
	}
	function jump(page){
		window.location=page;
	}
	function setActive(id){
		getID(id).style.background="#b61212";
		getID(id).style.color="#fff";
		getID(id).style.fontWeight="bold";
	}
</script>

</head>

<?php
	function jump($page){
		echo "<script>window.location='$page'</script>";
	}
?>	

