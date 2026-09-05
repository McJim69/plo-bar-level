<?php
	require("../connect.php");
	
	$allowed_tables = ['mce', 'bce', 'pl', 'hl', 'officer', 'special', 'sollist', 'users'];
	$table = $_GET["table"];
	$vin = $_GET["vin"];
	$contact = isset($_GET["contact"]) ? $_GET["contact"] : '';

	if (!in_array($table, $allowed_tables)) {
		die("ERROR: Invalid table name.");
	}

	$stmt = $link->prepare("select * from `$table` where vin=?");
	$stmt->execute([$vin]);
	$ex = $stmt;
	
	if(!($rs=$ex->fetch(PDO::FETCH_BOTH))){
		$stmt = $link->prepare("update voters set ato='Sure' where vin=?");
		$stmt->execute([$vin]);
	}else{
		$stmt = $link->prepare("update `$table` set contact=? where vin=?");
		$stmt->execute([$contact, $vin]);
	}
?>