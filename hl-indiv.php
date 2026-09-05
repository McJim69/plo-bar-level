<?php
	require("connect.php");
	require("head.php");

	// Safe parameter handling
	$t_search = isset($_POST["t_search"]) ? $_POST["t_search"] : "";
	$value = strtoupper($t_search);
	$rep = "<b style='color:#0014d0;background:#ffa0a0'>" . htmlspecialchars($value) . "</b>";
	
	$barangay = isset($_GET["barangay"]) ? $_GET["barangay"] : "";
	$hl_val = isset($_GET["hl"]) ? $_GET["hl"] : "";
	$ato = isset($_GET["ato"]) ? $_GET["ato"] : "";
	$p = isset($_GET["page"]) ? intval($_GET["page"]) : 1;

	$bar = "BARANGAY " . $barangay;
	if (empty($barangay) || $barangay === "All barangays") {
		$bar = "ALL BARANGAYS";
	}

	$rec = 1;
	if ($p > 1) {
		$to = $rec;
		$from = ($p * $rec) - $rec;
		$i = (($p - 1) * $rec) + 1;
	} else {
		$to = $rec;
		$from = 0;
		$i = 1;
		$p = 1;
	}			
		
	$hl_clause = "";
	if (!empty($hl_val)) {
		$hl_clause = " and v.vin='" . $hl_val . "' ";	
	}
	
	$filter = "";
	if (!empty($barangay) && $barangay !== "All barangays") {
		$filter = " and v.barangay='" . $barangay . "'";
	}
		
	if (isset($_POST["b_search"]) && !empty($t_search)) {
		$stmt = $link->prepare("select * from voters v, hl h where v.vname LIKE CONCAT('%', ?, '%') and h.vin=v.vin {$hl_clause} {$filter} order by v.vname");
		$stmt->execute([$t_search]);
		$ex1 = $stmt;
		
		$stmt = $link->prepare("select * from voters v, hl h where v.vname LIKE CONCAT('%', ?, '%') and h.vin=v.vin {$hl_clause} {$filter} order by v.vname limit {$from},{$to}");
		$stmt->execute([$t_search]);
		$ex2 = $stmt;
	} else {
		$stmt = $link->prepare("select * from voters v, hl h where h.vin=v.vin {$hl_clause} {$filter} order by v.vname");
		$stmt->execute([]);
		$ex1 = $stmt;
		
		$stmt = $link->prepare("select * from voters v, hl h where h.vin=v.vin {$hl_clause} {$filter} order by v.vname limit {$from},{$to}");
		$stmt->execute([]);
		$ex2 = $stmt;
	}
	
	$total_records = $ex1->rowCount();
?>

<link href="css/hlindiv.css" rel="stylesheet" type="text/css"/>

<!-- Print-specific overrides to guarantee clean paper margins and hide screen UI -->
<style>
	@media print {
		body {
			background-color: #fff !important;
			color: #000 !important;
		}
		.d-print-none {
			display: none !important;
		}
		.print-container {
			width: 100% !important;
			margin: 0 !important;
			padding: 0 !important;
			box-shadow: none !important;
			background: transparent !important;
		}
		.info, .info th, .info td {
			border: 1px solid #000 !important;
			border-collapse: collapse !important;
			color: #000 !important;
		}
	}
</style>

<script>
	setActive("sum");
	setActive("sumform");

	function printF() {
		window.print();
	}
</script>

<?php require("menu.php"); ?>	

<div class="container my-4 d-print-none">
	<!-- Search & Filters Card -->
	<form method="post" enctype="multipart/form-data" class="mb-4">
		<div class="card-glass p-3">
			<div class="row g-3 align-items-center justify-content-between">
				<div class="col-md-4">
					<div class="input-group">
						<span class="input-group-text border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
						<input type="text" name="t_search" class="form-control border-start-0 ps-2" 
							placeholder="Search for HL to Print..." value="<?php echo htmlspecialchars($t_search); ?>" />
					</div>
				</div>
				<div class="col-md-3 d-flex gap-2">
					<button type="submit" name="b_search" class="btn btn-danger flex-fill fw-bold"><i class="fa-solid fa-search"></i> Search</button>
					<button type="button" class="btn btn-dark flex-fill fw-bold" onclick="printF()"><i class="fa-solid fa-print"></i> Print</button>
				</div>
				<div class="col-md-2">
					<select class="form-select" onchange="jump('?barangay='+encodeURIComponent(this.value))">
						<option value="All barangays">All barangays</option>
						<?php
							$ex = $link->query("select barangay from voters group by barangay order by barangay");
							while($rs = $ex->fetch(PDO::FETCH_BOTH)){
								$sel = ($barangay === $rs[0]) ? "selected" : "";
								echo "<option value='".htmlspecialchars($rs[0])."' $sel>".htmlspecialchars($rs[0])."</option>";
							}
						?>
					</select>
				</div>
				<div class="col-md-3 d-flex align-items-center gap-1 justify-content-end">
					<a class="btn btn-sm btn-outline-danger fw-bold <?php if($p <= 1) echo 'disabled'; ?>" 
						href="?page=<?php echo ($p - 1); ?>&barangay=<?php echo urlencode($barangay); ?>&ato=<?php echo urlencode($ato); ?>">
						<i class="fa-solid fa-chevron-left"></i>
					</a>
					<select class="form-select form-select-sm text-center fw-bold" style="width: auto; min-width: 65px;" 
						onchange="jump('?page=' + this.value + '&barangay=<?php echo urlencode($barangay); ?>&ato=<?php echo urlencode($ato); ?>')">
						<?php
							$max_pages = ceil($total_records / $rec);
							if ($max_pages < 1) $max_pages = 1;
							for ($j = 1; $j <= $max_pages; $j++) {
								$sel = ($p === $j) ? "selected" : "";
								echo "<option value='$j' $sel>$j</option>";
							}
						?>
					</select>
					<span class="small text-muted text-nowrap">of <?php echo $max_pages; ?></span>
					<a class="btn btn-sm btn-outline-danger fw-bold <?php if($p >= $total_records) echo 'disabled'; ?>" 
						href="?page=<?php echo ($p + 1); ?>&barangay=<?php echo urlencode($barangay); ?>&ato=<?php echo urlencode($ato); ?>">
						<i class="fa-solid fa-chevron-right"></i>
					</a>
				</div>
			</div>
		</div>
	</form>
