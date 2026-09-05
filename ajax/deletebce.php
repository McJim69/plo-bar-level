<?php
	require("../connect.php");
	$stmt = $link->prepare('select * from bce where bno=?');
	$stmt->execute([$_GET["bno"]]);
	$ex = $stmt;
	$rs=$ex->fetch(PDO::FETCH_BOTH);
	$bce=$rs["vin"];
	
	$stmt = $link->prepare('select * from pl where bcevin=?');
	$stmt->execute([$bce]);
	$ex = $stmt;
	$stmt = $link->prepare('select * from hl where plvin=?');
	$stmt->execute([$bce]);
	$ex1 = $stmt;

	while($rs=$ex1->fetch(PDO::FETCH_BOTH)){
		$stmt = $link->prepare('update voters set ato=\'\' where vin=?');
	$stmt->execute([$rs["vin"]]);

	while($rs=$ex->fetch(PDO::FETCH_BOTH)){
		$stmt = $link->prepare('update voters set ato=\'\' where vin=?');
	$stmt->execute([$rs["vin"]]);
		$hl=$rs["vin"];
		$stmt = $link->prepare("select * from hl_children where hlvin={$hl}");
	$stmt->execute([]);
	$ex2 = $stmt;
		while($rs2=$ex2->fetch(PDO::FETCH_BOTH)){
			$stmt = $link->prepare('update voters set ato=\'\' where vin=?');
	$stmt->execute([$rs2["vin"]]);
			}
		}
	}	
		
	$stmt = $link->prepare('update voters set ato=\'\' where vin=?');
	$stmt->execute([$bce]);
	
	$stmt = $link->prepare('delete from bce where bno=?');
	$stmt->execute([$_GET["bno"]]);
	echo "Success";
?>