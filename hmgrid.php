<?php
	require("connect.php");
	require("head.php");
?>

<script>
	var table=0;
	var hlvotno=0;
</script>

<script>
	function printF(){
		getID('header').style.display='block';
		getID('t_controls').style.visibility='hidden'; 
		getID('bottom').style.display='block'; 
		$(".spacer").css("display","none");
		$(".hid").css("display","none");
			
	window.print();
		$(".hid").css("display","block");
		$(".spacer").css("display","block");
		getID('header').style.display='none'; 
		getID('bottom').style.display='block'; 
		getID('t_controls').style.visibility='visible'; 
	}
</script>

<br><br>

<div id="t_controls" style="box-shadow:2px 0 10px #000;border-bottom:5px solid #801919;background:#b61212;position:fixed;z-index:100;width:100%;top:38;left:0">

<?php require("menu.php"); ?>	

<script> setActive("hm"); </script>

	<table style="margin:0 auto" >
		<tr style="background:#b61212;" class="no_style" >
		<form method=post enctype="multipart/form-data"  >
			<td colspan=14> <br>
				<table>
					<tr style="background:#b61212;color:white;">
						<td style="padding:0 0 0 15px;" ><input onfocus="this.value=''"  type=text name="t_search" value="<?php if($_POST["t_search"]!=""){echo $_POST["t_search"];}else{echo "Type a keyword";} ?>" size=30 /></td>
						<td><input type=submit name='b_search' value="Search" /></td>
						<td><input type=button value="Refresh" onclick="jump('hmgrid.php?barangay=<?php echo $_GET["barangay"];?>')" /></td>
						<td><input type=button value="Grid View" onclick="jump('hmlist.php?barangay=<?php echo $_GET["barangay"];?>')" /></td>
						<td><input type=button value='Print' onclick="printF()" /></td>
						<td align=right >
							<select style="padding:5px;" onchange="jump('?barangay='+this.value+'&ato=<?php echo $_GET["ato"]."&lit=".$_GET["lit"]; ?>')" >
								<option>All barangays</option>
								<?php
									$stmt = $link->prepare('select barangay from voters where city_mun=? group by barangay order by barangay');
	$stmt->execute([$_SESSION["city_mun"]]);
	$ex = $stmt;
									while($rs=$ex->fetch(PDO::FETCH_BOTH)){
										echo "<option ";
											if($_GET["barangay"]===$rs[0])
												echo "selected";
										echo" >".$rs["0"]."</option>";
									}
								?>
							</select>
						</td>
						<td>
							<select style="padding:5px;" onchange="jump('?barangay=<?php echo $_GET["barangay"]; ?>&ato='+this.value+'&lit=<?php echo $_GET["lit"]; ?>')"  >
								<option <?php if($_GET["ato"]=="All")echo"selected"; ?>>Ato-All</option>
								<option <?php if($_GET["ato"]=="Sure")echo"selected"; ?>>Sure</option>
								<option <?php if($_GET["ato"]=="Sure-OT")echo"selected"; ?>>Sure-OT</option>
								<option <?php if($_GET["ato"]=="Undecided")echo"selected"; ?>>Undecided</option>
							</select>
						</td>
					</tr>
				</table> <br>
			</td>
		</tr>
	</table>
</div>

<DIV style="text-align:center;background:#fff;display:none" id='header' >
	CITY OF <?PHP echo $_SESSION["city_mun"]; ?>
	<?php
		if($_GET["barangay"]!="All barangays" && $_GET["barangay"]!="")
			echo "<br>BARANGAY ".$_GET["barangay"]."";

		if($_GET["lit"]=="A")   echo"<br>List of Illiterate";
		if($_GET["lit"]=="B")   echo"<br>List of PWD";
		if($_GET["lit"]=="C")   echo"<br>List of Senior Citizen";
		if($_GET["lit"]=="AB")  echo"<br>List of Illeterate PWD";
		if($_GET["lit"]=="AC")  echo"<br>List of Illiterate Senior Citizen";
		if($_GET["lit"]=="BC")  echo"<br>List of PWD Senior Citizen";
		if($_GET["lit"]=="ABC") echo"<br>List of Illiterate PWD Senior Citizen";

		if($_GET["ato"]=="All") echo"<br>Ato = Ato-All";
		if($_GET["ato"]=="Sure")echo"<br>Ato = Sure";
		if($_GET["ato"]=="Sure-OT")echo"<br>Ato = Sure-OT";
		if($_GET["ato"]=="Undecided")echo"<br>Ato = Undecided";
	?>
	<br>
</div>	

<div class="spacer" style="padding:20px">&nbsp;</div>

<div style="padding:2px">&nbsp;</div>

