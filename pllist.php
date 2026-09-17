<?php
	require("connect.php");
	require("head.php");
	
	$bar="BARANGAY ".$_GET["barangay"];
	if($bar=="BARANGAY ")
		$bar="ALL BARANGAYS";
?>

<?php require("menu.php"); ?>	

<script> setActive("pl"); </script>
	
<div class="container my-4">
	<!-- Search & Controls Card -->
	<form method="post" enctype="multipart/form-data" class="mb-4" id="searchForm">
		<div class="card-glass p-3">
			<div class="row g-3 align-items-center">
				<div class="col-md-4">
					<div class="input-group">
						<span class="input-group-text border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
						<input type="text" name="t_search" id="t_search" class="form-control border-start-0 ps-2" 
							placeholder="Type a keyword..." value="<?php echo isset($_POST["t_search"]) ? htmlspecialchars($_POST["t_search"]) : ''; ?>" />
					</div>
				</div>
				<div class="col-md-5 d-flex gap-2">
					<button type="submit" name="b_search" class="btn btn-danger flex-fill"><i class="fa-solid fa-search"></i> Search</button>
					<button type="submit" class="btn btn-secondary" onclick="document.getElementById('t_search').value=''"><i class="fa-solid fa-rotate"></i></button>
					<button type="button" class="btn btn-success flex-fill" onclick="getID('div_pl').style.display='block';"><i class="fa-solid fa-plus"></i> Add PL</button>
					<button type="button" class="btn btn-dark" onclick="printF()"><i class="fa-solid fa-print"></i> Print</button>
				</div>
				<div class="col-md-3">
					<select class="form-select" onchange="jump('?barangay='+encodeURIComponent(this.value))">
						<option value="All barangays">All barangays</option>
						<?php
							$stmt = $link->prepare('select barangay from voters where city_mun=? group by barangay order by barangay');
							$stmt->execute([$_SESSION["city_mun"]]);
							$ex = $stmt;
							while($rs=$ex->fetch(PDO::FETCH_BOTH)){
								$sel = (isset($_GET["barangay"]) && $_GET["barangay"] === $rs[0]) ? "selected" : "";
								echo "<option value='".htmlspecialchars($rs[0])."' $sel>".htmlspecialchars($rs[0])."</option>";
							}
						?>
					</select>
				</div>
			</div>
		</div>
	</form>
</div>	

<!-- Select BCG MODAL -->
<div class="container-fluid" id="div_pl" style="margin-top:-185px;display:none;position:absolute;left:0;z-index:2;width:100%;height:100%;background:url('images/blank_bg.png')no-repeat;background-fill:cover;background-size:100%">
	<div class="mx-auto border border-danger p-3" style="background:#000;position:relative;max-width:990px; height:600px; top:100px; overflow-y:auto; overflow-x:hidden;">
		<!-- Sticky Header -->
		<div style="
		  position:sticky;
		  top:-20px;
		  left:-20px;
		  right:-20px;
		  margin:-20px -20px -7px -20px;
		  z-index:100;
		  background:#d00;
		  color:#fff;
		  padding:10px;
		  font-size:20px;
		  border-bottom:5px solid #aa0000;
		  display:flex;
		  align-items:center;
		  justify-content:space-between;
		">
		  <b> &nbsp; SELECT KAGAWAD TO BE ASSIGNED</b>

		  <input type="button" value="Close" 
				 onclick="getID('div_pl').style.display='none';" 
				 style="font-weight:bold;padding:5px;border:none;cursor:pointer;" />
		</div>
		<div class="row mt-4">
		  <?php
			$stmt = $link->prepare('select * from voters v, bce b where b.vin=v.vin and v.city_mun=? order by vname');
			$stmt->execute([$_SESSION["city_mun"]]);
			$ex = $stmt;

			$i=1;
			$value=strtoupper($_POST["t_search"]);
			$rep="<b class='text-primary bg-warning'>".$value."</b>";

			while($rs=$ex->fetch(PDO::FETCH_BOTH)){
			  $birthDate = $rs["birth"];
			  $age = "-";
			  if (!empty($birthDate) && $birthDate !== '0000-00-00') {
				$birthObj = date_create($birthDate);
				if ($birthObj !== false) {
				  $age = date_diff($birthObj, date_create('today'))->y;
				}
			  }

			  $stmt = $link->prepare('select count(*) from pl where bcevin=?');
			  $stmt->execute([$rs["vin"]]);
			  $rspl=$stmt->fetch(PDO::FETCH_BOTH);
			  $pl=$rspl[0];

			  //$bgClass = ($i % 2 == 0) ? "bg-warning" : "bg-light";

			  echo "
			  <div class='col-md-3 mb-4'>
				<div class='card h-100 shadow-sm id='div_".$i."' 
					 onmouseout=\"getID('div_controls_".$rs["0"]."').style.visibility='hidden';getID('div_browse_".$rs["0"]."').style.visibility='hidden';\" 
					 onmousemove=\"getID('div_controls_".$rs["0"]."').style.visibility='visible';getID('div_browse_".$rs["0"]."').style.visibility='visible';\">
				  				  
				  <div class='card-body text-center' onclick=\"jump('bceinfo.php?bce=".$rs["0"]."')\" style='cursor:pointer'>
					<div class='position-relative mb-2' style='height:200px; overflow:hidden;'>
					  <img  class='img-fluid' style='object-fit:cover;height:100%;width:100%;'";
					  if(file_exists("images/voters/".$rs["0"].".jpg")){
						echo" src='images/voters/".$rs["0"].".jpg?".date("h:i:s")."' />";
					  } else {
						echo" src='images/blank.jpg' />";
					  }
					echo "
					</div>
					<div style='text-align:center;padding:5px;font-size:12px'>
						<b class='text-truncate'>".str_replace($value,$rep,$rs["vname"])."</b><br>
						<b class='text-danger mb-1'>BCG Member</b><br>
						Sex: <b class='text-danger'>".(($rs["sex"]=="M")?"Male":"Female")."</b> | Age: <b>".$age."</b><br>
						Precinct: <b class='text-danger'>".str_replace($value,$rep,$rs["precinct"])."</b><br>
						Purok: ".str_replace($value,$rep,$rs["address"])."<br>
						<span>Total PL: <b>".$pl."</b>
					</div>
				  </div>
				</div>
			  </div>";
			  $i++;
			}
		  ?>
		</div>
	</div>