</div>

<!-- Receipt Presentation Container -->
<div class="container my-3 print-container" style="max-width: 1000px; background: #fff; border-radius: var(--radius-md); box-shadow: var(--shadow-md); padding: 30px;">
	<?php
		while($rs = $ex2->fetch(PDO::FETCH_BOTH)){
			$precinct = $rs["precinct"];
			$name = $rs["vname"];
			$sex = $rs["sex"];
			
			$stmt = $link->prepare('select * from voters v where v.vin=?');
			$stmt->execute([$rs["plvin"]]);
			$ppp = $stmt;
			$rspl = $ppp->fetch(PDO::FETCH_BOTH);
			$pl_name = $rspl ? $rspl["vname"] : "";
	?>
			<div id="div_<?php echo htmlspecialchars($rs["vin"]); ?>" class="p-3">
				<!-- Campaign Header Logo -->
				<div class="text-center mb-4">
					<img src="images/foot1.png" style="height: 70px;" alt="Campaign Banner" />
				</div>
				
				<!-- Receipt Identification Header -->
				<div class="text-center mb-4">
					<h3 class="fw-bold mb-1" style="font-size: 26px; letter-spacing: 1px;">ACKNOWLEDGEMENT RECEIPT</h3>
					<h5 class="fw-bold mb-0 text-uppercase" style="font-size: 19px;">BARANGAY <?php echo htmlspecialchars($rs["barangay"]); ?></h5>
					<span class="text-muted fw-bold text-uppercase" style="font-size: 14px;">CITY OF <?php echo htmlspecialchars($_SESSION["city_mun"]); ?></span>
				</div>

				<div class="row align-items-center justify-content-between mb-3 g-2">
					<div class="col-auto">
						<span class="badge bg-danger text-white px-3 py-2 fs-6 fw-bold">HL ID: <?php printf("%04d", $rs["vin"]); ?></span>
					</div>
					<div class="col-auto text-end">
						<span class="badge bg-danger text-white px-3 py-2 fs-6 fw-bold">First Release</span>
					</div>
				</div>
				
				<?php
					$birthDate = $rs["birth"];
					$age = "-";
					if (!empty($birthDate) && $birthDate !== '0000-00-00') {
						$birthObj = date_create($birthDate);
						if ($birthObj !== false) {
							$age = date_diff($birthObj, date_create('today'))->y;
						}
					}

					$stmt = $link->prepare("SELECT cluster FROM clusters WHERE precinct LIKE CONCAT('%', ?, '%')");
					$stmt->execute([$precinct]);
					$cluster = $stmt;
					$rsc = $cluster->fetch(PDO::FETCH_BOTH);
					$hl_cluster = $rsc ? $rsc[0] : "";
				?>
				
				<div style="min-height: 520px;border:1px solid #000;border-radius:10px">
					<!-- Acknowledgement Details Table -->
					<table class="table table-bordered align-middle info mb-0" id="info">
						<thead class="table-light">
							<tr class="align-middle text-center fw-bold" style="background:#bbb; font-weight:bold;">
								<th style="padding: 10px 5px;">#</th>
								<th style="width: 70px;">PHOTO</th>
								<th class="text-start ps-3">NAME OF VOTERS</th>
								<th>SEX</th>
								<th>AGE</th>
								<th>PREC</th>
								<th>CLUS</th>
								<th style="width: 200px;">SIGNATURE</th>
							</tr>
						</thead>
						<tbody>
							<!-- Household Leader Row -->
							<tr class="fw-bold align-middle text-center">
								<td>HL</td>
								<td>
									<?php if (file_exists("images/voters/" . $rs["vin"] . ".jpg")): ?>
										<img src="images/voters/<?php echo $rs["vin"]; ?>.jpg" height="55" width="55" class="rounded border" />
									<?php else: ?>
										<img src="images/blank.jpg" height="55" width="55" class="rounded border" />
									<?php endif; ?>
								</td>
								<td class="text-start ps-3">
									<?php 
										if (!empty($t_search)) {
											echo str_replace($value, $rep, $name);
										} else {
											echo htmlspecialchars($name);
										}
									?>
								</td>
								<td><?php echo htmlspecialchars($sex); ?></td>
								<td><?php echo $age; ?></td>
								<td><?php echo htmlspecialchars($precinct); ?></td>
								<td><?php echo htmlspecialchars($hl_cluster); ?></td>
								<td></td>
							</tr>

							<!-- Household Members Rows -->
							<?php
								$ii = 1;
								$stmt = $link->prepare('select * from hl_children hl, voters v where hl.hlvin=? and hl.vin=v.vin');
								$stmt->execute([$rs["vin"]]);
								$exhlc = $stmt;
								
								while($rshlc = $exhlc->fetch(PDO::FETCH_BOTH)){
									$hmname = $rshlc["vname"];
									$hmsex = $rshlc["sex"];
									$hmprec = $rshlc["precinct"];
									
									$stmt = $link->prepare("select cluster from clusters where precinct LIKE CONCAT('%', ?, '%')");
									$stmt->execute([$hmprec]);
									$cluster2 = $stmt;
									$rsc2 = $cluster2->fetch(PDO::FETCH_BOTH);
									$hm_cluster = $rsc2 ? $rsc2[0] : "";
											
									$hmBirthDate = $rshlc["birth"];
									$hmAge = "-";
									if (!empty($hmBirthDate) && $hmBirthDate !== '0000-00-00') {
										$hmBirthObj = date_create($hmBirthDate);
										if ($hmBirthObj !== false) {
											$hmAge = date_diff($hmBirthObj, date_create('today'))->y;
										}
									}
										
									$clusterColor = ($hl_cluster !== $hm_cluster) ? "color: red;" : "color: #000;";
							?>
									<tr class="fw-bold align-middle text-center">
										<td><?php echo $ii; ?>.</td>
										<td>
											<?php if (file_exists("images/voters/" . $rshlc["vin"] . ".jpg")): ?>
												<img src="images/voters/<?php echo $rshlc["vin"]; ?>.jpg" height="55" class="rounded border" />
											<?php else: ?>
												<img src="images/blank.jpg" height="55" class="rounded border" />
											<?php endif; ?>
										</td>
										<td class="text-start ps-3"><?php echo htmlspecialchars($hmname); ?></td>
										<td><?php echo htmlspecialchars($hmsex); ?></td>
										<td><?php echo $hmAge; ?></td>
										<td><?php echo htmlspecialchars($hmprec); ?></td>
										<td style="<?php echo $clusterColor; ?>"><?php echo htmlspecialchars($hm_cluster); ?></td>
										<td></td>
									</tr>
							<?php
									$ii++;
								}
							?>
						</tbody>
					</table>
				</div>

				<!-- Verification Signatures Block -->
				<div class="row text-center mt-5 mb-4 g-4" style="font-size: 13px;">
					<div class="col-md-4">
						<div class="fw-bold mb-1"><?php echo htmlspecialchars($pl_name); ?></div>
						<div class="border-top border-dark pt-1 mx-auto" style="max-width: 220px;">Precinct Leader</div>
					</div>
					<div class="col-md-4">
						<div class="fw-bold mb-1">&nbsp;</div>
						<div class="border-top border-dark pt-1 mx-auto" style="max-width: 220px;">Barangay Kagawad</div>
					</div>
					<div class="col-md-4">
						<div class="fw-bold mb-1">WILLIAM ABASOLO LARUBIS, SR.</div>
						<div class="border-top border-dark pt-1 mx-auto" style="max-width: 220px;">Barangay Chairman</div>
					</div>
				</div>
				<!-- Office Signatures Block -->
				<div class="row text-center mt-5 g-4" style="font-size: 13px;">
					<div class="col-md-4" style="margin-top:-60px">
						<div class="mb-1"><img src="images/no_signature.png" height="55" alt="Signature" /></div>
						<div class="fw-bold mb-1">AILYN O. SULAD</div>
						<div class="border-top border-dark pt-1 mx-auto" style="max-width: 220px;">Assistant Supervising Facilitator</div>
					</div>
					<div class="col-md-4 d-flex align-items-center justify-content-center" style="margin-top:-10px">
						<div class="border border-secondary border-dashed p-4 text-muted" style="width: 200px; border-style: dotted !important;">
							PAID STAMP
						</div>
					</div>
					<div class="col-md-4" style="margin-top:-60px">
						<div class="mb-1"><img src="images/no_signature.png" height="55" alt="Signature" /></div>
						<div class="fw-bold mb-1">&nbsp;</div>
						<div class="border-top border-dark pt-1 mx-auto" style="max-width: 220px;">Cashier</div>
					</div>
				</div>
			</div>
		<?php } ?>

</div>

</body>

</html>
