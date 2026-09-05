<?php
	require("connect.php");
	
	if(isset($_POST['update'])){
		$vin = $_POST['vin'];
		$access = $_POST['access'];
		$username = $_POST['username'];
		$password = $_POST['password'];
		$gender = $_POST['gender'];		
		$contact = $_POST['contact'];
		
		$stmt = $link->prepare("UPDATE users set access=?, username=?, password=?, gender=?, contact=? where vin=?");
		if($stmt->execute([$access, $username, $password, $gender, $contact, $vin]) == TRUE){
			header("location:users.php");			
		}else{
			echo "<script>alert('ERROR! Failled to Update Data!');
			window.location.href = '';</script>";
		}
	}	

	echo"<form action='usersupdate.php' method='POST'>";

	$sql = "select * from users m where m.vin=m.vin";
	$params = [];
	if(isset($_GET["users"]) && $_GET["users"] != "") {
		$sql .= " and m.vin = ?";
		$params[] = $_GET["users"];
	}
				
	$stmt = $link->prepare($sql);
	$stmt->execute($params);
	$ex = $stmt;

	while($rs=$ex->fetch(PDO::FETCH_BOTH)){
	
	echo"<center><div style='border-radius:5px;padding-top:5px'>";			

	if(file_exists("images/voters/".$rs["0"].".jpg")){
		echo"<img src='images/voters/".$rs["0"].".jpg?".date("h:i:s")."' height='290px' style='border-radius:5px'/>";
	} else
		echo"<img src='images/blank.png' height='290px' style='border-radius:5px'/>";
	
	echo"
		<div style='margin:-5px'>&nbsp;</div>
		<div style='background:#FFF;width:280px;border:1px solid #bbb;border-radius:5px;padding:5px'>
			<x style='text-transform:uppercase'>".$rs["username"]."</x> - "; $cont = $rs["vin"]; printf("%04d", $cont); echo" - ".$rs["access"]."
		</div>";

	echo"</center><br>";

	echo"<table class='no_style' style='color:#FFF'>	

			<tr style='background:transparent'>
				<td align='left' style='padding-left:5px'>Access</td>
				<td style='padding:2px'>
					<select style='padding-left:10px;padding-top:0;padding-bottom:0;width:230px' required name=access >
						<option value=''>Access Level</option>		
						<option value='IT Staff' ".($rs["access"] === 'IT Staff' ? 'selected' : '').">Level 1 = IT Staff</option>
						<option value='Manager' ".($rs["access"] === 'Manager' ? 'selected' : '').">Level 2 = Manager</option>
						<option value='SuperAdmin' ".($rs["access"] === 'SuperAdmin' ? 'selected' : '').">Level 3 = SuperAdmin</option>
					</select><br/>
				</td>
			</tr>
			<tr style='background:transparent;display:none'>
				<td align='left' style='padding-left:5px'>Username</td>
				<td style='padding:2px'><input type='text' name=vin value='".$rs["vin"]."' style='width:230px' required /><br/></td>
			</tr>

			<tr style='background:transparent'>
				<td align='left' style='padding-left:5px'>Username</td>
				<td style='padding:2px'><input type='text' name=username value='".$rs["username"]."' style='width:230px' required /><br/></td>
			</tr>

			<tr style='background:transparent'>
				<td align='left' style='padding-left:5px'>Password</td>
				<td style='padding:2px'><input type='text' name=password value='".$rs["password"]."' style='width:230px' required /><br/></td>
			</tr>

			<tr style='background:transparent'>
				<td align='left' style='padding-left:5px'>Gender</td>
				<td style='width:230px;padding:2px'><center>
					<div style='font-family:arial;background:#FFF;color:#545454;padding:5px;border:1px solid #f87700;border-radius:20px'>
						<span type='radio'><input type='radio' name='gender' value='M' required ".($rs["gender"] === 'M' ? 'checked' : '')." />Male</span>&nbsp;&nbsp;&nbsp;
						<span type='radio'><input type='radio' name='gender' value='F' required ".($rs["gender"] === 'F' ? 'checked' : '')." />Female</span>&nbsp;&nbsp;&nbsp;						
						<span type='radio'><input type='radio' name='gender' value='B' required ".($rs["gender"] === 'B' ? 'checked' : '')." />Both</span>
					</div></center>
				</td>
			</tr>

			<tr style='background:transparent'>
				<td align='left' style='padding-left:5px'>Contact</td>
				<td style='padding:2px'><input type='text' name=contact value='".$rs["contact"]."' style='width:230px' required /><br/></td>
			</tr>
			
			<tr align='left' style='background:transparent'>
				<td>&nbsp;</td>
				<td class='update'><br/>
				<input type='SUBMIT' name=update id='btnnav' value='Update'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href=''><input type='button' value='Cancel'/></a>
				</td>
			</tr>				
		</table>";		
		
	}				

	echo"</form>";

?>
