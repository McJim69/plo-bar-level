<?php
	require("connect.php");
	require("head.php");

	$ex = $link->query("SELECT * from voters v, hl h, clusters WHERE h.vin=v.vin ORDER BY v.vname");
	
	echo"
	
	<table width='30%'>
		<tr>
		<th>NO</th>
		<th>HL NAME</th>
		<th>PRECINCT</th>
		<th>CLUSTER</th>
		<th>CODE</th>
		</tr>
		";
	$i=1;
	
	while($rs=$ex->fetch(PDO::FETCH_BOTH)){
		
	$stmt = $link->prepare('SELECT cluster FROM clusters WHERE precinct=?');
	$stmt->execute([$rs["precinct"]]);
	$cluster = $stmt;
	$rsc=$cluster->fetch(PDO::FETCH_BOTH);

		echo"<tr>";
		echo"<td style='border:1px solid #bbb'>".$i.".</td>
			<td style='border:1px solid #bbb'>".$rs["vname"]."</td>
			<td style='border:1px solid #bbb'>".$rs["precinct"]."</td>
			<td style='border:1px solid #bbb'>".$rsc[0]."</td>
			<td style='border:1px solid #bbb'>TestCode</td>
		</tr>";

		$i++;
	}

	echo"</table>";
?>
