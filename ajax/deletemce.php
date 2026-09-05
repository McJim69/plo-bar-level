<?php
	require("../connect.php");
	$stmt = $link->prepare('select * from bce where mcevin=?');
	$stmt->execute([$_GET["vin"]]);
	$ex3 = $stmt;
	while($rs3=$ex3->fetch(PDO::FETCH_BOTH)){
		$stmt = $link->prepare('update voters set ato=\'\' where vin=?');
	$stmt->execute([$rs3["vin"]]);

	$stmt = $link->prepare('select * from pl where bcevin=?');
	$stmt->execute([$rs3["vin"]]);
	$ex = $stmt;
	$stmt = $link->prepare('select * from hl where plvin=?');
	$stmt->execute([$rs3["vin"]]);
	$ex1 = $stmt;

	while($rs=$ex->fetch(PDO::FETCH_BOTH)){
		$stmt = $link->prepare('update voters set ato=\'\' where vin=?');
	$stmt->execute([$rs["vin"]]);
		$hl=$rs["vin"];

	while($rs=$ex1->fetch(PDO::FETCH_BOTH)){
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
	}
	$stmt = $link->prepare('update voters set ato=\'\' where vin=?');
	$stmt->execute([$_GET["vin"]]);
	
	$stmt = $link->prepare('delete from mce where vin=?');
	$stmt->execute([$_GET["vin"]]);
	echo "Success";
?>