</div>

<div id="spacer" style="margin-top:-80px"></div>

<div id="toprint" style="display:none" style="width:1000px">
	<div style="text-align:center;margin-top:-80px">
		LIST OF PRECINCT LEADERS (PL)<BR>
		<b STYLE='font-size:20px'>
		<?php
			if($_GET["barangay"]!="All barangays" && $_GET["barangay"]!="")
				echo "BARANGAY ".$_GET["barangay"];
		?></b><br>
		CITY OF <?PHP echo $_SESSION["city_mun"]; ?>
	</div><br>
	<div>
		<table width=100%>
		<tr style='border:1px solid #545454'>
			<th style='font-size:16px;padding:0;text-align:center'>NO.</th>	
			<th style='font-size:16px;padding:2px'>PHOTO</th>				
			<th style='padding:0;font-size:16px;'>PRECINCT LEADER (PL)</th>
			<th style='padding:0;font-size:16px;'>SEX</th>
			<th style='padding:0;font-size:16px;'>AGE</th>
			<th style='padding:0;font-size:16px;'>PREC</th>
			<th style='padding:0;font-size:16px;'>CLUS</th>
			<th style='padding:0;font-size:16px;'>PUROK</th>
			<th style='padding:0;font-size:16px;'>HLs</th>
			<th style='font-size:16px;text-align:center'>SIGNATURE</th>			
		</tr>
		<?php
			$p=$_GET['page'];
			if($p!=""){
				$to=$p*100;
				$from=$to-100;
			}else{
				$to=100;
				$from=0;
			}
				
			$filter="";
			if($_GET["barangay"]!="All barangays" && $_GET["barangay"]!="" )
				$filter="v.barangay='".$_GET["barangay"]."' and";

			$stmt = $link->prepare("select * from voters v, pl p where {$filter}  p.vin=v.vin and v.city_mun=? order by vname");
			$stmt->execute([$_SESSION["city_mun"]]);
			$ex = $stmt;

			$i=1;
			
			while($rs3=$ex->fetch(PDO::FETCH_BOTH)){

				$stmt = $link->prepare("select cluster from clusters where precinct LIKE CONCAT('%', ?, '%')");
				$stmt->execute([$rs3["precinct"]]);
				$cluster = $stmt;
				$rsc=$cluster->fetch(PDO::FETCH_BOTH);
				$pl_cluster=$rsc[0];	

				$birthDate = $rs3["birth"];
				$age = "-";
				if (!empty($birthDate) && $birthDate !== '0000-00-00') {
					$birthObj = date_create($birthDate);
					if ($birthObj !== false) {
						$age = date_diff($birthObj, date_create('today'))->y;
					}
				}

				$stmt = $link->prepare('select count(*) from hl h where h.plvin=?');
				$stmt->execute([$rs3[0]]);
				$exhl = $stmt;
				$rshl=$exhl->fetch(PDO::FETCH_BOTH);
				$hln=$rshl[0];

				echo"<tr class='no_style' style='border:1px solid #545454'>

					<td style='font-size:16px;width:10px;text-align:center'>".$i.".</td>
					<td style='font-size:16px;width:10px;padding:2px'>";					
					echo"<img ";
						if(file_exists("images/voters/".$rs3["0"].".jpg")){
							echo"src='images/voters/".$rs3["0"].".jpg?".date("h:i:s")."' height=55 width=55 />";
						}else echo"src='images/blank.jpg' height=55 width=55 />";
					echo"</td>
					<td style='padding-right:0;font-size:16px;'>".$rs3["vname"]."</td>
					<td style='font-size:16px;'>".$rs3["sex"]."</td>
					<td style='font-size:16px;'>".$age."</td>
					<td style='font-size:16px;'>".$rs3["precinct"]."</td>
					<td style='font-size:16px;'>".$rsc[0]."</td>
					<td style='font-size:16px;'>".$rs3["address"]."</td>
					<td style='font-size:16px;'>".$hln."</td>	
					<td style='width:200px;font-size:16px;'>&nbsp;</td>";

				echo"</tr>";
									
				$i++;
			}
			$totPL=($i-1);	
		?>
		</table>
	</div>
