<?php
	require("connect.php");
	require("head.php");
	if(isset($_POST["bSave"])){
		$stmt = $link->prepare('insert into unregistered values(0,?,?,?,?,?)');
	$stmt->execute([$_POST["t1"], $_POST["t2"], $_POST["t3"], $_POST["t4"], $_POST["t5"]]);
			echo"<script>window.location='unregistered.php';</script>";
	}
	
?>

<?php require("menu.php"); ?>
<script>setActive("unreg");</script>

<div id="t_controls">
	<div class="controls">
		<div>
			<table style="margin:0 auto;">
				<tr style="background:transparent" >
					<td style="padding:10px;color:#fff;font-size:25px;border:0">
						<b>LIST OF UNREGISTERED VOTERS</b>
					</td>
					<td style="border:0">
						<select style='padding:1px 15px 1px 15px;width:200px' onchange="jump('?barangay='+this.value)" >
							<option>All barangays</option>
							<?php
								$stmt = $link->prepare('select barangay from voters where city_mun=? group by barangay');
								$stmt->execute([$_SESSION["city_mun"]]);
								$ex = $stmt;
								while($rs=$ex->fetch(PDO::FETCH_BOTH)){
									echo"<option ";
										if($_GET["barangay"]==$rs[0]){
											echo " selected ";
										}
									echo" >".$rs["0"]."</option>";
								}
							?>
						</select>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class='del'><br><br></div>
</div>

<div style="padding:10px;color:#fff;font-size:25px;color:#000"><b>LIST OF UNREGISTERED VOTERS</b>
	<div id='other' style="display:none">
		<?php
			if($_GET["barangay"]!="" && $_GET["barangay"]!="All barangays")
				echo "Barangay ".$_GET["barangay"]."<br>";
				
			echo "MUNICIPALITY OF ".$_SESSION["city_mun"];
		?>
	</div>
</div>

<div class='del'><br></div>

<div style="width:1000px;margin:0 auto;">
	<table width=100% >
		<tr style="background:transparent" >
			<td width=300 style="border:0;" valign=top id='form' >
			<form method=post>
				<input required name=t1 type=text style="width:100%" placeholder="Fullname" autofocus /><div style="display:block;padding:3px;" ></div>
				<input required name=t2 type=text style="width:100%" placeholder="Age" /><div style="display:block;padding:3px;" ></div>
				<select style='padding:5px;width:100%' name='t3' >
					<?php
						$stmt = $link->prepare('select barangay from voters where city_mun=? group by barangay');
						$stmt->execute([$_SESSION["city_mun"]]);
						$ex = $stmt;
						while($rs=$ex->fetch(PDO::FETCH_BOTH)){
							echo "<option>".$rs["0"]."</option>";
						}
					?>
				</select>
				<div style="display:block;padding:3px;" ></div>
				<input required name=t4 type=text style="width:100%" placeholder="Household Leader" /><div style="display:block;padding:3px;" ></div>
				<input required name=t5 type=text style="width:100%" placeholder="Precinct Leader" /><div style="display:block;padding:3px;" ></div>
				<input type=submit value="Save" name='bSave' /> <input type=reset value="Cancel" />
			</form>
			</td>
		</tr>
	</table>
	<br>
<style>
	td,th{
		padding:1px 1px 1px 5px;
		color:#545454;
	}
</style>

	<table width=100% >
		<form method=post>
			<td style="border:0;padding:0" valign=top >
				<table width=100% >
					<tr>
						<th style="color:#FFF" colspan=2>FULLNAME</th>
						<th style="color:#FFF">AGE</th>
						<th style="color:#FFF">BARANGAY</th>
						<th style="color:#FFF">HOUSEHOLD LEADER</th>
						<th style="color:#FFF">PRECINCT LEADER</th>
						<th style="color:#FFF"><input type=button value="Print" onclick="printF()" id="print" /></th>
					</tr>
					
					<?php
						$i=1;
						$bar="";
						if($_GET["barangay"]!="" && $_GET["barangay"]!="All barangays")
							$bar=$_GET["barangay"];
							
						$stmt = $link->prepare("select * from unregistered where brgy like {$bar}%' order by fullname ");
						$stmt->execute([]);
						$ex = $stmt;
						while($rs=$ex->fetch(PDO::FETCH_BOTH)){
							$c="odd";
							if($i%2==0)
								$c="even";
							echo"
							<tr class='".$c."'>
								<td>".$i.".</td>
								<td>".$rs["1"]."</td>
								<td>".$rs["2"]."</td>
								<td>".$rs["3"]."</td>
								<td>".$rs["4"]."</td>
								<td>".$rs["5"]."</td>
								<td><input onclick=\"return confirm('Are you sure?');\" name='del".$rs["0"]."' type=image src='images/delete.png' class='del' /></td>
							</tr>";
							
							if(isset($_POST["del".$rs[0]."_x"])){
								$stmt = $link->prepare('delete from unregistered where uno=?');
								$stmt->execute([$rs["0"]]);
								echo"<script>window.location='unregistered.php';</script>";
							}
							$i++;
						}
					?>
				</table>
			</td>
		</form>
	</table>
</div>

<script>
	function printF(){
		$("#form").css("display","none");
		$("#t_controls").css("display","none");
		$("#print").css("display","none");
		$("#other").css("display","block");
		$(".del").css("display","none");
	
		window.print();
		$("#form").css("display","table-cell");
		$("#t_controls").css("display","block");
		$("#print").css("display","block");
		$(".del").css("display","block");
		$("#other").css("display","none");
	}
</script>

</body>

</html>