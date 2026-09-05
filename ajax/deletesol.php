<?php
	require("../connect.php");
	
	// Fetch the vin first using solno
	$stmt = $link->prepare('select vin from sollist where solno=?');
	$stmt->execute([$_GET["solno"]]);
	$vin = $stmt->fetchColumn();

	$stmt = $link->prepare('delete from sollist where solno=?');
	$stmt->execute([$_GET["solno"]]);

	if ($vin) {
		$stmt = $link->prepare('update voters set ato=\'\' where vin=?');
		$stmt->execute([$vin]);
	}
	
	echo "Success";
?>