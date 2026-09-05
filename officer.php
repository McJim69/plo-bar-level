<?php
	require("connect.php");
	require("head.php");
	require("popvoters.php");
	require("menu.php");
?>

<script> setActive("io"); </script>

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
					<button type="button" class="btn btn-success flex-fill" onclick="add();"><i class="fa-solid fa-plus"></i> Add IO</button>
					<button type="button" class="btn btn-dark" onclick="printF()"><i class="fa-solid fa-print"></i> Print</button>
				</div>
				<div class="col-md-3">
					<select class="form-select" onchange="jump('?barangay='+encodeURIComponent(this.value))">
						<option value="All barangays">All barangays</option>
						<?php
							$stmt = $link->prepare('select barangay from voters where city_mun=? group by barangay order by barangay');
							$stmt->execute([$_SESSION["city_mun"]]);
							$ex2 = $stmt;
							while($rs=$ex2->fetch(PDO::FETCH_BOTH)){
								$sel = (isset($_GET["barangay"]) && $_GET["barangay"]===$rs[0]) ? "selected" : "";
								echo "<option value='".htmlspecialchars($rs[0])."' $sel>".htmlspecialchars($rs[0])."</option>";
							}
						?>
					</select>
				</div>
			</div>
		</div>
	</form>	

	<div id="thumbnails" class="tomb-grid" style="margin-top:-10px">
		<?php
			$rec=1000;
			$p=isset($_GET['page']) ? $_GET['page'] : 1;
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
								
			$bar="";
			if(isset($_GET["barangay"]) && $_GET["barangay"]!="" && $_GET["barangay"]!="All barangays" )
				$bar=" v.barangay='".$_GET["barangay"]."'  and ";
			
			$stmt = $link->prepare("select * from officer hc, voters v where {$bar} hc.vin=v.vin and v.city_mun=? order by v.vname LIMIT {$from},{$to} ");
			$stmt->execute([$_SESSION["city_mun"]]);
			$ex = $stmt;
			if(isset($_POST["b_search"]) && $_POST["t_search"]!=""){
				$stmt = $link->prepare("select * from officer hc, voters v where (v.vname LIKE CONCAT('%', ?, '%') or v.barangay LIKE CONCAT('%', ?, '%')) and {$bar} hc.vin=v.vin and v.city_mun=? order by v.vname LIMIT {$from},{$to} ");
				$stmt->execute([$_POST["t_search"], $_POST["t_search"], $_SESSION["city_mun"]]);
				$ex = $stmt;
			}
				
			$value=isset($_POST["t_search"]) ? strtoupper($_POST["t_search"]) : "";
			$rep="<b style='color:#0014d0;background:#ffa0a0'>".$value."</b>";
			
			while($rs=$ex->fetch(PDO::FETCH_BOTH)){

				$stmt = $link->prepare("select cluster from clusters where precinct LIKE CONCAT('%', ?, '%')");
				$stmt->execute([$rs["precinct"]]);
				$cluster = $stmt;
				$rsc=$cluster->fetch(PDO::FETCH_BOTH);
				$cluster_num = $rsc ? $rsc[0] : "-";

				$birthDate = $rs["birth"];
				$age = "-";
				if (!empty($birthDate) && $birthDate !== '0000-00-00') {
					$birthObj = date_create($birthDate);
					if ($birthObj !== false) {
						$age = date_diff($birthObj, date_create('today'))->y;
					}
				}
																				
				if(isset($_POST["b_upImg_".$rs["vin"]])){
					move_uploaded_file($_FILES["b_file_".$rs["vin"]]["tmp_name"], "images/voters/".$rs["vin"].".jpg");
					jump("");
				}

				$img_src = file_exists("images/voters/".$rs["vin"].".jpg") ? "images/voters/".$rs["vin"].".jpg" : "images/blank.jpg";
				
				echo "
				<div class='tomb card-glass' id='div_".$rs["vin"]."' 
					onmouseout=\"getID('div_controls_".$rs["vin"]."').style.visibility='hidden';\" 
					onmousemove=\"getID('div_controls_".$rs["vin"]."').style.visibility='visible';\">
					
					<div class='position-absolute top-0 start-0 z-2 text-light bg-dark px-2 py-1 font-weight-bold' style='border-bottom-right-radius:11px'>
						$i
					</div>
					
					<div class='tomb-img-container' onclick=\"$('#b_file_".$rs["vin"]."').click();\">
						<img src='$img_src?".date("h:i:s")."' />
						<div class='tomb-img-overlay'>
							<i class='fa-solid fa-camera'></i>
							<span>Change Photo</span>
						</div>
					</div>
					<input type='file' name='b_file_".$rs["vin"]."' id='b_file_".$rs["vin"]."' style='display:none;' onchange=\"if(this.value!='') $('#b_upImg_".$rs["vin"]."').click();\" />
					<input type='submit' name='b_upImg_".$rs["vin"]."' id='b_upImg_".$rs["vin"]."' style='display:none;' />
					
					<div class='mt-2'>
						<h5 style='font-size: 15px; font-weight: 700; margin-bottom: 2px; text-transform: uppercase;'>".str_replace($value, $rep, $rs["vname"])."</h5>
						<p class='text-muted small mb-2' style='font-size: 12px;'><i class='fa-solid fa-id-card'></i> ID: " . sprintf("%04d", $rs["vin"]) . "</p>
					</div>
					
					<div class='small text-muted space-y-1' style='font-size: 13px;'>
						<div>Sex: <strong>" . ($rs["sex"] == "M" ? "Male" : "Female") . "</strong> &nbsp; Age: <strong>$age y.o.</strong></div>
						<div>Precinct: <strong>".str_replace($value, $rep, $rs["precinct"])."</strong> &nbsp; Cluster: <strong>".$cluster_num."</strong></div>
						<div>Address: <strong>".str_replace($value, $rep, $rs["address"]).", ".str_replace($value, $rep, $rs["barangay"])."</strong></div>
					</div>
					
					<hr class='my-2'>
					<div class='small text-muted space-y-1' style='font-size: 13px;'>
						<div class='text-danger' style='font-weight: 600;'>Information Officer</div>
					</div>";
					
					if(($_SESSION["access"]=="SuperAdmin") or ($_SESSION["access"]=="Admin")){
						echo "
						<div class='mt-3 d-flex gap-2 flex-wrap' style='visibility: hidden; transition: var(--transition);' id='div_controls_".$rs["vin"]."'>
							<button type='button' onclick=\"deleteio('".$rs["vin"]."')\" class='btn btn-sm btn-danger px-2 py-1' style='font-size:11px;'><i class='fa-solid fa-trash-can'></i> Remove</button>
						</div>";
					}
				echo"</div>";
			$i++;
			}
		?>
	</div>
