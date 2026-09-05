<?php
	require("connect.php");
	require("head2.php");
?>

<link href="css/hlindiv.css" rel="stylesheet" type="text/css"/>

<body>

<div id="t_controls" style="box-shadow:2px 0 10px #000;border-bottom:5px solid #801919;background:#b61212;position:fixed;z-index:100;width:100%;top:38;left:0">

<?php require("menu.php"); ?>	

<script>
	setActive("sum");
	setActive("sumform");

	function printF(){
		$('#header').css("display","block");
		$('#spacer').css("display","none");
		$('.nav').css("display","none");
		getID('t_controls').style.visibility='hidden'; 

		window.print(); 
		getID('t_controls').style.visibility='visible'; 
		$('#header').css("display","none");
		$('#spacer').css("display","block");
		$('.nav').css("display","block");
	}
</script>
	
	<table style="margin:0 auto">
		<tr style="background:#b61212;" class="no_style">
		<form method=post enctype="multipart/form-data">
			<td colspan=14><br>
				<table>
					<tr style="background:#b61212;color:white;">
					<td style="padding:0 0 0 15px;" ><input onfocus="this.value=''"  type=text name="t_search" value="<?php if($_POST["t_search"]!=""){echo $_POST["t_search"];}else{echo "Search for HL to Print";} ?>" size=30 /></td>
					<td align=right style="padding:0 0 0 5px;font:bold 15px arial"><input type=submit name='b_search' value="Search"/></td>
					<td align=right style="padding:0 0 0 5px;font:bold 15px arial"><input type=button value='Print' onclick="printF()"/></td>
					<td align=right style="padding:0 0 0 5px;font:bold 15px arial"><a href="hl-indiv.php"><input type=button value='Individual HL Form'/></a></td>
					<td align=right style="padding:0 0 0 5px;font:bold 15px arial">
						<select style="padding:5px;" onchange="jump('?barangay='+this.value)">
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
					</tr>
				</table>
				<br>
			</td>
		</tr>
	</table>
</div>

