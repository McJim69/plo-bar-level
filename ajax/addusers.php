<?php
	require("../connect.php");
	$stmt = $link->prepare('select * from users where vin=?');
	$stmt->execute([$_GET["vin"]]);
	$ex = $stmt;
	if(!($rs=$ex->fetch(PDO::FETCH_BOTH))){
		$stmt = $link->prepare('insert into users values(?,?,?,?,?,0,0,0,\'\',\'\',\'\',\'\',\'\')');
	$stmt->execute([$_GET["vin"], $_GET["position"], $_GET["username"], $_GET["password"], $_GET["gender"]]);
		echo "Success";
	}else{
		echo "UNABLE to add PLO User.\nVoter is already a PLO User.";
	}
?>