<?php
	require("../connect.php");
	$stmt = $link->prepare('select * from hl_children where vin=?');
	$stmt->execute([$_GET["vin"]]);
	$ex = $stmt;
	if(!($rs=$ex->fetch(PDO::FETCH_BOTH))){
		$stmt = $link->prepare('insert into hl_children values(0,?,?,?)');
	$stmt->execute([$_GET["hlvin"], $_GET["vin"], $_GET["remarks"]]);
		$stmt = $link->prepare('update voters set ato=\'Sure\' where vin=?');
	$stmt->execute([$_GET["vin"]]);
		echo "Success";
	}else{
		echo "UNABLE to add Household Member.\nVoter is already assigned to a Household Leader.";
	}
?>
