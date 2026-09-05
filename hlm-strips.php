<?php
	require("connect.php");
	require("head.php");
	
	$value=strtoupper($_POST["t_search"]);
	$rep="<b style='color:#0014d0;background:#ffa0a0'>$value</b>";
	
	$bar="BARANGAY ".$_GET["barangay"];
	if($bar=="BARANGAY ")
		$bar="ALL BARANGAYS";
?>

		<script>
				var table=0;
				var hlvotno=0;	
		</script>
<body>

<div id="t_controls" class="controls">

<?php require("menu.php"); ?>	
	
<script>setActive("sum");</script>
<script>setActive("sumpt");</script>

		<table style="margin:0 auto" >
			<tr style="background:#375ba2;" class="no_style" >
			<form method=post enctype="multipart/form-data"  >
				<td colspan=14 >
					<br>
					<table>
						<tr style="background:#375ba2;color:white;">
						<td style="padding:0 0 0 15px;" ><input onfocus="this.value=''"  type=text name="t_search" value="<?php if($_POST["t_search"]!=""){echo $_POST["t_search"];}else{echo "Type a keyword";} ?>" size=30 /></td>
						<td><input type=submit name='b_search' value="Search" /></td>
						<td><input type=button value='Print' onclick="printF()" /></td>
						<td>&nbsp;&nbsp;</td>
						<td align=right>
							<select style="padding:5px;" onchange="jump('?barangay='+this.value)" >
								<option>All barangays</option>
								<?php
									$ex=$link->query("select barangay from voters group by barangay order by barangay")or die(mysqli_error($link));
									while($rs=mysqli_fetch_array($ex)){
										echo "<option ";
											if($_GET["barangay"]===$rs[0])
												echo "selected";
										echo" >$rs[0]</option>";
									}
								?>
							</select>
						</td>
						<td>&nbsp;&nbsp;</td>
						<script>
							function printF(){
								$('#header').css("display","block");
								$('#spacer').css("display","none");
								$('.nav').css("display","none");
								getID('t_controls').style.visibility='hidden'; 
								//getID('thumbnails').style.display='none'; 
								//getID('grid').style.display='block'; 
								window.print(); 
								//getID('grid').style.display='none'; 
								//getID('thumbnails').style.display='block'; 
								getID('t_controls').style.visibility='visible'; 
								$('#header').css("display","none");
								$('#spacer').css("display","block");
								$('.nav').css("display","block");
							}
						</script>
						</tr>
					</table>
					<br>
				</td>
			
			</tr>
		</table>
</div>
<style>
	td{
		border:0;
	}
	#info td{
		font-size:20px;
	}
	.info td{
		border:2px solid #000
	}
