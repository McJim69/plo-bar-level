<?php
	require("connect.php");
	require("head.php");
	require("popvoters.php");
	require("menu.php");

	$bar="BARANGAY ".$_GET["barangay"];
		if($bar=="BARANGAY ")
	$bar="ALL BARANGAYS";
?>

<script> setActive("bce"); </script>

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
					<button type="button" class="btn btn-success flex-fill" onclick="getID('div_bce').style.display='block';"><i class="fa-solid fa-plus"></i> Add BCG</button>
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

<div class="container-fluid" id="div_bce" style="margin-top:-185px;display:none;position:absolute;left:0;z-index:2;width:100%;height:100%;background:url('images/blank_bg.png')no-repeat;background-fill:cover;background-size:100%">
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
		  <b> &nbsp; SELECT CHAIRMAN TO BE ASSIGNED</b>

		  <input type="button" value="Close" 
				 onclick="getID('div_bce').style.display='none';" 
				 style="font-weight:bold;padding:5px;border:none;cursor:pointer;" />
		</div>
		<div class="row mt-4">
		<?php
			$stmt = $link->prepare('select * from voters v, mce m where m.vin=v.vin and v.city_mun=? order by vname');
			$stmt->execute([$_SESSION["city_mun"]]);
			$ex = $stmt;

			$i=1;

			$value=strtoupper($_POST["t_search"]);

			$rep="<b style='color:#0014d0;background:#ffa0a0'>".$value."</b>";

			while($rs=$ex->fetch(PDO::FETCH_BOTH)){

				$stmt = $link->prepare('select count(*) from bce where mcevin=?');
				$stmt->execute([$rs["vin"]]);
				$ex1 = $stmt;
				$rsbce=$ex1->fetch(PDO::FETCH_BOTH);
				$bce=$rsbce[0];
									
				echo "
				<div class='col-md-3 mb-4'>
					<div class='card h-100 shadow-sm style='".$s."' id='div_".$rs["0"]."' 
						onmouseout=\"getID('div_controls_".$rs["0"]."').style.visibility='hidden';\" 
						onmousemove=\"getID('div_controls_".$rs["0"]."').style.visibility='visible';\" >
										
						<div class='card-body text-center' onclick=\"jump('mceinfo.php?mce=".$rs["0"]."')\" style='cursor:pointer'>
							<div class='position-relative mb-2' style='height:200px; overflow:hidden;'>
								
								<img  class='img-fluid' style='object-fit:cover;height:100%;width:100%;'";
							
								if(file_exists("images/voters/".$rs["0"].".jpg")){
									echo" src='images/voters/".$rs["0"].".jpg?".date("h:i:s")."' />";
								}
								else
									echo" src='images/blank.jpg' />";
							
								echo"
							</div>
						</div>
						<div style='text-align:center;padding:5px;font-size:12px'>
							<b class='text-truncate'>".str_replace($value,$rep,$rs["vname"])."</b><br>
							<b style='color:#d32727'>BCG Chairman</b><br>
							<span>
								ID No: <b style='color:#002d94'>"; $cont = $rs["vin"]; printf("%04d", $cont); echo"</b> | 
								Precinct: <b style='color:#d32727'>".str_replace($value,$rep,$rs["precinct"])."</b> <br>
							</span>
							<span style='color:#943800'>
								Purok: ".str_replace($value,$rep,$rs["address"])."
							</span><br>					
							<b style='color:#00940e' href='mceinfo.php?mce=".$rs["0"]."' >Total BEC Members: ".$bce."</b>				
						</div>
					</div>
				</div>
			</div>";
			$i++;
			}
		?>
	</div>
</div>