</div>

<!-- Print-Only Layout Section -->
<div id="toprint" style="width:1000px;margin:0 auto;display:none"><br><br>
	<DIV style="text-align:center;background:transparent;" id='spacer' ><br><br><br><br></div><center>
	<div style="text-align:center;background:transparent;display:none" id='header'>
		LIST OF INFORMATION OFFICER<BR>
		<b STYLE='font-size:20px'>CITY OF <?PHP echo $_SESSION["city_mun"]; ?></b>
		<?php
			if(isset($_GET["barangay"]) && $_GET["barangay"]!="All barangays" && $_GET["barangay"]!="")
				echo "<br>BARANGAY ".$_GET["barangay"];
		?>
		<hr>
	</div></center>
		
	<?php
		echo "<table width=100%>
			<tr>
				<th colspan=2>VOTER'S NAME</th>
				<th>IDN</th>
				<th>SEX</th>
				<th>AGE</th>
				<th>PREC</th>
				<th>CLUS</th>
				<th>PUROK</th>
				<TH>REMARKS</TH>
			</tr>
		";
		$rec=2000;
		$p=isset($_GET['page']) ? $_GET['page'] : 1;
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
		
		$stmt = $link->prepare("select * from officer hc, voters v where hc.vin=v.vin and v.city_mun=? order by v.vname limit {$from},{$to} ");
		$stmt->execute([$_SESSION["city_mun"]]);
		$ex1 = $stmt;
		
		while($rs3=$ex1->fetch(PDO::FETCH_BOTH)){
			
			$stmt = $link->prepare("select cluster from clusters where precinct LIKE CONCAT('%', ?, '%')");
			$stmt->execute([$rs3["precinct"]]);
			$cluster2 = $stmt;
			$rsc2=$cluster2->fetch(PDO::FETCH_BOTH);
			$hm_cluster=$rsc2 ? $rsc2[0] : "-";

			$birthDate = $rs3["birth"];
			$age = "-";
			if (!empty($birthDate) && $birthDate !== '0000-00-00') {
				$birthObj = date_create($birthDate);
				if ($birthObj !== false) {
					$age = date_diff($birthObj, date_create('today'))->y;
				}
			}
			
			echo "<tr style='";
				if($rs3["ato"]==="Sure Wala Diri")
					echo "color:#09c700";
				else if($rs3["ato"]==="Sure")
					echo "color:#000";
				else if($rs3["ato"]==="Undecided")
					echo "color:#e20000";
			echo"' >
				<td style='WIDTH:10px;padding:5px' >".$i.".</td>
				<td>".htmlspecialchars($rs3["vname"])."</td>
				<td>"; $cont = $rs3["vin"]; printf("%04d", $cont); echo"</td>
				<td style='font-size:10px'>".$rs3["sex"]."</td>
				<td style='font-size:10px'>".$age."</td>
				<td style='font-size:10px'>".$rs3["precinct"]."</td>
				<td style='font-size:10px'>".$hm_cluster."</td>
				<td style='font-size:10px'>".$rs3["address"]."</td>
				<td style='font-size:10px'>".$rs3[2]."</td>
			</tr>";
			$i++;
		}
		echo"</table><br><br>";
	?>	
</div>

<script>
	function printF(){
		$('#header').show();
		$('#spacer').hide();
		$('#searchForm').hide(); 
		$('#thumbnails').hide(); 
		$('#toprint').show(); 
		$('#cssmenu-wrapper').hide();
		
		window.print(); 
		
		$('#toprint').hide(); 
		$('#thumbnails').show(); 
		$('#searchForm').show();
		$('#header').hide();
		$('#spacer').show();
		$('#cssmenu-wrapper').show();
	}
</script>

<script>
	var table=0;
	var hlvotno=0;
								
	function getVoters(value){	
		table="mce";
		var url = "ajax/getvoters.php?value="+encodeURIComponent(value)+"&id="+hlvotno+"&table="+table;
		fetch(url)
			.then(response => response.text())
			.then(data => {
				getID("query_voters").innerHTML = data;
			})
			.catch(err => console.error("Fetch error in getVoters:", err));
	}
				
	function add(){
		getVoters('');
		var addModal = new bootstrap.Modal(document.getElementById('votersModal'));
		addModal.show();
	}
</script>

</body>

</html>