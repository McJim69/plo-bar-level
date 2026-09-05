<?php
	require("../connect.php");
	$stmt = $link->prepare('delete from users where vin=?');
	$stmt->execute([$_GET["vin"]]);
	$stmt = $link->prepare('update voters set ato=\'\' where vin=?');
	$stmt->execute([$_GET["vin"]]);
	echo "Success";
?>