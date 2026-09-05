<?php
	error_reporting(0);
	session_start();	

	require("config.php");

	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	if($link === false){
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}
?>