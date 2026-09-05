<?php
	require("../connect.php");
	$stmt = $link->prepare('select * from officer where vin=?');
	$stmt->execute([$_GET["vin"]]);
	$ex = $stmt;
	if(!($rs=$ex->fetch(PDO::FETCH_BOTH))){
		$stmt = $link->prepare('insert into officer values(0,?,?)');
	$stmt->execute([$_GET["vin"], $_GET["remarks"]]);
		echo "Success";
	}else{
		echo "UNABLE to add Information Officer\nVoter is already Information Officer.";
	}
?>
