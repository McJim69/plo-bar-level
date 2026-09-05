<?php
	require("../connect.php");
	$stmt = $link->prepare('update sollist set remarks=? where vin=?');
	$stmt->execute([$_GET["remarks"], $_GET["vin"]]);
	$stmt = $link->prepare('update voters set ato=\'Dili Ato\' where vin=?');
	$stmt->execute([$_GET["vin"]]);
?>