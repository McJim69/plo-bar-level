<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<script>
	// Initialize theme immediately to prevent flashing
	(function() {
		const savedTheme = localStorage.getItem('theme') || 'light';
		document.documentElement.setAttribute('data-theme', savedTheme);
		document.documentElement.setAttribute('data-bs-theme', savedTheme);
	})();
</script>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>PLO Database v.5.23</title>
<link rel="shortcut icon" href="images/iloveyou-icon.png"/>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

<script>
	function toggleTheme() {
		const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
		const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
		document.documentElement.setAttribute('data-theme', newTheme);
		document.documentElement.setAttribute('data-bs-theme', newTheme);
		localStorage.setItem('theme', newTheme);
		updateThemeButtonText(newTheme);
	}

	function updateThemeButtonText(theme) {
		const themeToggle = document.querySelector('.theme-toggle-btn');
		if (themeToggle) {
			themeToggle.setAttribute('title', theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode');
		}
	}

	jQuery(document).ready(function($) {
		const savedTheme = localStorage.getItem('theme') || 'light';
		updateThemeButtonText(savedTheme);
	});
</script>

</head>

<?php
	function jump($page){
		echo "<script>window.location='".$page."'</script>";
	}
?>	

<body>
