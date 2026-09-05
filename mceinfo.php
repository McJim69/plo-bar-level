<?php
	require("connect.php");
	require("head.php");

	$stmt = $link->prepare('select * from voters where vin=?');
	$stmt->execute([$_GET["mce"]]);
	$ex = $stmt;
	$rsmce=$ex->fetch(PDO::FETCH_BOTH);
	
	$stmt = $link->prepare('select count(*) from bce where mcevin=?');
	$stmt->execute([$_GET["mce"]]);
	$ex = $stmt;
	$rsbce=$ex->fetch(PDO::FETCH_BOTH);
	$bce=$rsbce[0];
	
	$mce="'".$_GET["mce"]."'";
?>

<div id="toprint" class="container d-none" style="max-width: 1000px;">
	<div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
		<div>
			<h2 style="font-weight: 700;"><?php echo $rsmce["vname"]; ?></h2>
			<h4 class="text-muted">Barangay Core Group (BCG) Chairman</h4>
			<p class="mb-0 text-muted"><?php echo $rsmce["barangay"] . ", " . $rsmce["city_mun"] . ", " . $rsmce["province"]; ?></p>
		</div>
		<div>
			<h5>LIST OF BCG MEMBERS</h5>
		</div>
	</div>
	
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>NO.</th>
				<th>NAME OF BCG MEMBERS</th>
				<th>IDN</th>
				<th>SEX</th>
				<th>AGE</th>
				<th>PRECINCT</th>
				<th>PUROK</th>
				<th>BARANGAY</th>
				<th>TOTAL PLs</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$stmt = $link->prepare('select * from bce b, voters v where b.mcevin=? and b.vin=v.vin order by vname');
				$stmt->execute([$_GET["mce"]]);
				$ex1 = $stmt;
				$i=1;
				while($rs3=$ex1->fetch(PDO::FETCH_BOTH)){
					$birthDate = $rs3["birth"];
					$birthDate = explode("-", $birthDate);
					$age = "-";
					if(count($birthDate) === 3) {
						$age_temp_date = $birthDate[0] . '-' . $birthDate[1] . '-' . $birthDate[2];
			$age = "-";
			if ($birthDate[0] !== '0000' && !empty($birthDate[0])) {
				$birthObj = date_create($age_temp_date);
				if ($birthObj !== false) {
					$age = date_diff($birthObj, date_create('today'))->y;
				}
			}
					}

					$stmt = $link->prepare('select count(*) from pl where bcevin=?');
					$stmt->execute([$rs3["vin"]]);
					$exx = $stmt;
					$rsbce=$exx->fetch(PDO::FETCH_BOTH);
					$pl=$rsbce[0];

					echo "<tr>
						<td>".$i.".</td>
						<td>".$rs3["vname"]."</td>
						<td>" . sprintf("%04d", $rs3["vin"]) . "</td>
						<td>".$rs3["sex"]."</td>
						<td>".$age."</td>
						<td>".$rs3["precinct"]."</td>
						<td>".$rs3["address"]."</td>
						<td>".$rs3["barangay"]."</td>
						<td>".$pl."</td>
					</tr>";
					$i++;
				}
			?>
		</tbody>
	</table>
</div>

<?php 
	require("menu.php"); 
	require("popvoters.php");
?>
<script>setActive("mce");</script>

