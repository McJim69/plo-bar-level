<?php
	require("../connect.php");
	$stmt = $link->prepare('delete from hl_children where hlcno=?');
	$stmt->execute([$_GET["hlcno"]]);
	$stmt = $link->prepare('update voters set ato=\'\' where vin=?');
	$stmt->execute([$_GET["vin"]]);
	echo "Success";
?>