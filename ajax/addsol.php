<?php
	require("../connect.php");
	$stmt = $link->prepare('select * from sollist where vin=?');
	$stmt->execute([$_GET["vin"]]);
	$ex = $stmt;
	if(!($rs=$ex->fetch(PDO::FETCH_BOTH))){
		$stmt = $link->prepare('insert into sollist values(0,?,?)');
	$stmt->execute([$_GET["vin"], $_GET["remarks"]]);
		$stmt = $link->prepare('update voters set ato=\'Dili Ato\' where vin=?');
	$stmt->execute([$_GET["vin"]]);
		echo "Success";
	}else{
		echo "UNABLE to add Dili Ato\nVoter is already Dili Ato.";
	}
?>
