<?php
	require("../connect.php");
	$stmt = $link->prepare('select * from special where vin=?');
	$stmt->execute([$_GET["vin"]]);
	$ex = $stmt;
	if(!($rs=$ex->fetch(PDO::FETCH_BOTH))){
		$stmt = $link->prepare('insert into special values(0,?,?)');
	$stmt->execute([$_GET["vin"], $_GET["remarks"]]);
		echo "Success";
	}else{
		echo "UNABLE to add Special Operation List\nVoter is already in Special Operation List.";
	}
?>
