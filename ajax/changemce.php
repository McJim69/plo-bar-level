<?php
	require("../connect.php");
	$stmt = $link->prepare('update hl set plvin=? where vin=? ');
	$stmt->execute([$_GET["pl"], $_GET["hl"]]);
	$ex = $stmt;
	$stmt = $link->prepare('update hl set plvin=? where plvin=? ');
	$stmt->execute([$_GET["tobce"], $_GET["frombce"]]);
	$ex = $stmt;
?>