<div id="bottom">	
	<div class="container my-4">
		<!-- Chairman Glass Card -->
		<div class="card-glass mb-4 text-white" style="background: linear-gradient(135deg, #991b1b 0%, #7f1d1d 100%); border: none;">
			<div class="row align-items-center g-4">
				<div class="col-md-3 text-center">
					<?php
						if(file_exists("images/voters/".$_GET["mce"].".jpg")){
							echo "<img style='border: 3px solid rgba(255,255,255,0.3); border-radius: var(--radius-md); max-width: 100%; max-height: 220px; object-fit: cover;' src='images/voters/".$_GET["mce"].".jpg?".date("h:i:s")."' />"; 
						}else{
							echo "<img style='border: 3px solid rgba(255,255,255,0.3); border-radius: var(--radius-md); max-width: 100%; max-height: 220px; object-fit: cover;' src='images/blank.jpg' />"; 
						}
					?>
				</div>
				<div class="col-md-9">
					<h2 class="text-uppercase mb-1" style="font-weight: 700; color: #ffffff;"><?php echo $rsmce["vname"]; ?></h2>
					<h5 style="color: rgba(255,255,255,0.8); font-weight: 500;">BARANGAY CORE GROUP (BCG) CHAIRMAN</h5>
					<hr style="border-color: rgba(255,255,255,0.2);">
					<div class="row g-2 mb-3" style="font-size: 14px;">
						<div class="col-sm-6">ID No: <strong class="text-warning"><?php printf("%04d", $rsmce["vin"]); ?></strong></div>
						<div class="col-sm-6">Precinct: <strong class="text-warning"><?php echo $rsmce["precinct"]; ?></strong></div>
						<div class="col-sm-6">Address: <strong><?php echo $rsmce["address"] . ", " . $rsmce["barangay"]; ?></strong></div>
						<div class="col-sm-6">Total BCG Members: <strong class="text-warning"><?php echo $bce; ?></strong></div>
					</div>
					
					<div id="d_controls" class="d-flex gap-2 flex-wrap">
						<button class="btn btn-success btn-sm" onclick="add()"><i class="fa-solid fa-user-plus"></i> Add BCG Member</button>
						<button class="btn btn-light btn-sm text-danger" onclick="printF()"><i class="fa-solid fa-print"></i> Print</button>
						<button class="btn btn-outline-light btn-sm" onclick="jump('bcelist.php')"><i class="fa-solid fa-list"></i> BCG List</button>
						<button class="btn btn-outline-light btn-sm" onclick="jump('index.php')"><i class="fa-solid fa-house"></i> Home</button>
					</div>
					<script>
						function printF(){
							$('#d_controls').addClass('d-none');
							$('#bottom').addClass('d-none');
							$('#toprint').removeClass('d-none');
							window.print();
							$('#toprint').addClass('d-none');
							$('#bottom').removeClass('d-none');
							$('#d_controls').removeClass('d-none');
						}
					</script>
				</div>
			</div>
		</div>

		<!-- Title Section -->
		<div class="mb-4">
			<h4 style="font-weight: 700; color: var(--text-main);">
				Barangay Core Group (BCG) belonged to <span class="text-danger"><?php echo htmlspecialchars($rsmce["vname"]); ?></span>:
			</h4>
		</div>

		<!-- Cards Grid Container -->
		<form method="post" enctype="multipart/form-data" class="m-0">
			<div id="thumbnails" class="tomb-grid">
				<?php
					$stmt = $link->prepare('select * from voters v, bce b where b.mcevin=? and b.vin=v.vin order by v.vname');
					$stmt->execute([$_GET["mce"]]);
					$ex = $stmt;

					$i=1;
					$value = isset($_POST["t_search"]) ? strtoupper($_POST["t_search"]) : "";
					$rep = "<b style='color:#0014d0;background:#ffa0a0'>".$value."</b>";

					while($rs=$ex->fetch(PDO::FETCH_BOTH)){
						$birthDate = $rs["birth"];
						$birthDate = explode("-", $birthDate);
						$age = "-";
						if(count($birthDate) === 3) {
							$age_temp_date = $birthDate[0] . '-' . $birthDate[1] . '-' . $birthDate[2];
			$age = "-";
			if ($birthDate[0] !== '0000' && !empty($birthDate[0])) {
				$birthObj = date_create($age_temp_date);
				if ($birthObj !== false) {
					$age = date_diff($birthObj, date_create('today'))->y;
				}
			}
						}

						$stmt = $link->prepare('select count(*) from pl where bcevin=?');
						$stmt->execute([$rs["vin"]]);
						$exx = $stmt;
						$rsbce=$exx->fetch(PDO::FETCH_BOTH);
						$plm=$rsbce[0];
										
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
						
						echo "
						<div class='tomb card-glass' id='div_".$rs["vin"]."' 
							onmouseout=\"getID('div_controls_".$rs["vin"]."').style.visibility='hidden';\" 
							onmousemove=\"getID('div_controls_".$rs["vin"]."').style.visibility='visible';\">
							
							<img onclick=\"jump('bceinfo.php?bce=".$rs["vin"]."')\" src='$img_src?".date("h:i:s")."' style='cursor:pointer;' />
							
							<div class='mt-2'>
								<h5 style='font-size: 15px; font-weight: 700; margin-bottom: 2px; text-transform: uppercase;'>".str_ireplace($value, $rep, $rs["vname"])."</h5>
								<p class='text-muted small mb-2' style='font-size: 12px;'><i class='fa-solid fa-id-card'></i> ID: " . sprintf("%04d", $rs["vin"]) . "</p>
							</div>
							
							<div class='small text-muted space-y-1' style='font-size: 13px;'>
								<div>Sex: <strong>" . ($rs["sex"] == "M" ? "Male" : "Female") . "</strong></div>
								<div>Age: <strong>$age y.o.</strong></div>
								<div>Precinct: <strong>".str_ireplace($value, $rep, $rs["precinct"])."</strong></div>
								<div>Address: <strong>".str_ireplace($value, $rep, $rs["address"])."</strong></div>
								<div class='text-success mt-2' style='font-weight: 600;'><i class='fa-solid fa-users'></i> Total PLs: $plm</div>
							</div>
							
							<div class='mt-3 d-flex gap-2' style='visibility: hidden; transition: var(--transition);' id='div_controls_".$rs["vin"]."'>
								<button onclick=\"deletebce('".$rs["bno"]."')\" class='btn btn-sm btn-danger px-3'><i class='fa-solid fa-trash-can'></i> Remove</button>
								<button onclick=\"jump('bceinfo.php?bce=".$rs["vin"]."')\" type='button' class='btn btn-sm btn-dark px-3'><i class='fa-solid fa-eye'></i> PL Records</button>
							</div>
						</div>";
						$i++;
					}
					if(isset($_GET["add"]) && $_GET["add"] == "auto"){
						echo "<script>$('#bAddBCE').click()</script>";
					}
				?>
			</div>
		</form>
	</div>
</div>

</body>
</html>

<script>
	function addmce(id,row){
		var vin=id;
		var url = "ajax/addbce.php?mcevin="+<?php echo $mce; ?>+"&vin="+vin;
		fetch(url)
			.then(response => response.text())
			.then(data => {
				if(data.trim() == "Success"){
					$("#q_tr_"+row).animate({
						opacity:0
					},500,function(){
						$("#q_tr_"+row).css("display","none");
					});
				}else{
					alert(data);
				}
			})
			.catch(err => console.error("Fetch error in addmce:", err));
	}
			
	function deletebce(bno){	
		if(confirm("Are you Sure?")){
			var url = "ajax/deletebce.php?bno="+bno;
			fetch(url)
				.then(response => response.text())
				.then(data => {
					if(data.trim() == "Success"){
						$("#div_"+bno).fadeOut(500);
					} else {
						alert(data);
					}
				})
				.catch(err => console.error("Fetch error in deletebce:", err));
		}
	}
</script>