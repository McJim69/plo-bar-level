<?php
	require("../connect.php");
	
	// Fetch the vin first using iono
	$stmt = $link->prepare('select vin from officer where iono=?');
	$stmt->execute([$_GET["iono"]]);
	$vin = $stmt->fetchColumn();

	$stmt = $link->prepare('delete from officer where iono=?');
	$stmt->execute([$_GET["iono"]]);

	if ($vin) {
		$stmt = $link->prepare('update voters set ato=\'\' where vin=?');
		$stmt->execute([$vin]);
	}
	
	echo "Success";
?>