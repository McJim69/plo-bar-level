<?php
	require("../connect.php");
	$stmt = $link->prepare('update hl_children set remarks=? where vin=?');
	$stmt->execute([$_GET["remarks"], $_GET["vin"]]);
?>