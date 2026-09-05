<?php
	require("connect.php");
	require("head.php");
	
	$stmt = $link->prepare('select * from voters where vin=?');
	$stmt->execute([$_GET["pl"]]);
	$ex = $stmt;
	$rsmce=$ex->fetch(PDO::FETCH_BOTH);
	
	$stmt = $link->prepare('select count(*) from hl where plvin=?');
	$stmt->execute([$_GET["pl"]]);
	$ex = $stmt;
	$rspl=$ex->fetch(PDO::FETCH_BOTH);
	$hl=$rspl[0];
	
	$pl="'".$_GET["pl"]."'";

	$stmt = $link->prepare("select cluster from clusters where precinct LIKE CONCAT('%', ?, '%')");
	$stmt->execute([$rsmce["precinct"]]);
	$cluster = $stmt;
	$rsc=$cluster->fetch(PDO::FETCH_BOTH);
	$cluster_num = $rsc ? $rsc[0] : "-";
?>

<div id="printA" class="container d-none" style="max-width: 1000px;">
	<div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
		<div class="d-flex gap-3 align-items-center">
			<?php
				if(file_exists("images/voters/".$_GET["pl"].".jpg")){
					echo "<img src='images/voters/".$_GET["pl"].".jpg' height='100' style='border-radius:6px;' />"; 
				}else{
					echo "<img src='images/blank.jpg' height='100' style='border-radius:6px;' />"; 
				}
			?>
			<div>
				<h2 style="font-weight: 700;"><?php echo $rsmce["vname"]; ?></h2>
				<h4 class="text-muted">Precinct Leader (PL)</h4>
				<p class="mb-0 text-muted">Precinct: <?php echo $rsmce["precinct"]; ?> | Cluster: <?php echo $cluster_num; ?></p>
			</div>
		</div>
		<div>
			<h5>HOUSEHOLD COMPOSITION</h5>
			<p class="mb-0 text-muted">No. of Household Leaders: <strong class="text-primary"><?php echo $hl; ?></strong></p>
		</div>
	</div>
	
	<?php
		$stmt = $link->prepare('select * from hl h, voters v where h.plvin=? and h.vin=v.vin order by vname');
		$stmt->execute([$_GET["pl"]]);
		$ex1 = $stmt;
		$i=1;
		while($rs3=$ex1->fetch(PDO::FETCH_BOTH)){
			$stmt = $link->prepare("select cluster from clusters where precinct LIKE CONCAT('%', ?, '%')");
			$stmt->execute([$rs3["precinct"]]);
			$rsc1=$stmt->fetch(PDO::FETCH_BOTH);
			$hl_cluster = $rsc1 ? $rsc1[0] : "-";
			
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
			
			echo "
			<div class='mb-4'>
				<table class='table table-bordered table-sm mb-0'>
					<thead class='table-dark'>
						<tr>
							<th>NO.</th>
							<th>NAME OF LEADER AND MEMBERS</th>
							<th>SEX</th>
							<th>AGE</th>
							<th>PREC</th>
							<th>CLUS</th>
							<th>PUROK</th>
							<th>REMARKS</th>
						</tr>
					</thead>
					<tbody>
						<tr style='font-weight:bold; background-color: #f1f5f9;'>
							<td>HL".$i.".</td>
							<td>".$rs3["vname"]."</td>
							<td>".$rs3["sex"]."</td>
							<td>".$age."</td>
							<td>".$rs3["precinct"]."</td>
							<td>".$hl_cluster."</td>
							<td>".$rs3["address"]."</td>
							<td></td>
						</tr>";
						
						$stmt = $link->prepare('select * from hl_children hc, voters v where hc.hlvin=? and hc.vin=v.vin order by v.vname');
						$stmt->execute([$rs3["vin"]]);
						$ex_m = $stmt;
						$iii=1;
						while($rs_m=$ex_m->fetch(PDO::FETCH_BOTH)){
							$stmt = $link->prepare("select cluster from clusters where precinct LIKE CONCAT('%', ?, '%')");
							$stmt->execute([$rs_m["precinct"]]);
							$rsc2=$stmt->fetch(PDO::FETCH_BOTH);
							$hm_cluster = $rsc2 ? $rsc2[0] : "-";
							
							$color = ($hl_cluster != $hm_cluster) ? "color:red;" : "";
							
							$birthDate = $rs_m["birth"];
							$birthDate = explode("-", $birthDate);
							$age_m = "-";
							if(count($birthDate) === 3) {
								$age_temp_date = $birthDate[0] . '-' . $birthDate[1] . '-' . $birthDate[2];
							$age_m = "-";
								if ($birthDate[0] !== '0000' && !empty($birthDate[0])) {
									$birthObj = date_create($age_temp_date);
									if ($birthObj !== false) {
										$age_m = date_diff($birthObj, date_create('today'))->y;
									}
								}
							}
							echo "<tr>
								<td>".$i.".".$iii."</td>
								<td>".$rs_m["vname"]."</td>
								<td>".$rs_m["sex"]."</td>
								<td>".$age_m."</td>
								<td>".$rs_m["precinct"]."</td>
								<td style='$color'>".$hm_cluster."</td>
								<td>".$rs_m["address"]."</td>
								<td>".$rs_m[3]."</td>
							</tr>";
							$iii++;
						}
					echo "
					</tbody>
				</table>
			</div>";
			$i++;
		}
	?>
