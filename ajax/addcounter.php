<?php
	require("../connect.php");
	$stmt = $link->prepare('select * from counter where vin=?');
	$stmt->execute([$_GET["vin"]]);
	$ex = $stmt;
	if(!($rs=$ex->fetch(PDO::FETCH_BOTH))){
		$stmt = $link->prepare('INSERT INTO counter VALUES(?,0,0,0,\'\',\'\',\'\',\'\',\'\')');
	$stmt->execute([$_GET["vin"]]);
		echo "Success";
	}else{
		echo "UNABLE to add PLO User.\nVoter is already a PLO User.";
	}
?>