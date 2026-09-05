<?php
	require("../connect.php");
	$stmt = $link->prepare('update pl set bcevin=? where bcevin=? ');
	$stmt->execute([$_GET["tobce"], $_GET["frombce"]]);
	$ex = $stmt;	
?>