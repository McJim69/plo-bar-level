<?php
	// Include connection but override authentication checking since this is used on login page
	require("../connect.php");

	$city_mun = isset($_GET["city_mun"]) ? $_GET["city_mun"] : "";

	if ($city_mun !== "") {
		$stmt = $link->prepare("select barangay from voters where city_mun=? group by barangay order by barangay");
		$stmt->execute([$city_mun]);
		$ex = $stmt;
		while ($rs = $ex->fetch(PDO::FETCH_BOTH)) {
			echo "<option style='color:#000;' value='" . htmlspecialchars($rs["barangay"]) . "'>" . htmlspecialchars($rs["barangay"]) . "</option>";
		}
	}
?>
