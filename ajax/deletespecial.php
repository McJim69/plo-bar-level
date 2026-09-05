<?php
	require("../connect.php");
	
	// Fetch the vin first using spno
	$stmt = $link->prepare('select vin from special where spno=?');
	$stmt->execute([$_GET["spno"]]);
	$vin = $stmt->fetchColumn();

	$stmt = $link->prepare('delete from special where spno=?');
	$stmt->execute([$_GET["spno"]]);

	if ($vin) {
		$stmt = $link->prepare('update voters set ato=\'\' where vin=?');
		$stmt->execute([$vin]);
	}
	
	echo "Success";
?>