</style>
<div style="padding:0px;" ></div>
<div style="width:1000px;margin:0 auto;position:relative;" >
	<div id="thumbnails" >
		<div class='nav' ><br><br><br><br><br></div>
		<?php
			$rec=500;
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
				
			$hl="";
			if($_GET["hl"]!="")
				$hl=" and v.vin='".$_GET["hl"]."' ";

			$pl="";
			if($_GET["pl"]!="")
				$pl=" and v.vin='".$_GET["pl"]."' ";

			$bce="";
			if($_GET["bce"]!="")
				$bce=" and v.vin='".$_GET["bce"]."' ";

			$mce="";
			if($_GET["mce"]!="")
				$mce=" and v.vin='".$_GET["mce"]."' ";
				
			$filter="";
				if($_GET["barangay"]!="All barangays" && $_GET["barangay"]!="" )
					$filter=" and v.barangay='".$_GET["barangay"]."'";
					
			if(isset($_POST["b_search"])){
				$ex=$link->query("select * from voters v, hl h where 
					(v.vname like'%".$_POST["t_search"]."%' or
					v.remarks like'%".$_POST["t_search"]."%' or
					v.birth like'%".$_POST["t_search"]."%' or
					v.sex like'%".$_POST["t_search"]."%' or
					v.precinct like'%".$_POST["t_search"]."%' or
					v.address like'%".$_POST["t_search"]."%' or
					v.city_mun like'%".$_POST["t_search"]."%') and h.vin=v.vin $hl $pl $bce $mce $filter order by v.vname limit $from,$to ")or die(mysqli_error($link));
			
				$ex2=$link->query("select * from voters v, hl h where
					(v.vname like'%".$_POST["t_search"]."%' or
					v.remarks like'%".$_POST["t_search"]."%' or
					v.birth like'%".$_POST["t_search"]."%' or
					v.sex like'%".$_POST["t_search"]."%' or
					v.precinct like'%".$_POST["t_search"]."%' or
					v.address like'%".$_POST["t_search"]."%' or
					v.city_mun like'%".$_POST["t_search"]."%') and h.vin=v.vin $hl $pl $bce $mce $filter order by v.vname")or die(mysqli_error($link));

				$ex3=$link->query("select * from voters v, pl p where
					(v.vname like'%".$_POST["t_search"]."%' or
					v.remarks like'%".$_POST["t_search"]."%' or
					v.birth like'%".$_POST["t_search"]."%' or
					v.sex like'%".$_POST["t_search"]."%' or
					v.precinct like'%".$_POST["t_search"]."%' or
					v.address like'%".$_POST["t_search"]."%' or
					v.city_mun like'%".$_POST["t_search"]."%') and p.vin=v.vin $hl $pl $bce $mce $filter")or die(mysqli_error($link));

				$ex4=$link->query("select * from voters v, bce b where
					(v.vname like'%".$_POST["t_search"]."%' or
					v.remarks like'%".$_POST["t_search"]."%' or
					v.birth like'%".$_POST["t_search"]."%' or
					v.sex like'%".$_POST["t_search"]."%' or
					v.precinct like'%".$_POST["t_search"]."%' or
					v.address like'%".$_POST["t_search"]."%' or
					v.city_mun like'%".$_POST["t_search"]."%') and b.vin=v.vin $hl $pl $bce $mce $filter")or die(mysqli_error($link));

			} else {
				$ex=$link->query("select * from voters v, hl h where h.vin=v.vin $hl $pl $bce $mce $filter order by v.vname limit $from,$to ")or die(mysqli_error($link));
				$ex2=$link->query("select * from voters v, hl h where h.vin=v.vin $hl $pl $bce $mce $filter order by v.vname")or die(mysqli_error($link));
				$ex3=$link->query("select * from voters v, pl p where p.vin=v.vin $hl $pl $bce $mce $filter limit $from,$to")or die(mysqli_error($link));
				$ex4=$link->query("select * from voters v, bce b where b.vin=v.vin $hl $pl $bce $mce $filter limit $from,$to")or die(mysqli_error($link));
			}
			
			while($rs=mysqli_fetch_array($ex)){
				$ex1=$link->query("select count(*) from bce where mcevin='".$rs["vin"]."'")or die(mysqli_error($link));
				$rsbce=mysqli_fetch_array($ex1);
				$bce=$rsbce[0];
				
				$s="background:#fff";

				$eee=$link->query("select * from voters v where v.vin='".$rs["plvin"]."'")or die(mysqli_error($link));
				$rsmce=mysqli_fetch_array($eee);
				
				echo"<br><div style='$s;width:100%;height:100%' id='div_$rs[0]'><div style='padding:5px' >";
				
					$exx=$link->query("select count(*) from hl_children hc where hc.hlvin='".$rs[0]."' ")or die(mysqli_error($link));
					$rsbce=mysqli_fetch_array($exx);
					$hl=$rsbce[0];
					
					$cluster=$link->query("select cluster from clusters where precinct='".$rs["precinct"]."'")or die(mysqli_error($link));
					$rsc=mysqli_fetch_array($cluster);
					$hl_cluster=$rsc[0];
					
					ECHO "
					<div>
						<table width=100% id='info' class='info' >
							<TR style='font-weight:bold' class='no_style'>
								<td width='50px' align='center'>HL</td>";
								echo"<td width='80px' align='center'>";
									if(file_exists("images/voters/$rs[0].jpg")){
										echo"<img src='images/voters/$rs[0].jpg' height='80' style='padding:0;border-radius:3px' />";
									}else{
										echo"<img src='images/blank.jpg' height='80' style='border-radius:3px' />";
									}
								echo"</td>";
								echo"								
								<td style='width:500px;padding-left:5px;'>".$rs["vname"]."</td>
								<td width='120px' align='center'>PR-".$rs["precinct"]."</td>
								<td width='120px' align='center'>SQ-".$rs["seq"]."</td>
								<td width='120px' align='center'>CL-".$rsc[0]."</td>
							</TR>";
							
						$exhlc=$link->query("select * from hl_children hl, voters v where hl.hlvin='".$rs["vin"]."' and hl.vin=v.vin ")or die(mysqli_error($link));
							
						$ii=1;
						while($rshlc=mysqli_fetch_array($exhlc)){

						$cluster=$link->query("select cluster from clusters where precinct='".$rshlc["precinct"]."'")or die(mysqli_error($link));
						$rsc=mysqli_fetch_array($cluster);

						while($rs=mysqli_fetch_array($ex3)){							
							$bbb=$link->query("select * from voters v where v.vin='".$rs["bcevin"]."'")or die(mysqli_error($link));
							$rsbec=mysqli_fetch_array($bbb);
						}
									
						while($rs=mysqli_fetch_array($ex4)){
							$mmm=$link->query("select * from voters v where v.vin='".$rs["mcevin"]."'")or die(mysqli_error($link));
							$rsmec=mysqli_fetch_array($mmm);
						}
								
								$color="color:#000";
								if($rshlc["ato"]==="Undecided")
									$color="color:#e20000";
								else if($rshlc["ato"]==="NPC")
									$color="color:#000099";
								else if($rshlc["ato"]==="Dili")
									$color="color:#000";
								else if($rshlc["ato"]==="Sure Wala Diri")
									$color="color:#2ceb00";
								else if($rshlc["cluster"]==="clusters")
									$color="color:#2ceb00";
								
								$clcolor="#000";
								if($rsc[0]!=$hl_cluster)
									$clcolor="red";
									
								echo "
								<tr style='font-weight:bold;padding:2px' class='no_style'>
									<td align='center'>$ii.</td>";
									
								echo"<td align='center'>";
				
								if(file_exists("images/voters/$rshlc[2].jpg")){
									echo"<img src='images/voters/$rshlc[2].jpg' height='80' style='border-radius:3px' />";
								}
								else
									echo"<img src='images/blank.jpg' height='80' style='border-radius:3px' />";
								echo"</td>";
								echo"								
									
									<td style='width:500px;padding-left:5px'>".str_replace($val,$rep,$rshlc["vname"])."</td>
									<td width='120px' align='center'>PR-".$rshlc["precinct"]."</td>
									<td width='120px' align='center'>SQ-".$rshlc["seq"]."</td>
									<td width='120px' align='center'><b style='color:$clcolor'>CL-".$rsc[0]."</b></td>
								</tr>";			
	
								$ii++;
							}
					echo"</table>
				
						</div>
					
					</div>";
				$i++;
			}
		?>
	</form>
	</div><br>
</div>
</body>
</html>
	
	<script>
		
			function addmce(id,row){
				table="mce";
				var vin=id;

				xmlhttp.onreadystatechange=function()
				{
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
			
			function deletehl(vin){	
				alert(vin);
				if(confirm("Are you sure")){
					xmlhttp.onreadystatechange=function()
					{
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
					xmlhttp.open("GET","ajax/deletehl.php?vin="+vin,true);
					xmlhttp.send();
				}
			}

	</script>
