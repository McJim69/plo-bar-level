<?php
	require("../connect.php");
	$stmt = $link->prepare('update bce set mcevin=? where vin=? ');
	$stmt->execute([$_GET["mce"], $_GET["bce"]]);
	$ex = $stmt;
?>