<?php
	require("../connect.php");
	$stmt = $link->prepare('delete from counter where vin=?');
	$stmt->execute([$_GET["vin"]]);
	echo "Success";
?>