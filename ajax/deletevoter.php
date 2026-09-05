<?php
	require("../connect.php");
	$stmt = $link->prepare('delete from voters where vin=?');
	$stmt->execute([$_GET["vin"]]);
	echo "Success";
?>