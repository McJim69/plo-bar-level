<?php
	require("../connect.php");
	$stmt = $link->prepare('SELECT * FROM pl WHERE vin=?');
	$stmt->execute([$_GET["vin"]]);
	$ex = $stmt;
	if(!($rs=$ex->fetch(PDO::FETCH_BOTH))){
		$stmt = $link->prepare('INSERT INTO pl VALUES(0,?,?,0,0,0,\'\',\'\',\'\',\'\',\'\')');
	$stmt->execute([$_GET["bcevin"], $_GET["vin"]]);
		$stmt = $link->prepare('update voters set ato=\'Sure\' where vin=?');
	$stmt->execute([$_GET["vin"]]);
		echo "Success";
	}else{
		echo "UNABLE to add Precint Leader.\nVoter is already assigned to a BCG.";
	}
?>