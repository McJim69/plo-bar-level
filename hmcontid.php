<?php
	require("connect.php");
	require("head.php");
	
	$mun=strtoupper($_GET["municipality"]);
	
?>

<style>	
	.border td,.border th{
		border:1px dotted #aaa;
		padding:5px;font-size:15px;
		text-align:center;
		font-family:arial;border:1px dotted #999;
	}
</style>

<body>

<div style="width:100%;margin:0 auto;TEXT-align:center;background:#fff;" >
	<div id="d_controls" style="padding:5px;width:1000px;margin:0 auto;">
		<?php require("menu.php"); ?>
		<script>
			setActive("hmcont");
		</script>
		<div style="font-size:30px;" >
		<?php
			echo"LA CONTROL NUMBERS<BR><b>MUNICIPALITY OF ".$_GET["municipality"]."</b>";
		?>
		</div>
		<div style="padding:10px;" id="hide" >
			<select style="padding:5px 20PX 5px 20px;" onchange="if(this.value=='All municipality')jump('hmcontid.php'); else jump('hmcontid.php?municipality='+this.value)" >
				<?php
					$ex2=$link->query("select city_mun from voters group by city_mun order by city_mun")or die(mysqli_error($link));
					while($rs2=mysqli_fetch_array($ex2)){
						echo "<option ";
					if($_GET["municipality"]===$rs2[0])
						echo " selected ";
						echo" >$rs2[0]</option>";
					}
				?>
			</select>
			<input type=button value='Print' onclick="printF()" />
			<script>
				function printF(){
					getID('menu_').style.display='none'; 
					getID('hide').style.display='none'; 
					window.print(); 
					getID('menu_').style.display='block'; 
					getID('hide').style.display='block'; 
				}
				setActive("sum");
			</script>
		</div>
	<table class="border" width=100% >
		<?php
			$ex=Q("select barangay from voters where city_mun='".$mun."' group by barangay order by barangay")or d();
			$i=1;
			$cont=1;
			while($rs=fetch($ex)){
				$c="even";
				if($mun!=""){
					if($mun=="KUMALARANG")
						$abb="KUM";
					elseif($mun=="LAKEWOOD")
						$abb="LW";
					elseif($mun=="TIGBAO")
						$abb="TIG";
					elseif($mun=="DUMALINAO")
						$abb="DUM";
					elseif($mun=="GUIPOS")
						$abb="GUI";
					elseif($mun=="SAN PABLO")
						$abb="SP";
					elseif($mun=="SAN MIGUEL")
						$abb="SM";
					elseif($mun=="LAPUYAN")
						$abb="LAP";
					elseif($mun=="MARGOSSATUBIG")
						$abb="MARGOS";
					elseif($mun=="VINCENZO SAGUN")
						$abb="VS";
					elseif($mun=="DINAS")
						$abb="DIN";
					elseif($mun=="DIMATALING")
						$abb="DIM";
					elseif($mun=="PITOGO")
						$abb="PIT";
					elseif($mun=="TABINA")
						$abb="TAB";
					elseif($mun=="BAYOG")
						$abb="BAY";
				}
				echo "<tr class=$c ondblclick=\"$('#hl_$i').animate({height:'toggle'});\" ><td style='text-align:left;font-size:25px;background:#fff;border:0;' >$i. BARANGAY <b>$rs[0]</td></tr>";
				echo "<tr id='hl_$i' ><td style='text-align:left;border:0;padding:0' >";
					$ex1=Q("select * from hl h, voters v where h.vin=v.vin and v.city_mun='".$mun."' and v.barangay='$rs[0]' order by v.vname")or d();
						$h=1;
						while($rs1=fetch($ex1)){
							if($cont<10)
								$hlcont="000".$cont;
							elseif($cont<100)
								$hlcont="00".$cont;
							elseif($cont<1000)
								$hlcont="0".$cont;
							elseif($cont<10000)
								$hlcont=$cont;
								
							echo "
							<table  width=100%  >
							<tr style='text-align:left;'><td  colspan=2 ><b>HOUSEHOLD LEADER</td><TD><b>PRECINCT NO.</td><TD><b>SEQ. NO.</td><TD><b>LA CONTROL NO.</td><td>SIGNATURE</td></tr>
						
							<tr style='background:#fff' >
								<Td width=1><b>$h.</td>
								<td style='text-align:left' ><b>".$rs1["vname"]."</td>
								<td style='text-align:left;width:150px' ><b>".$rs1["precinct"]."</td>
								<td style='text-align:left;width:100px' ><b>".$rs1["seq"]."</td>
								<td style='text-align:left;width:150px' ><b>$abb $hlcont</td>
								<td style='text-align:left;width:205px' ></td>
							</tr>";
							
							echo"<tr style='background:#aaa;' >
							<td colspan=6 >
								<table width=100% >
									<tr style='text-align:left;'><td colspan=2 ><b>MEMBERS</td><TD><b>PRECINCT NO.</td><TD><b>SEQ. NO.</td><TD><b>LA CONTROL NO.</td><td>SIGNATURE</td></tr>";
								$ex2=Q("select * from hl_children hl,voters v where hl.vin=v.vin and hl.hlvin='".$rs1["vin"]."' order by v.vname")or d();
								$ii=1;
								$hm=explode("-","-A-B-C-D-E-F-G-H-I-J-K-L-M");
								while($rs2=fetch($ex2)){
									echo "<tr>
										<td style='text-align:left;' ><b>$h.$ii.</b></td><td style='text-align:left;'>".$rs2["vname"]."</td>
										<td style='text-align:left;width:150px' >".$rs2["precinct"]."</td>
										<td style='text-align:left;width:100px' >".$rs2["seq"]."</td>
										<td style='text-align:left;width:150px' ><b>$abb $hlcont".$hm[$ii]."</td>
										<td style='text-align:left;width:200px' ></td>
									</tr>";
									$ii++;
								}
								echo"
								</table>
							</td>
						</tr><tr style='background:#fff;border:0;' ><td colspan=5 style='background:#fff;border:0;'></td></tr>";
							$h++;
							$cont++;
						}
					echo"</table>
				</td></tr>";
				
				echo "<script>
					_$('#hl_$i').animate({
						height:'toggle'
					});
				</script>";
				$i++;
			}
		?>
	</table>
</div>

</div>

</body>

</html>