<div style="width:1000px;margin:0 auto" >
<div style="text-align:center;background:transparent;display:none" id='spacer' ></div>
	<div style="text-align:center;background:transparent;display:none" id='header' >
		List of Barangay Core Group (BCG)<BR>
		<b STYLE='font-size:20px' >CITY OF <?PHP echo $_SESSION["city_mun"]; ?></b>
		<?php
			if($_GET["barangay"]!="All barangays" && $_GET["barangay"]!="")
				echo "<br>BARANGAY ".$_GET["barangay"];
		?>
	</div>	
	<div id="grid" style="display:none">
		<table width=100%>
			<tr height='50px'>
				<th style='text-align:center;font-size:14px;border:1px solid #000'>NO</th>
				<th style='text-align:center;font-size:14px;border:1px solid #000'>PIC</th>
				<th style='text-align:center;font-size:14px;border:1px solid #000'>NAME OF BCG MEMBERS</th>
				<th style='text-align:center;font-size:14px;border:1px solid #000'>IDN</th>				
				<th style='text-align:center;font-size:14px;border:1px solid #000'>SEX</th>				
				<th style='text-align:center;font-size:14px;border:1px solid #000'>AGE</th>				
				<th style='text-align:center;font-size:14px;border:1px solid #000'>PREC</th>				
				<th style='text-align:center;font-size:14px;border:1px solid #000'>PUROK</th>
				<th width='180px' style='text-align:center;font-size:14px;border:1px solid #000'>SIGNATURE</TH>
			</tr>
			
			<?php
				$bar="";
				if($_GET["barangay"]!="" && $_GET["barangay"]!="All barangays"){
					$bar=" and v.barangay='".$_GET["barangay"]."' ";
				}
				
				$stmt = $link->prepare("select * from bce b, voters v  where b.vin=v.vin {$bar} and v.city_mun=? order by vname");
				$stmt->execute([$_SESSION["city_mun"]]);
				$ex3 = $stmt;
				$i=1;
				while($rs3=$ex3->fetch(PDO::FETCH_BOTH)){

				$birthDate = $rs3["birth"];;
				$birthDate = $birthDate;
				$age = "-";
				if (!empty($birthDate) && $birthDate !== '0000-00-00') {
					$birthObj = date_create($birthDate);
					if ($birthObj !== false) {
						$age = date_diff($birthObj, date_create('today'))->y;
					}
				}
					
				echo "<tr>
					<td style='text-align:center;font-size:14px;border:1px solid #000'>".$i.".</td>
					<td class='no_style' style='text-align:center;border:1px solid #000;'>";					
					echo"<img ";
						if(file_exists("images/voters/".$rs3["2"].".jpg")){
							echo"src='images/voters/".$rs3["2"].".jpg?".date("h:i:s")."' height=80 width=80 />";
						}else echo"src='images/blank.jpg' height=80 width=80 />";
					echo"</td>					
					<td style='font-size:14px;border:1px solid #000'>".$rs3["vname"]."</td>
					<td style='text-align:center;font-size:14px;border:1px solid #000'>"; $cont = $rs3["vin"]; printf("%04d", $cont); echo"</td>
					<td style='text-align:center;font-size:14px;border:1px solid #000'>".$rs3["sex"]."</td>
					<td style='text-align:center;font-size:14px;border:1px solid #000'>".$age."</td>
					<td style='text-align:center;font-size:14px;border:1px solid #000'>".$rs3["precinct"]."</td>
					<td style='font-size:14px;border:1px solid #000'>".$rs3["address"]."</td>
					<td style='font-size:14px;border:1px solid #000'></td>
				</tr>";
				$i++;
				}
			?>
		</table>
	</div>
<form method="post" enctype="multipart/form-data" class="m-0">

