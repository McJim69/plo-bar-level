<?php
	require("../connect.php");
	$stmt = $link->prepare('update hl_children set hlvin=? where hlvin=? ');
	$stmt->execute([$_GET["toHL"], $_GET["fromHL"]]);
	$ex = $stmt;
?>












