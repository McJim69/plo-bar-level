<?php
	require("../connect.php");
	$stmt = $link->prepare('select * from hl_children where hlvin=?');
	$stmt->execute([$_GET["vin"]]);
	$ex = $stmt;
	while($rs=$ex->fetch(PDO::FETCH_BOTH)){
		$stmt = $link->prepare('update voters set ato=\'\' where vin=?');
	$stmt->execute([$rs["vin"]]);
	}
	$stmt = $link->prepare('update voters set ato=\'\' where vin=?');
	$stmt->execute([$_GET["vin"]]);
	$stmt = $link->prepare('delete from hl where vin=?');
	$stmt->execute([$_GET["vin"]]);
	echo "Success";
?>