<div id="thumbnails" class="tomb-grid" style="margin-top:-50px">
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
			$stmt = $link->prepare("select * from voters v,bce b where {$filter}
				(v.vname LIKE CONCAT('%', ?, '%') or
				v.remarks LIKE CONCAT('%', ?, '%') or
				v.birth LIKE CONCAT('%', ?, '%') or
				v.sex LIKE CONCAT('%', ?, '%') or
				v.precinct LIKE CONCAT('%', ?, '%') or
				v.address LIKE CONCAT('%', ?, '%') or
				v.city_mun LIKE CONCAT('%', ?, '%')) and b.vin=v.vin and v.city_mun=? order by vname");
			$stmt->execute([$_POST["t_search"], $_POST["t_search"], $_POST["t_search"], $_POST["t_search"], $_POST["t_search"], $_POST["t_search"], $_POST["t_search"], $_SESSION["city_mun"]]);
			$ex = $stmt;
			} else {
				$stmt = $link->prepare("select * from voters v, bce b where {$filter}  b.vin=v.vin and v.city_mun=? order by vname");
				$stmt->execute([$_SESSION["city_mun"]]);
				$ex = $stmt;
			}

			$i=1;
			$value=strtoupper($_POST["t_search"]);
			$rep="<b style='color:#0014d0;background:#ffa0a0'>".$value."</b>";

		while($rs=$ex->fetch(PDO::FETCH_BOTH)){

			$birthDate = $rs["birth"];;
			$birthDate = $birthDate;
			$age = "-";
			if (!empty($birthDate) && $birthDate !== '0000-00-00') {
				$birthObj = date_create($birthDate);
				if ($birthObj !== false) {
					$age = date_diff($birthObj, date_create('today'))->y;
				}
			}
				
			if(isset($_POST["b_remove_".$rs["0"]])){
				$stmt = $link->prepare('delete from mce where vin=?');
				$stmt->execute([$rs["0"]]);
				jump("mcelist.php");
			}

			if(isset($_POST["b_upImg_".$rs["0"]])){
				move_uploaded_file($_FILES["b_file_".$rs["0"]]["tmp_name"], "images/voters/".$rs[0].".jpg");
				jump("");
			}

			$img_src = file_exists("images/voters/".$rs[0].".jpg") ? "images/voters/".$rs[0].".jpg" : "images/blank.jpg";
			
			$stmt = $link->prepare('select * from voters v where v.vin=?');
			$stmt->execute([$rs["mcevin"]]);
			$eee = $stmt;
			$rsmce=$eee->fetch(PDO::FETCH_BOTH);
			
			$stmt = $link->prepare('select count(*) from pl p where p.bcevin=? ');
			$stmt->execute([$rs[0]]);
			$exx = $stmt;
			$rsbce=$exx->fetch(PDO::FETCH_BOTH);
			$pl=$rsbce[0];

			echo "
			<div class='tomb card-glass' id='div_".$rs["vin"]."' 
				onmouseout=\"getID('div_controls_".$rs["vin"]."').style.visibility='hidden';\" 
				onmousemove=\"getID('div_controls_".$rs["vin"]."').style.visibility='visible';\">
				
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
					<div>Precinct: <strong>".str_replace($value, $rep, $rs["precinct"])."</strong></div>
					<div>Address: <strong>".str_replace($value, $rep, $rs["address"]).", ".str_replace($value, $rep, $rs["barangay"])."</strong></div>
				</div>
				
				<hr class='my-2'>
				<div class='small text-muted space-y-1' style='font-size: 13px;'>
					<div class='text-primary' style='font-weight: 600;'><i class='fa-solid fa-users'></i> Total PLs: $pl</div>
					<div>BCE: <a style='color:#00940e; font-weight:600;' href='mceinfo.php?mce=".$rsmce["vin"]."'>".str_replace($value, $rep, $rsmce["vname"])."</a></div>
				</div>";
				
				if(($_SESSION["access"]=="SuperAdmin") or ($_SESSION["access"]=="Admin")){
					echo "
					<div class='mt-3 d-flex gap-2' style='visibility: hidden; transition: var(--transition);' id='div_controls_".$rs["vin"]."'>
						<button type='button' onclick=\"deletebce('".$rs["vin"]."')\" class='btn btn-sm btn-danger px-3'><i class='fa-solid fa-trash-can'></i> Remove</button>
						<button type='button' onclick=\"jump('bceinfo.php?bce=".$rs["vin"]."')\" class='btn btn-sm btn-dark px-3'><i class='fa-solid fa-eye'></i> View PLs</button>
					</div>";
				}
			echo "
			</div>";
			$i++;
		}
		echo "</div>";
	?>
	</form><br><br>
</div>

<script>
	var table=0;
	var hlvotno=0;

	function printF(){
		$('#header').show();
		$('#spacer').hide();																						
		$('#searchForm').hide(); 
		$('#thumbnails').hide(); 
		$('#grid').show(); 
	
		window.print(); 																					
		$('#grid').hide(); 
		$('#thumbnails').show(); 
		$('#searchForm').show();
		$('#header').hide();
		$('#spacer').show();
	}

	function deletebce(vin){	
		if(confirm("Are you sure?")){
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
			xmlhttp.open("GET","ajax/deletebce.php?vin="+vin,true);
			xmlhttp.send();
		}
	}
</script>

</body>

</html>