</div>

<?php 
	require("menu.php"); 
	require("popvoters.php"); 	
?>
<script>setActive("pl");</script>

<div id="bottom">	
	<div class="container my-4">
		<!-- PL Glass Card -->
		<div class="card-glass mb-4 text-white" style="background: linear-gradient(135deg, #991b1b 0%, #7f1d1d 100%); border: none;">
			<div class="row align-items-center g-4">
				<div class="col-md-3 text-center">
					<?php
						if(file_exists("images/voters/".$_GET["pl"].".jpg")){
							echo "<img style='border: 3px solid rgba(255,255,255,0.3); border-radius: var(--radius-md); max-width: 100%; max-height: 220px; object-fit: cover;' src='images/voters/".$_GET["pl"].".jpg?".date("h:i:s")."' />"; 
						}else{
							echo "<img style='border: 3px solid rgba(255,255,255,0.3); border-radius: var(--radius-md); max-width: 100%; max-height: 220px; object-fit: cover;' src='images/blank.jpg' />"; 
						}
					?>
				</div>
				<div class="col-md-9">
					<h2 class="text-uppercase mb-1" style="font-weight: 700; color: #ffffff;"><?php echo $rsmce["vname"]; ?></h2>
					<h5 style="color: rgba(255,255,255,0.8); font-weight: 500;">PRECINCT LEADER (PL)</h5>
					<hr style="border-color: rgba(255,255,255,0.2);">
					<div class="row g-2 mb-3" style="font-size: 14px;">
						<div class="col-sm-6">ID No: <strong class="text-warning"><?php printf("%04d", $rsmce["vin"]); ?></strong></div>
						<div class="col-sm-6">Precinct: <strong class="text-warning"><?php echo $rsmce["precinct"]; ?></strong> &nbsp; Cluster: <strong class="text-warning"><?php echo $cluster_num; ?></strong></div>
						<div class="col-sm-6">Address: <strong><?php echo $rsmce["address"] . ", " . $rsmce["barangay"]; ?></strong></div>
						<div class="col-sm-6">Total Household Leaders: <strong class="text-warning"><?php echo $hl; ?></strong></div>
					</div>
					
					<div id="d_controls" class="d-flex gap-2 flex-wrap">
						<button class="btn btn-success btn-sm" onclick="add()"><i class="fa-solid fa-user-plus"></i> Add Household Leader</button>
						<button class="btn btn-light btn-sm text-danger" onclick="printA()"><i class="fa-solid fa-print"></i> Print</button>
						<button class="btn btn-outline-light btn-sm" onclick="jump('pllist.php?barangay=SAN%20PEDRO')"><i class="fa-solid fa-list"></i> PL List</button>
					</div>
					<script>
						function printA(){
							$('#d_controls').addClass('d-none');
							$('#bottom').addClass('d-none');
							$('#cssmenu-wrapper').addClass('d-none');
							$('#printA').removeClass('d-none');
							window.print();
							$('#printA').addClass('d-none');
							$('#bottom').removeClass('d-none');
							$('#d_controls').removeClass('d-none');
							$('#cssmenu-wrapper').removeClass('d-none');
						}
					</script>
				</div>
			</div>
		</div>

		<!-- Title Section -->
		<div class="mb-4">
			<h4 style="font-weight: 700; color: var(--text-main);">
				Household Leaders (HL) belonged to <span class="text-danger"><?php echo htmlspecialchars($rsmce["vname"]); ?></span>:
			</h4>
		</div>

		<!-- Cards Grid Container -->
		<form method="post" enctype="multipart/form-data" class="m-0">
			<div id="thumbnails" class="tomb-grid">
				<?php
					$stmt = $link->prepare('select * from hl h, voters v where h.plvin=? and h.vin=v.vin order by vname');
					$stmt->execute([$_GET["pl"]]);
					$ex = $stmt;

					$i=1;
					$value = isset($_POST["t_search"]) ? strtoupper($_POST["t_search"]) : "";
					$rep = "<b style='color:#0014d0;background:#ffa0a0'>".$value."</b>";

					while($rs=$ex->fetch(PDO::FETCH_BOTH)){
						$stmt = $link->prepare("select cluster from clusters where precinct LIKE CONCAT('%', ?, '%')");
						$stmt->execute([$rs["precinct"]]);
						$rsc1=$stmt->fetch(PDO::FETCH_BOTH);
						$pl_cluster = $rsc1 ? $rsc1[0] : "-";

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

						$stmt = $link->prepare('select count(*) from hl_children where hlvin=?');
						$stmt->execute([$rs["vin"]]);
						$exx = $stmt;
						$rspl=$exx->fetch(PDO::FETCH_BOTH);
						$hlm=$rspl[0];
										
						if(isset($_POST["b_remove_".$rs["0"]])){
							$stmt = $link->prepare('delete from mce where vin=?');
							$stmt->execute([$rs["0"]]);
							jump("mcelist.php");
						}
						if(isset($_POST["b_upImg_".$rs["0"]])){
							move_uploaded_file($_FILES["b_file_".$rs["0"]]["tmp_name"], "images/voters/".$rs[2].".jpg");
							jump("");
						}

						$img_src = file_exists("images/voters/".$rs[2].".jpg") ? "images/voters/".$rs[2].".jpg" : "images/blank.jpg";
						
						echo "
						<div class='tomb card-glass' id='div_".$rs["vin"]."' 
							onmouseout=\"getID('div_controls_".$rs["vin"]."').style.visibility='hidden';\" 
							onmousemove=\"getID('div_controls_".$rs["vin"]."').style.visibility='visible';\">
							
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
								<h5 style='font-size: 15px; font-weight: 700; margin-bottom: 2px; text-transform: uppercase;'>".str_ireplace($value, $rep, $rs["vname"])."</h5>
								<p class='text-muted small mb-2' style='font-size: 12px;'><i class='fa-solid fa-id-card'></i> ID: " . sprintf("%04d", $rs["vin"]) . "</p>
							</div>
							
							<div class='small text-muted space-y-1' style='font-size: 13px;'>
								<div>Sex: <strong>" . ($rs["sex"] == "M" ? "Male" : "Female") . "</strong></div>
								<div>Age: <strong>$age y.o.</strong></div>
								<div>Precinct: <strong>".str_ireplace($value, $rep, $rs["precinct"])."</strong> &nbsp; Cluster: <strong>$pl_cluster</strong></div>
								<div>Address: <strong>".str_ireplace($value, $rep, $rs["address"])."</strong></div>
								<div class='text-success mt-2' style='font-weight: 600;'><i class='fa-solid fa-users'></i> Total Members: $hlm</div>
							</div>
							
							<div class='mt-3 d-flex gap-2' style='visibility: hidden; transition: var(--transition);' id='div_controls_".$rs["vin"]."'>
								<button onclick=\"deletehl('".$rs["vin"]."',".$i.")\" class='btn btn-sm btn-danger px-3'><i class='fa-solid fa-trash-can'></i> Remove</button>
								<button onclick=\"jump('hlinfo.php?hl=".$rs["vin"]."')\" type='button' class='btn btn-sm btn-dark px-3'><i class='fa-solid fa-eye'></i> HL Members</button>
							</div>
						</div>";
						$i++;
					}
					if(isset($_GET["add"]) && $_GET["add"] == "auto"){
						echo "<script>$('#bAddHL').click()</script>";
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
		var url = "ajax/addhl.php?plvin="+<?php echo $pl; ?>+"&vin="+vin;
		fetch(url)
			.then(response => response.text())
			.then(data => {
				if(data.trim() == "Success"){
					$("#q_tr_"+row).animate({
						opacity:0
					},500,function(){
						$("#q_tr_"+row).css("display","none");
					});
					window.location="hlinfo.php?hl="+vin;
				} else {
					console.error("addhl returned:", data);
				}
			})
			.catch(err => console.error("Fetch error in addmce:", err));
	}
			
	function deletehl(hlno,id){	
		if(confirm("Are you Sure?")){
			var url = "ajax/deletehl.php?vin="+hlno;
			fetch(url)
				.then(response => response.text())
				.then(data => {
					if(data.trim() == "Success"){
						$("#div_"+id).fadeOut(500);
					} else {
						console.error("deletehl returned:", data);
					}
				})
				.catch(err => console.error("Fetch error in deletehl:", err));
		}
	}
</script>