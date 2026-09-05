<?php
	require("config.php");

	$dbhost = DB_HOST;
	$dbuser = DB_USER;
	$dbpass = DB_PASS;
	$dbname = DB_NAME;

	try{
		$pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", '$dbuser', '$dbpass', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		//echo 'Connection Successfull';
	}catch(PDOException $error){
		echo $error->getmessage();
	}
?>