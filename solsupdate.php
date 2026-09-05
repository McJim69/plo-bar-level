<?php
	require("connect.php");
	
	if(isset($_POST['update'])){
		$rem = $_POST['remarks'];
		$vin = $_POST['vin'];
		
		$stmt = $link->prepare("UPDATE sollist set remarks=? where vin=?");
		if($stmt->execute([$rem, $vin]) == TRUE){
			header("location:sollist.php");			
			exit();
		}else{
			echo "<script>alert('ERROR! Failed to Update Data!');
			window.location.href = '';</script>";
		}
	}	

	echo"<form action='solsupdate.php' method='POST'>";

	$sql = "select s.*, v.vname from sollist s, voters v where s.vin=v.vin";
	$params = [];
	if(isset($_GET["sollist"]) && $_GET["sollist"] != "") {
		$sql .= " and s.vin = ?";
		$params[] = $_GET["sollist"];
	}
				
	$stmt = $link->prepare($sql);
	$stmt->execute($params);
	$ex = $stmt;

	while($rs=$ex->fetch(PDO::FETCH_BOTH)){
	
		echo"<center><div style='border-radius:5px;padding-top:5px'>";			
	
		if(file_exists("images/voters/".$rs["vin"].".jpg")){
			echo"<img src='images/voters/".$rs["vin"].".jpg?".date("h:i:s")."' height='290px' style='border-radius:5px'/>";
		} else {
			echo"<img src='images/blank.png' height='290px' style='border-radius:5px'/>";
		}
		
		echo"
			<div style='margin:-5px'>&nbsp;</div>
			<div style='background:#FFF;width:280px;border:1px solid #bbb;border-radius:5px;padding:5px'>
				<x style='text-transform:uppercase'>".$rs["vname"]."</x> - "; 
				$cont = $rs["vin"]; 
				printf("%04d", $cont); 
				echo "
			</div>
		</center><br>";
	
		echo"
		<table class='no_style' style='color:#FFF'>	
			<tr style='background:transparent;display:none'>
				<td style='padding:2px'><input type='text' name='vin' value='".$rs["vin"]."' required /></td>
			</tr>
			<tr style='background:transparent'>
				<td align='left' style='padding-left:5px'>Remarks</td>
				<td style='padding:2px'><input type='text' name='remarks' value='".htmlspecialchars($rs["remarks"] ?? '')."' style='width:230px' /><br/></td>
			</tr>
			<tr align='left' style='background:transparent'>
				<td>&nbsp;</td>
				<td class='update'><br/>
				<input type='SUBMIT' name='update' id='btnnav' value='Update'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href='sollist.php'><input type='button' value='Cancel'/></a>
				</td>
			</tr>				
		</table>";
	}				

	echo"</form>";
?>
