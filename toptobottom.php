<?php
	require("connect.php");
	require("head.php");
?>

<div style="box-shadow:2px 0 10px #000;background:#34c83e;position:fixed;z-index:100;width:100%;top:0;left:0;border-bottom:5px solid #2e4e8e">
	<table width=100% >
		<tr style="background:#375ba2;" class="no_style" >
		<form method=post enctype="multipart/form-data"  >
			<td colspan=14 >
				<br>
				<table>
					<tr style="background:#375ba2;color:white;">
					<td style="padding:0 0 0 15px;" ><input onfocus="this.value=''"  type=text name="t_search" id="t_search" value="<?php if($_POST["t_search"]!=""){echo $_POST["t_search"];}else{echo "Type a keyword";} ?>" size=30 /></td>
					<td><input type=submit name='b_search' value="Search" /></td>
					<td><input type=submit value="Refresh" onclick="getID('t_search').value=''" /></td>
					<td><input type=button value='&laquo; Home' onclick="jump('index.php')" /></td>
					<td style="padding:0 0 0 25px;font:bold 25px arial" >&raquo; FROM TOP TO BOTTOM RELATIONSHIP</td>
					</tr>
				</table>
				<br>
			</td>
		
		</tr>
	</table>
</div>
<br><br><br>

<style>
	#container td{
		padding:5px;
	}
</style>

<div style="padding:3px;" ></div>
<div style="width:1000px;margin:0 auto" >
		<table width=100% id="container" >
		<tr class="no_style" >
			<?php
				$p=$_GET['page'];
				if($p!=""){
					$to=$p*100;
					$from=$to-100;
				}
				else{
					$to=100;
					$from=0;
				}
				$ex11="select * from voters v, mce m  where 
						(v.vname like'%".$_POST["t_search"]."%' or
						v.remarks like'%".$_POST["t_search"]."%' or
						v.birth like'%".$_POST["t_search"]."%' or
						v.sex like'%".$_POST["t_search"]."%' or
						v.precinct like'%".$_POST["t_search"]."%' or
						v.address like'%".$_POST["t_search"]."%' or
						v.city_mun like'%".$_POST["t_search"]."%') and m.vin=v.vin
					order by vname";
				$ex22="select * from voters v, mce m where m.vin=v.vin order by vname";
				
				$toadd=1;
				$cc=1;
				for($xx=1;$xx<=4;$xx++){
					$top=$cc;
					echo"<td width=25% valign=top >";
						if(isset($_POST["b_search"])){
							$stmt = $link->prepare('?');
							$stmt->execute([$ex11]);
							$ex = $stmt;
						} else {
							$stmt = $link->prepare('?');
							$stmt->execute([$ex22]);
							$ex = $stmt;
						}
							
						$i=1;
						
						$value=strtoupper($_POST["t_search"]);
						$rep="<b style='color:#0014d0;background:#ffa0a0'>".$value."</b>";
						while($rs=$ex->fetch(PDO::FETCH_BOTH)){
							$s="background:#f5f190";
							if($i%2==0)
								$s="background:#ffb2b2";
							
							if($ctr%2==1){
								$s="background:#ffb2b2";
								if($i%2==0)
									$s="background:#f5f190";
							}
								
							if($i==$top || $xx==$ii){
								$stmt = $link->prepare('select count(*) from bce where mcevin=?');
								$stmt->execute([$rs["vin"]]);
								$ex1 = $stmt;
								$rsbce=$ex1->fetch(PDO::FETCH_BOTH);
								$bce=$rsbce[0];
								
								if(isset($_POST["b_remove_".$rs["0"]])){
									$stmt = $link->prepare('delete from mce where vin=?');
									$stmt->execute([$rs["0"]]);
									jump("mcelist.php");
								}
								if(isset($_POST["b_upImg_".$rs["0"]])){
									move_uploaded_file($_FILES["b_file_".$rs["0"]]["tmp_name"], "images/voters/".$rs[0].".jpg");
									jump("");
								}
								
								
								echo "
								<div style='".$s.";position:relative;padding:5px;border:1px solid #aaa;'	id='div_".$rs["0"]."' >
									<div style='position:absolute;left:0;top:0;z-index:2' ><div style=\"padding:5px;color:#fff;background:#4cd656;font:bold 12px arial;border-bottom-right-radius:10px;border-top-left-radius:5pxborder-bottom-right-radius:10px;\">".$i.".</div></div>";
									echo"<div style='position:relative;' >";
									if(file_exists("images/voters/".$rs["0"].".jpg")){
										echo"<img src='images/voters/".$rs["0"].".jpg' width=100% />";
									}
									else
										echo"<img src='images/blank.jpg' />";
									
									echo"
										</div>";
									echo"
									<div style='padding:10px'></div>
									[MCE] <b>".str_replace($value,$rep,$rs["vname"])."</b><br>
									<span style='color:#002d94'>VIN: <b>".str_replace($value,$rep,$rs["vin"])."</b></span><br><span style='color:#940000'>";
									if($rs["sex"]==="M")
										echo"MALE";
									else
										echo"FEMALE";
								
								$birthDate = $rs["birth"];;
								$birthDate = $birthDate;
								$age = "-";
								if (!empty($birthDate) && $birthDate !== '0000-00-00') {
									$birthObj = date_create($birthDate);
									if ($birthObj !== false) {
										$age = date_diff($birthObj, date_create('today'))->y;
									}
								}
												
								$stmt = $link->prepare('select * from bce b, voters v where mcevin=? and b.vin=v.vin');
								$stmt->execute([$rs["vin"]]);
								$exbce = $stmt;
								
								echo"			
									</span>| <span style='color:#00778c'>".str_replace($value,$rep,$rs["birth"])."</span>
									| <span style='color:#535353'>".$age." yrs. old</span><br>
									<span style='color:#943800'>".str_replace($value,$rep,$rs["address"])."</span><br>
									<span style='color:#00940e'>Total BCE Members: <b>".$bce."</b></span>
									<br><br>
									<div>";
										while($rsbce=$exbce->fetch(PDO::FETCH_BOTH)){
											echo "<div style='padding:1px 0 1px 0;border-top:1px solid #bbb;background:#fff'></div>
												<div>
												<table width=100%>";
													if(file_exists("images/voters/".$rsbce["2"].".jpg"))
														echo"<td><img width=30 src='images/voters/".$rsbce["2"].".jpg' />";
													else
														echo"<td><img width=30 src='images/blank.jpg' />";
													
													echo"
													</td><td><b>".$rsbce["vname"]."</b></td>
												</table>
											";
											echo "</div>
											";
											
										}
									echo"</div>
								</div>";
								
								echo "<div style='padding:5px;'></div>";
								$top+=4;
								$toadd++;
							}
							
							$i++;
						}
					echo"</td>";
					$cc++;
				}
				
			?>
		</tr>
	</table>
</form>
<br><br>
</div>

</body>

</html>