</div>

<form method="post" enctype="multipart/form-data" class="m-0">

<div id="bottom" class="container my-4">
	<div id="thumbnails" class="tomb-grid">
		<?php
			$p=$_GET['page'];
			if($p!=""){
				$to=$p*100;
				$from=$to-100;
			}else{
				$to=100;
				$from=0;
			}
				
			$filter="";
			if($_GET["barangay"]!="All barangays" && $_GET["barangay"]!="" )
				$filter="v.barangay='".$_GET["barangay"]."' and";
				
			if(isset($_POST["b_search"])){
				$stmt = $link->prepare("select * from voters v, pl p where {$filter}
				   (v.vname LIKE CONCAT('%', ?, '%') or
					v.remarks LIKE CONCAT('%', ?, '%') or
					v.birth LIKE CONCAT('%', ?, '%') or
					v.sex LIKE CONCAT('%', ?, '%') or
					v.precinct LIKE CONCAT('%', ?, '%') or
					v.address LIKE CONCAT('%', ?, '%') or
					v.city_mun LIKE CONCAT('%', ?, '%')) and p.vin=v.vin and v.city_mun=? order by vname");
				$stmt->execute([$_POST["t_search"], $_POST["t_search"], $_POST["t_search"], $_POST["t_search"], $_POST["t_search"], $_POST["t_search"], $_POST["t_search"], $_SESSION["city_mun"]]);
				$ex = $stmt;
			} else {
				$stmt = $link->prepare("select * from voters v, pl p where {$filter}  p.vin=v.vin and v.city_mun=? order by vname");
				$stmt->execute([$_SESSION["city_mun"]]);
				$ex = $stmt;
			}

			$i=1;
			$value=strtoupper($_POST["t_search"]);
			$rep="<b style='color:#0014d0;background:#ffa0a0'>".$value."</b>";
			
			while($rs=$ex->fetch(PDO::FETCH_BOTH)){

				$birthDate = $rs["birth"];
				$age = "-";
				if (!empty($birthDate) && $birthDate !== '0000-00-00') {
					$birthObj = date_create($birthDate);
					if ($birthObj !== false) {
						$age = date_diff($birthObj, date_create('today'))->y;
					}
				}

				$stmt = $link->prepare("select cluster from clusters where precinct LIKE CONCAT('%', ?, '%')");
				$stmt->execute([$rs["precinct"]]);
				$cluster = $stmt;
				$rsc=$cluster->fetch(PDO::FETCH_BOTH);
				$pl_cluster=$rsc[0];	

				if(isset($_POST["b_remove_".$rs["0"]])){
					$stmt = $link->prepare('delete from mce where vin=?');
					$stmt->execute([$rs["vin"]]);
					jump("pllist.php");
				}
				
				if(isset($_POST["b_upImg_".$rs["0"]])){
					move_uploaded_file($_FILES["b_file_".$rs["0"]]["tmp_name"], "images/voters/".$rs[0].".jpg");
					jump("");
				}

				$img_src = file_exists("images/voters/".$rs[0].".jpg") ? "images/voters/".$rs[0].".jpg" : "images/blank.jpg";

				$stmt = $link->prepare('select * from voters v where v.vin=?');
				$stmt->execute([$rs["bcevin"]]);
				$eee = $stmt;
				$rsbce=$eee->fetch(PDO::FETCH_BOTH);
				
				$stmt = $link->prepare('select count(*) from hl h where h.plvin=?');
				$stmt->execute([$rs[0]]);
				$exx = $stmt;
				$rspl=$exx->fetch(PDO::FETCH_BOTH);
				$hl=$rspl[0];
				
				echo "
				<div class='tomb card-glass' id='div_".$rs["0"]."' 
					onmouseout=\"getID('div_controls_".$rs["0"]."').style.visibility='hidden';\" 
					onmousemove=\"getID('div_controls_".$rs["0"]."').style.visibility='visible';\">
					
					<div class='position-absolute top-0 start-0 z-2 text-light bg-dark px-2 py-1 font-weight-bold' style='border-bottom-right-radius:11px'>
						$i
					</div>
					
					<div class='tomb-img-container' onclick=\"$('#b_file_".$rs["0"]."').click();\">
						<img src='$img_src?".date("h:i:s")."' />
						<div class='tomb-img-overlay'>
							<i class='fa-solid fa-camera'></i>
							<span>Change Photo</span>
						</div>
					</div>
					<input type='file' name='b_file_".$rs["0"]."' id='b_file_".$rs["0"]."' style='display:none;' onchange=\"if(this.value!='') $('#b_upImg_".$rs["0"]."').click();\" />
					<input type='submit' name='b_upImg_".$rs["0"]."' id='b_upImg_".$rs["0"]."' style='display:none;' />
					
					<div class='mt-2'>
						<h5 style='font-size: 15px; font-weight: 700; margin-bottom: 2px; text-transform: uppercase;'>".str_replace($value, $rep, $rs["vname"])."</h5>
						<p class='text-muted small mb-2' style='font-size: 12px;'><i class='fa-solid fa-id-card'></i> ID: " . sprintf("%04d", $rs["vin"]) . "</p>
					</div>
					
					<div class='small text-muted space-y-1' style='font-size: 13px;'>
						<div>Sex: <strong>" . ($rs["sex"] == "M" ? "Male" : "Female") . "</strong> &nbsp; Age: <strong>$age y.o.</strong></div>
						<div>Precinct: <strong>".str_replace($value, $rep, $rs["precinct"])."</strong> &nbsp; Cluster: <strong>".$rsc[0]."</strong></div>
						<div>Address: <strong>".str_replace($value, $rep, $rs["address"]).", ".str_replace($value, $rep, $rs["barangay"])."</strong></div>
					</div>
					
					<hr class='my-2'>
					<div class='small text-muted space-y-1' style='font-size: 13px;'>
						<div>Total HL Members: <strong class='text-primary'>$hl</strong></div>
						<div>BCG Leader: <a style='color:#00940e; font-weight:600;' href='bceinfo.php?bce=".$rsbce["vin"]."'>".str_replace($value, $rep, $rsbce["vname"])."</a></div>
					</div>";
					
					if(($_SESSION["access"]=="SuperAdmin") or ($_SESSION["access"]=="Admin")){
						echo "
						<div class='mt-3 d-flex gap-2 flex-wrap' style='visibility: hidden; transition: var(--transition);' id='div_controls_".$rs["0"]."'>
							<button type='button' onclick=\"deletepl2('".$rs["0"]."')\" class='btn btn-danger px-2 py-1' style='font-size:11px;'><i class='fa-solid fa-trash-can'></i> Remove</button>
							<button type='button' onclick=\"jump('plinfo.php?pl=".$rs["0"]."')\" class='btn btn-dark px-2 py-1' style='font-size:11px;'><i class='fa-solid fa-eye'></i> View HL</button>
						</div>";
					}
				echo "
				</div>";
				$i++;
			}
		?>
	</div>
</div>

</form>

<script>
	var table=0;
	var hlvotno=0;
				
	function add(){
		var scr=screen;
		var obj=getID("voters_holder");
		var w = obj.clientWidth;
		var h = obj.clientHeight;
					
		var left = (scr.width-w)/2;
		var top = (scr.height-h)/2;
					
		//obj.style.top=top+"px";
		obj.style.top=(15+document.body.scrollTop)+"px";
		obj.style.left=left+"px";
		obj.style.visibility="visible";
	}	

	function printF(){	
		$('#div_pl').hide();	
		$('#spacer').show();
		$('#toprint').show();
		$('#searchForm').hide(); 
		$('#thumbnails').hide(); 
		$('#cssmenu-wrapper').hide();
		window.print(); 																					
		$('#div_pl').hide();	
		$('#spacer').hide();
		$('#toprint').hide();
		$('#thumbnails').show(); 
		$('#searchForm').show();
		$('#cssmenu-wrapper').show();
	}
	
	function deletepl2(vin){	
		if(confirm("Are you Sure?")){
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
			xmlhttp.open("GET","ajax/deletepl2.php?vin="+vin,true);
			xmlhttp.send();
		}
	}
</script>

</body>

</html>
