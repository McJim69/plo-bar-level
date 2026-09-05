<?php
	require("../connect.php");
	$stmt = $link->prepare('SELECT * FROM hl WHERE vin=?');
	$stmt->execute([$_GET["vin"]]);
	$ex = $stmt;
	if(!($rs=$ex->fetch(PDO::FETCH_BOTH))){
		$stmt = $link->prepare('INSERT INTO hl VALUES(0,?,?,0,0,0,\'\',\'\',\'\',\'\',\'\')');
	$stmt->execute([$_GET["plvin"], $_GET["vin"]]);
		$stmt = $link->prepare('update voters set ato=\'Sure\' where vin=?');
	$stmt->execute([$_GET["vin"]]);
		echo "Success";
	}else{
		echo "UNABLE to add Household Leader.\nVoter is already assigned to a PL.";
	}
?>