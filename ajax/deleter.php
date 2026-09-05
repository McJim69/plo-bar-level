<?php
	require("../connect.php");
	$stmt = $link->prepare('delete from ? where ?=?');
	$stmt->execute([$_GET["table"], $_GET["field"], $_GET["value"]]);
	echo "Success";
?>