<div style="padding:3px;"></div>
<div style="width:1000px;margin:0 auto;position:relative; background:#FFF" >
	<div id="thumbnails">
		<div class='nav'><br><br><br><br><br><br></div>
		
		<?php
			$rec=500;
			$p=$_GET['page'];
			if($p>1){
				$to=$rec;
				$from=($p*$rec)-$rec;
				$i=(($p-1)*$rec)+1;
			}else{
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

			$filter="";
				if($_GET["barangay"]!="All barangays" && $_GET["barangay"]!="" )
					$filter=" and v.barangay='".$_GET["barangay"]."'";
					
			if(isset($_POST["b_search"])){
				$ex0=$link->query("select * from voters v, hl h where 
				   (v.vname like'%".$_POST["t_search"]."%' or
					v.remarks like'%".$_POST["t_search"]."%' or
					v.birth like'%".$_POST["t_search"]."%' or
					v.sex like'%".$_POST["t_search"]."%' or
					v.precinct like'%".$_POST["t_search"]."%' or
					v.address like'%".$_POST["t_search"]."%' or
					v.city_mun like'%".$_POST["t_search"]."%') and h.vin=v.vin $hl $pl $filter order by v.vname")or die(mysqli_error($link));

				$ex1=$link->query("select * from voters v, hl h where 
				   (v.vname like'%".$_POST["t_search"]."%' or
					v.remarks like'%".$_POST["t_search"]."%' or
					v.birth like'%".$_POST["t_search"]."%' or
					v.sex like'%".$_POST["t_search"]."%' or
					v.precinct like'%".$_POST["t_search"]."%' or
					v.address like'%".$_POST["t_search"]."%' or
					v.city_mun like'%".$_POST["t_search"]."%') and h.vin=v.vin $hl $pl $filter order by v.vname limit $from,$to")or die(mysqli_error($link));

				$ex2=$link->query("select * from voters v, pl p where 
				   (v.vname like'%".$_POST["t_search"]."%' or
					v.remarks like'%".$_POST["t_search"]."%' or
					v.birth like'%".$_POST["t_search"]."%' or
					v.sex like'%".$_POST["t_search"]."%' or
					v.precinct like'%".$_POST["t_search"]."%' or
					v.address like'%".$_POST["t_search"]."%' or
					v.city_mun like'%".$_POST["t_search"]."%') and p.vin=v.vin $hl $pl $filter order by v.vname limit $from,$to")or die(mysqli_error($link));
			}else{
				$ex0=$link->query("select * from voters v, hl  h where h.vin=v.vin $hl $pl $filter order by v.vname")or die(mysqli_error($link));
				$ex1=$link->query("select * from voters v, hl  h where h.vin=v.vin $hl $pl $filter order by v.vname limit $from,$to")or die(mysqli_error($link));
				$ex2=$link->query("select * from voters v, pl  p where p.vin=v.vin $hl $pl $filter order by v.vname limit $from,$to")or die(mysqli_error($link));
			}
			
			while($rs=mysqli_fetch_array($ex1)){
			
			$precinct=$rs["precinct"];
			$name=$rs["vname"];
			$sex=$rs["sex"];
			
			$ppp=$link->query("select * from voters v where v.vin='".$rs["plvin"]."'")or die(mysqli_error($link));
			$rspl=mysqli_fetch_array($ppp);

			$rsb=mysqli_fetch_array($ex2);					
			$bbb=$link->query("select * from voters v where v.vin='".$rsb["bcevin"]."'")or die(mysqli_error($link));
			$rsbce=mysqli_fetch_array($bbb);
			
			echo"<br><br>
				<div style='width:100%;height:100%' id='div_$rs[0]'><div style='padding:5px' >
					<table width=100% align='center'>
						<tr style='border:0'>
							<td width='50%' align='left' >
								<img src='images/alayon-big.png' style='height:70px'/>
							</td>
							<td width='50%' align='right' style='border:0' >
								<img src='images/iloveyudark.png' style='height:70px;'/>
							</td>
						</tr>
					</table>";
					
					echo"
					<table align='center' width='100%'>				
						<td align='center' width='100%'>
							<B style='font-size:25px'>ACKNOWLEDGEMENT RECEIPT</b><br>
							<b style='font-size:20px'>BARANGAY ".$rs["barangay"]."</b><br>
							<b style='font-size:15px'>CITY OF ".$_SESSION["city_mun"]."</b>
						</td>
					</table>
					<table align='center' width='100%'>
						<td width='15%' align='left'><div class='orform1'>HL ID: "; $cont = $rs["vin"]; printf("%04d", $cont); echo"</div></td>	
						<td width='70%' align='left'> &nbsp; </td>							
						<td width='15%' align='right'><div class='orform2'>First Release</div></td>						
					</table>";
					
					$birthDate = $rs["birth"];
					$birthDate = explode("-", $birthDate);
					$age = (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[2], $birthDate[0]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));

					$cluster=$link->query("SELECT cluster FROM clusters WHERE precinct LIKE '%$precinct%'") or die(mysqli_error($link));
					$rsc=mysqli_fetch_array($cluster);
					$hl_cluster=$rsc[0];
					
					ECHO"
					<div style='height:740px'>
						<table width=100% id='info' class='info' >
							<TR style='background:#bbb;font-weight:bold' class='no_style'>
								<td style='padding:15px;text-align:center'>#</td>
								<td width='60px' style='text-align:center'>PHOTO</td>
								<td style='text-align:left;padding-left:10px'>NAME OF VOTERS</td>
								<td style='text-align:center'>SEX</td>
								<td style='text-align:center'>AGE</td>
								<td style='text-align:center'>PREC</td>
								<td style='text-align:center'>CLUS</td>
								<td style='text-align:center;width:200px'>SIGNATURE</td>
							</TR>
							
							<TR style='font-weight:bold' class='no_style'>
								<td align='center'>HL</td>";
								echo"<td align='center'>";
									if(file_exists("images/voters/$rs[0].jpg")){
										echo"<img src='images/voters/$rs[0].jpg' height='55' width='55' style='padding:0;border-radius:3px' />";
									}else{
										echo"<img src='images/blank.jpg' height='55' width='55' style='border-radius:3px' />";
									}
								echo"</td>";
								
								echo"								
								<td style='padding-left:5px;'>$name</td>
								<td align='center'>$sex</td>
								<td align='center'>$age</td>
								<td align='center'>$precinct</td>								
								<td align='center'>$hl_cluster</td>								
								<td align='center'></td>
							</TR>";
						
							$ii=1;

							$exhlc=$link->query("select * from hl_children hl, voters v where hl.hlvin='".$rs["vin"]."' and hl.vin=v.vin")or die(mysqli_error($link));
														
							while($rshlc=mysqli_fetch_array($exhlc)){
							
							$hmname=$rshlc["vname"];
							$hmsex=$rshlc["sex"];
							$hmprec=$rshlc["precinct"];
							
							$cluster2=$link->query("select cluster from clusters where precinct LIKE '%$hmprec%'") or die(mysqli_error($link));
							$rsc2=mysqli_fetch_array($cluster2);
							$hm_cluster=$rsc2[0];
									
							$birthDate = $rshlc["birth"];
							$birthDate = explode("-", $birthDate);
							$age = (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[2], $birthDate[0]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));
								
							$color="color:#000";
							if($hl_cluster!=$hm_cluster) 
							$color="color:red";

							echo "
							<tr style='font-weight:bold;padding:2px' class='no_style'>
								<td align='center'>$ii.</td>";
								
							echo"<td align='center'>";
			
							if(file_exists("images/voters/$rshlc[2].jpg")){
								echo"<img src='images/voters/$rshlc[2].jpg' height='55' style='border-radius:3px' />";
							}
							else
								echo"<img src='images/blank.jpg' height='55' style='border-radius:3px' />";
							echo"
							</td>
								<td style='padding-left:5px;'>$hmname</td>
								<td align='center'>$hmsex</td>
								<td align='center'>$age</td>				
								<td align='center'>$hmprec</td>
								<td align='center' style='$color'>$hm_cluster</td>
								<td align='center'></td>
							</tr>";			

							$ii++;
						}
					echo"</table>
					</div><br><br>
						<table width='100%'>
							<td width=33% align='center' style='font-size:14px'>
								<b>".str_replace($val,$rep,$rspl["vname"])."</b>
								<div style='border-bottom:2px solid #000'></div>Precinct Leader
							</td>
							<td width=34% align=center style='font-size:14px' >
								<b>".str_replace($val,$rep,$rsbce["vname"])."</b>
								<div style='border-bottom:2px solid #000'></div>Barangay Kagawad
							</td>
							<td width=33% align='center' style='font-size:14px'>
								<b>WILLIAM ABASOLO LARUBIS, SR.</b>
								<div style='border-bottom:2px solid #000'></div>Barangay Chairman
							</td>
						</table>
							
						<br/><br/>
					
						<table width='100%'>
							<td width=33% align='center' style='font-size:14px'>
								<div style='margin-top:-100px;position:relative;top:40px'><img src='images/no_signature.png' height='100px'/></div>							
								<b>AILYN O. SULAD</b>
								<div style='border-bottom:2px solid #000' ></div>Assistant Supervising Facilitator
							</td>
							<td width=34% align='center' style='height:100px;border:1px dotted #000'>P A I D &nbsp; S T A M P</td>
							<td width=33% align=center style='font-size:14px'>
								<div style='margin-top:-100px;position:relative;top:50px'><img src='images/no_signature.png' height='110px'/></div>
								<b></b>
								<div style='border-bottom:2px solid #000' ></div>Cashier
							</td>							
						</table>
					<br/><br/><br/>
				</div>";
			}
		?>
	</form>
<!--
	<br><br>
	<div style="position:fixed; left:0;width:100%;bottom:0;height:60px;background:#b61212;z-index:1000" class="nav" >
		<div style="padding:1px;text-align:center;" >
			<table style="margin:0 auto;">
				<tr style="background:transparent" >
				<td><input <?php if($_GET["page"]<=1)echo" disabled "; ?> type=image value="Previous" src="images/prev.png" onclick="jump('?page=<?php echo ($_GET["page"]-1)."&barangay=".$_GET["barangay"]; ?>&ato=<?php echo $_GET["ato"]; ?>')" /></td>
				<td>
					<select style='padding:1px;text-align:center' id='s_pn' onchange="jump('?page='+this.value+'<?php echo "&barangay=".$_GET["barangay"]; ?>&ato=<?php echo $_GET["ato"]; ?>')" >
					<option>Page</option>
					<?php
						for($j=1;$j<=mysqli_num_rows($ex0)/$rec+1;$j++){
							echo "<option ";
						if($_GET["page"]==$j)
							echo "selected";						
						ECHO" >$j</option>";
						}
					?>
					</select>
				</td>
				<td><input <?php if($_GET["page"]>=mysqli_num_rows($ex0))echo" disabled "; ?>  type=image value="Next" src="images/next.png" onclick="jump('?page=<?php echo ($_GET["page"]+1)."&barangay=".$_GET["barangay"]; ?>&ato=<?php echo $_GET["ato"]; ?>')" /></td>
				</tr>
			</table>
		</div>
	</div>	
	<br><br>
-->
</div>

</div>

</body>

</html>