<div id="bottom" style="width:1000px;margin:0 auto" >

	<?PHP
		echo"
		<table width=100% style='border:1px solid #333'>";
			echo "
				<tr>
					<th>NO.</th>
					<th>NAME OF MEMBERS</th>
					<th>PRE</th>
					<th>SEQ</th>
					<TH>LIT</TH>
					<TH>ATO?</TH>
					<TH>REMARKS</TH>
					<th>ADDRESS</th>
					<TH>BARANGAY</TH>
				</tr>";

			$rec=10000;
			$p=$_GET['page'];
			if($p>1){
				$to=$rec;
				$from=($p*$rec)-$rec;
				$i=(($p-1)*$rec)+1;
			}
			else{
				$to=$rec;
				$from=0;
				$i=1;
				$p=1;
			}
				
				
			$lit="";
			if($_GET["lit"]!="" && $_GET["lit"]!="Literacy-All" )
				$lit=" v.lit='".$_GET["lit"]."' and ";
				
			$ato="";
			if($_GET["ato"]!="" && $_GET["ato"]!="Ato-All" )
				$ato=" v.ato='".$_GET["ato"]."' and ";
				
			$bar="";
			if($_GET["barangay"]!="" && $_GET["barangay"]!="All barangays" )
				$bar=" v.barangay='".$_GET["barangay"]."'  and ";
			
			
			$stmt = $link->prepare("select * from hl_children hc, voters v where {$bar} {$ato} {$lit} hc.vin=v.vin and v.city_mun=? order by v.vname");
	$stmt->execute([$_SESSION["city_mun"]]);
	$exz = $stmt;
			
			$stmt = $link->prepare("select * from hl_children hc, voters v where {$bar} {$ato} {$lit} hc.vin=v.vin and v.city_mun=? order by v.vname LIMIT {$from},{$to} ");
	$stmt->execute([$_SESSION["city_mun"]]);
	$ex = $stmt;
			if(isset($_POST["b_search"])){
				$stmt = $link->prepare("select * from hl_children hc, voters v where (v.vname LIKE CONCAT('%', ?, '%') or v.barangay LIKE CONCAT('%', ?, '%')) and {$bar} {$ato} {$lit} hc.vin=v.vin and v.city_mun=? order by v.vname LIMIT {$from},{$to} ");
				$stmt->execute([$_POST["t_search"], $_POST["t_search"], $_SESSION["city_mun"]]);
				$ex = $stmt;
			}
				
		
			$value=strtoupper($_POST["t_search"]);

			$rep="<b style='color:#0014d0;background:#ffa0a0'>".$value."</b>";
			
			while($rs=$ex->fetch(PDO::FETCH_BOTH)){

				$col="color:#000";
				if($rs["ato"]==="Undecided")
				$col="color:red";
				else if($rs["ato"]==="Sure-OT")
				$col="color:green";

			if($i%2==0)
				echo "<tr class='odd' id='tr_".$rs[0]."' >";
			else
				echo "<tr class='even' id='tr_".$rs[0]."' >";
										
				echo "
						<td style='".$col."' td>".$i.". </td>
						<td style='".$col."'>".$rs["vname"]."</td>
						<td style='".$col."'>".$rs["precinct"]."</td>
						<td style='".$col."'>".$rs["seq"]."</td>
						<td style='".$col."'>".$rs["lit"]."</td>
						<td style='".$col."'>".$rs["ato"]."</td>
						<td style='".$col."'>".$rs[3]."</td>						
						<td style='".$col."'>".$rs["address"]."</td>
						<td style='".$col."'>".$rs["barangay"]."</td>
					</tr>";
				$i++;
			}
		echo"</table><br>";
	?>	
</form>	
	
</div>

</body>

</html>
	
<script>	
	function addmce(id,row){
		table="mce";
		var vin=id;
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				if(xmlhttp.responseText=="Success"){
					$("#q_tr_"+row).animate({
						opacity:0
					},500,function(){
						$("#q_tr_"+row).css("display","none");
					});
				}else{
					alert(xmlhttp.responseText);
				}
			}
		}						
	
		xmlhttp.open("GET","ajax/addmce.php?table="+table+"&vin="+vin,true);
		xmlhttp.send();
	}
			
	function deletemce(vin){	
		if(confirm("Are you sure")){
			xmlhttp.onreadystatechange=function(){
				if (xmlhttp.readyState==4 && xmlhttp.status==200){
					if(xmlhttp.responseText=="Success"){
						$("#div_"+vin).animate({
							opacity:0
						},500);
					}else{
						//alert(xmlhttp.responseText);
					}
					$("#div_"+vin).animate({
							opacity:0
						},500);
					}
				}						
			xmlhttp.open("GET","ajax/deletemce.php?vin="+vin,true);
			xmlhttp.send();
		}
	}
			
	function updateStatus(value,vin,t){	
		if(confirm("Are you sure")){
			xmlhttp.onreadystatechange=function(){
				if (xmlhttp.readyState==4 && xmlhttp.status==200){
					if(value!="Sure"){
						var rem=prompt("Remarks:");
						updateRemarks(vin,rem);
					}
				}
			}						
			xmlhttp.open("GET","ajax/updatestatus.php?vin="+vin+"&value="+value+"&target="+t,true);
			xmlhttp.send();
		}
	}

	function updateRemarks(vin,remarks){	
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				if(xmlhttp.responseText==""){
					jump("");
				}else
					alert(xmlhttp.responseText);
				}
			}						
		xmlhttp.open("GET","ajax/updateremarks.php?vin="+vin+"&remarks="+remarks,true);
		xmlhttp.send();
	}
			
	function deletehlc(hlcno,vin){	
		if(confirm("Are you Sure?")){
			xmlhttp.onreadystatechange=function(){
				if (xmlhttp.readyState==4 && xmlhttp.status==200){
					if(xmlhttp.responseText=="Success"){
						$("#div_"+hlcno).animate({
							opacity:0
						},500);
					}else{
						alert(xmlhttp.responseText);
					}
						$("#div_"+hlcno).animate({
							opacity:0
						},500);
					}
				}						
			xmlhttp.open("GET","ajax/deletehlmember.php?hlcno="+hlcno+"&vin="+vin,true);
			xmlhttp.send();
		}
	}
</script>