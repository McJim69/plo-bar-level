<?php
	require("../connect.php");
	$stmt = $link->prepare('SELECT * FROM bce WHERE vin=?');
	$stmt->execute([$_GET["vin"]]);
	$ex = $stmt;
	if(!($rs=$ex->fetch(PDO::FETCH_BOTH))){
		$stmt = $link->prepare('INSERT INTO bce VALUES(0,?,?,0,0,0,\'\',\'\',\'\',\'\',\'\')');
	$stmt->execute([$_GET["mcevin"], $_GET["vin"]]);
		$stmt = $link->prepare('update voters set ato=\'Sure\' where vin=?');
	$stmt->execute([$_GET["vin"]]);

		echo "Success";
	}else{
		echo "UNABLE to add BCG Member.\nVoter is already assigned to a BCE.";
	}
?>