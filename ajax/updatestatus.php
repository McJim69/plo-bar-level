<?php
	require("../connect.php");
	
	$target = $_GET["target"] ?? '';
	$allowed = ['ato', 'status', 'remarks'];
	if (!in_array($target, $allowed)) {
		die("Invalid target column");
	}

	$stmt = $link->prepare("update voters set {$target}=? where vin=?");
	$stmt->execute([$_GET["value"], $_GET["vin"]]);
	
	$stmt = $link->prepare("update hl_children set remarks='' where vin=?");
	$stmt->execute([$_GET["vin"]]);
?>