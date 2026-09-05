<?php
	require("../connect.php");

	$link->query("use paga_sanpedro");
	$link->query("drop table if exists counter");
	$link->query("CREATE TABLE counter (
		vin VARCHAR(50) primary key,
		old_new int,
		isoff int,
		y_dec int,
		nof VARCHAR(30),
		nom VARCHAR(30),
		nohw VARCHAR(30),
		pof_nohw VARCHAR(100),
		contact	VARCHAR(20),
		foreign key(vin) references voters(vin) on delete cascade)");
		
	header("location:../counter.php");	
?>
