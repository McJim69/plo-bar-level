<?php
	require("connect.php");
	require("head.php");
	
	$value = isset($_GET['value']) ? $_GET['value'] : '';
	if(isset($_POST["b_search"])){
		$value = $_POST["t_search"];
	}

	$sql_cond = " where (vname like ? or remarks like ? or ato like ? or vin like ?)";
	$params = ['%' . $value . '%', '%' . $value . '%', '%' . $value . '%', '%' . $value . '%'];
	
	$mun_val = (isset($_GET["municipality"]) && $_GET["municipality"] !== '') ? $_GET["municipality"] : (isset($_SESSION["city_mun"]) ? $_SESSION["city_mun"] : '');
	if($mun_val != "All municipality" && $mun_val != "") {
		$sql_cond .= " and city_mun = ?";
		$params[] = $mun_val;
	}
	
	$bar_val = (isset($_GET["barangay"]) && $_GET["barangay"] !== '') ? $_GET["barangay"] : (isset($_SESSION["barangay"]) ? $_SESSION["barangay"] : '');
	if($bar_val != "All barangays" && $bar_val != "") {
		$sql_cond .= " and barangay = ?";
		$params[] = $bar_val;
	}
	
	$prk_val = isset($_GET["purok"]) ? $_GET["purok"] : '';
	if($prk_val != "All purok" && $prk_val != "") {
		$sql_cond .= " and address = ?";
		$params[] = $prk_val;
	}
	
	$prec_val = isset($_GET["precinct"]) ? $_GET["precinct"] : '';
	if($prec_val != "All precincts" && $prec_val != "") {
		$sql_cond .= " and precinct = ?";
		$params[] = $prec_val;
	}
	
	$ato_val = isset($_GET["ato"]) ? $_GET["ato"] : '';
	if($ato_val != "Ato o dili -All" && $ato_val != "") {
		$sql_cond .= " and ato = ?";
		$params[] = $ato_val;
	}

	// Count total records
	$stmt = $link->prepare("select count(*) from voters" . $sql_cond);
	$stmt->execute($params);
	$total_records = (int)$stmt->fetchColumn();

	$rec = 200;
	$p = isset($_GET['page']) ? intval($_GET['page']) : 1;
	if($p < 1) $p = 1;
	$total_pages = ceil($total_records / $rec);
	if($total_pages < 1) $total_pages = 1;
	if($p > $total_pages) $p = $total_pages;
	$from = ($p - 1) * $rec;
	
	// Fetch paginated records safely using integer casting
	$stmt = $link->prepare("select * from voters" . $sql_cond . " order by vname LIMIT $from, $rec");
	$stmt->execute($params);
	$ex = $stmt;

	// Helper function to render status with a beautiful color-coded dot indicator
	function getStatusIndicator($status) {
		if ($status == "" || $status == "0" || $status == "") {
			return "";
		}
		
		$dot_class = "";
		$display_text = htmlspecialchars($status);
		
		if ($status == "Sure-OT") {
			$dot_class = "green";
		} else if ($status == "Undecided") {
			$dot_class = "orange";
		} else if ($status == "Dili Ato" || $status == "Dili-Ato") {
			$dot_class = "red";
			$display_text = "Dili-Ato";
		} else if (stripos($status, "Sure") !== false) {
			$dot_class = "blue";
		} else {
			return $display_text;
		}
		
		return "<span class='status-indicator' title='$display_text'><span class='status-dot $dot_class'></span></span>";
	}
?>

<style>
	/* Custom styling for pagination and print spacing */
	.pagination-wrapper {
		display: flex;
		align-items: center;
		justify-content: space-between;
		flex-wrap: wrap;
		gap: 12px;
		margin-top: 16px;
	}
	
	.page-btn {
		background: var(--input-bg) !important;
		border: 1px solid var(--card-border) !important;
		padding: 6px 12px;
		border-radius: var(--radius-sm);
		cursor: pointer;
		font-size: 13px;
		text-transform: capitalize;
		transition: var(--transition);
		color: var(--text-main) !important;
		text-decoration: none;
	}
	
	.page-btn:hover {
		background: var(--primary-light) !important;
		color: var(--primary) !important;
		border-color: var(--primary) !important;
	}
	
	.page-btn.disabled {
		opacity: 0.5;
		cursor: not-allowed;
	}
</style>

<script>
	function printF(){
		$('#header-print').removeClass('d-none');
		$('#trcontrols-card').addClass('d-none');
		$('#cssmenu-wrapper').addClass('d-none');
		$('.hid-column').addClass('d-none');
		window.print();
		$('#header-print').addClass('d-none');
		$('#trcontrols-card').removeClass('d-none');
		$('#cssmenu-wrapper').removeClass('d-none');
		$('.hid-column').removeClass('d-none');
	}
</script>

<?php require("menu.php"); ?>
<script>setActive("voters");</script>

<div class="container my-4">
	<!-- Responsive Control Panel Card -->
	<div class="card-glass mb-4 animate__animated animate__fadeIn" id="trcontrols-card">
		<form method="post" class="row g-3">
			<!-- Search bar -->
			<div class="col-lg-3 col-md-6">
				<!--<label class="small text-muted mb-1" style="font-weight: 500;">Search Keyword</label>-->
				<input type="text" name="t_search" id="t_search" value="<?php echo htmlspecialchars($value); ?>" placeholder="Type a keyword..." />
			</div>
			
			<!-- Buttons -->
			<div class="col-lg-3 col-md-6 d-flex align-items-end gap-2">
				<button class="btn btn-danger w-100" type="submit" name="b_search"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
				<a class="btn btn-outline-secondary w-100" href="voterslist.php?municipality=<?php echo urlencode($mun_val); ?>&barangay=<?php echo urlencode($bar_val); ?>"><i class="fa-solid fa-arrows-rotate"></i> Reset</a>
			</div>
			
			<!-- Barangay Filter -->
			<div class="col-lg-2 col-md-4">
				<!--<label class="small text-muted mb-1" style="font-weight: 500;">Barangay</label>-->
				<select onchange="if(this.value=='All barangays') jump('?municipality=<?php echo urlencode($mun_val); ?>&barangay=All+barangays'); else jump('?municipality=<?php echo urlencode($mun_val); ?>&barangay='+encodeURIComponent(this.value)+'&precinct=&ato=<?php echo urlencode($ato_val); ?>&purok=<?php echo urlencode($prk_val); ?>')">
					<option value="All barangays">All barangays</option>
					<?php
						$stmt = $link->prepare('select barangay from voters where city_mun = ? group by barangay order by barangay');
						$stmt->execute([$mun_val]);
						$ex2 = $stmt;
						while($rs2 = $ex2->fetch(PDO::FETCH_BOTH)){
							$selected = ($bar_val === $rs2[0]) ? "selected" : "";
							echo "<option $selected>".$rs2[0]."</option>";
						}
					?>
				</select>
			</div>
			
			<!-- Precinct Filter -->
			<div class="col-lg-2 col-md-4">
				<!--<label class="small text-muted mb-1" style="font-weight: 500;">Precinct</label>-->
				<select onchange="jump('?municipality=<?php echo urlencode($mun_val); ?>&barangay=<?php echo urlencode($bar_val); ?>&precinct='+encodeURIComponent(this.value)+'&ato=<?php echo urlencode($ato_val); ?>&purok=<?php echo urlencode($prk_val); ?>')">
					<option value="All precincts">All precincts</option>
					<?php
						if($bar_val == "" || $bar_val == "All barangays") {
							$ex2 = $link->query("select precinct from voters group by precinct order by precinct");
						} else {
							$stmt = $link->prepare('select precinct from voters where barangay = ? group by precinct order by precinct');
							$stmt->execute([$bar_val]);
							$ex2 = $stmt;
						}
						while($rs2 = $ex2->fetch(PDO::FETCH_BOTH)){
							$selected = ($prec_val === $rs2[0]) ? "selected" : "";
							echo "<option $selected>".$rs2[0]."</option>";
						}
					?>
				</select>
			</div>
			
			<!-- Status Filter (Ato / Dili) -->
			<div class="col-lg-2 col-md-4">
				<!--<label class="small text-muted mb-1" style="font-weight: 500;">Status</label>-->
				<select onchange="jump('?municipality=<?php echo urlencode($mun_val); ?>&barangay=<?php echo urlencode($bar_val); ?>&precinct=<?php echo urlencode($prec_val); ?>&ato='+encodeURIComponent(this.value)+'&purok=<?php echo urlencode($prk_val); ?>')">
					<option value="Ato o dili -All">Ato o dili -All</option>
					<?php
						$ex2 = $link->query("select ato from voters where ato<>'' group by ato");
						while($rs2 = $ex2->fetch(PDO::FETCH_BOTH)){
							$selected = ($ato_val === $rs2[0]) ? "selected" : "";
							echo "<option $selected>".$rs2[0]."</option>";
						}
					?>
				</select>
			</div>
		</form>
		
		<!-- Pagination & Print controls -->
		<div class="pagination-wrapper mt-3 pt-3 border-top">
			<div class="d-flex align-items-center gap-2">
				<a class="page-btn <?php if($p <= 1) echo 'disabled'; ?>" href="<?php if($p > 1) echo '?municipality='.urlencode($mun_val).'&barangay='.urlencode($bar_val).'&precinct='.urlencode($prec_val).'&ato='.urlencode($ato_val).'&page=1&value='.urlencode($value); ?>">&laquo; First</a>
				<a class="page-btn <?php if($p <= 1) echo 'disabled'; ?>" href="<?php if($p > 1) echo '?municipality='.urlencode($mun_val).'&barangay='.urlencode($bar_val).'&precinct='.urlencode($prec_val).'&ato='.urlencode($ato_val).'&page='.($p - 1).'&value='.urlencode($value); ?>">&laquo; Prev</a>
				<span class="text-muted small">Page <strong><?php echo $p; ?></strong> of <strong><?php echo $total_pages; ?></strong> (Total: <?php echo number_format($total_records); ?>)</span>
				<a class="page-btn <?php if($p >= $total_pages) echo 'disabled'; ?>" href="<?php if($p < $total_pages) echo '?municipality='.urlencode($mun_val).'&barangay='.urlencode($bar_val).'&precinct='.urlencode($prec_val).'&ato='.urlencode($ato_val).'&page='.($p + 1).'&value='.urlencode($value); ?>">Next &raquo;</a>
				<a class="page-btn <?php if($p >= $total_pages) echo 'disabled'; ?>" href="<?php if($p < $total_pages) echo '?municipality='.urlencode($mun_val).'&barangay='.urlencode($bar_val).'&precinct='.urlencode($prec_val).'&ato='.urlencode($ato_val).'&page='.$total_pages.'&value='.urlencode($value); ?>">Last &raquo;</a>
			</div>
			
			<div class="d-flex align-items-center gap-2">
				<span class="small text-muted">Go to Page:</span>
				<select style="width: auto; padding: 4px 8px; font-size: 13px;" onchange="jump('?municipality=<?php echo urlencode($mun_val); ?>&barangay=<?php echo urlencode($bar_val); ?>&precinct=<?php echo urlencode($prec_val); ?>&ato=<?php echo urlencode($ato_val); ?>&page=' + this.value + '&value=<?php echo urlencode($value); ?>')">
					<?php
						for($j = 1; $j <= $total_pages; $j++){
							$selected = ($p == $j) ? "selected" : "";
							echo "<option $selected>$j</option>";
						}
					?>
				</select>
				<button class="btn btn-sm btn-dark" onclick="printF()"><i class="fa-solid fa-print"></i> Print List</button>
			</div>
		</div>
	</div>

	<!-- Print Section Header (Hidden by default) -->
	<div id="header-print" class="d-none text-center my-4">
		<h3 class="text-uppercase mb-1" style="font-weight: 700;">List of Registered Voters</h3>
		<h4 class="mb-2">City of <?php echo $_SESSION["city_mun"]; ?></h4>
		<?php
			if($bar_val != "All barangays" && $bar_val != "") echo "<h5>BARANGAY " . htmlspecialchars($bar_val) . "</h5>";
			if($prec_val != "All precincts" && $prec_val != "") echo "<h5>PRECINCT " . htmlspecialchars($prec_val) . "</h5>";
		?>
		<hr class="my-3">
	</div>

	<!-- Responsive Table Card -->
	<div class="card-glass p-0 overflow-hidden">
		<div class="table-responsive">
			<table class="table table-hover table-striped mb-0" id="tab">
				<thead>
					<tr>
						<th>NO.</th>
						<th>NAME OF VOTERS</th>
						<th>IDN</th>
						<th>SEX</th>
						<th>AGE</th>
						<th>PRECINCT</th>
					<!--<th>CLUSTER</th>-->
						<th>PUROK</th>
						<th class="hid-column">BARANGAY</th>
						<th class="hid-column">CITY</th>
						<th class="hid-column">ATO?</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$val = strtoupper($value);
						$rep = "<b style='color:#0014d0;background:#ffa0a0'>".$value."</b>";
						$i = $from + 1;
						
						while($rs = $ex->fetch(PDO::FETCH_BOTH)){
							$precinct = $rs["precinct"];
							
							// Fetch cluster
							$stmt = $link->prepare('SELECT cluster FROM clusters WHERE precinct = ?');
							$stmt->execute([$precinct]);
							$rsc = $stmt->fetch(PDO::FETCH_BOTH);
							$cluster_val = $rsc ? sprintf("%02d", $rsc[0]) : "";
							
							// Age calculation
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
							
							// Highlight search values
							$disp_name = ($value != "") ? str_ireplace($value, $rep, $rs["vname"]) : $rs["vname"];
							$disp_sex = ($value != "") ? str_ireplace($value, $rep, $rs["sex"]) : $rs["sex"];
							$disp_precinct = ($value != "") ? str_ireplace($value, $rep, $rs["precinct"]) : $rs["precinct"];
							$disp_address = ($value != "") ? str_ireplace($value, $rep, $rs["address"]) : $rs["address"];
							$disp_barangay = ($value != "") ? str_ireplace($value, $rep, $rs["barangay"]) : $rs["barangay"];
							$disp_city_mun = ($value != "") ? str_ireplace($value, $rep, $rs["city_mun"]) : $rs["city_mun"];
							
							// Fetch relationship category status (MCE / BCE / PL / HL etc.)
							$stmt = $link->prepare('select * from mce where vin=?');
							$stmt->execute([$rs["vin"]]);
							$is_mce = $stmt->fetchColumn();
							
							$stmt = $link->prepare('select * from bce where vin=?');
							$stmt->execute([$rs["vin"]]);
							$is_bce = $stmt->fetchColumn();
							
							$stmt = $link->prepare('select * from pl where vin=?');
							$stmt->execute([$rs["vin"]]);
							$is_pl = $stmt->fetchColumn();
							
							$stmt = $link->prepare('select * from hl where vin=?');
							$stmt->execute([$rs["vin"]]);
							$is_hl = $stmt->fetchColumn();
							
							$stmt = $link->prepare('select * from users where vin=?');
							$stmt->execute([$rs["vin"]]);
							$is_users = $stmt->fetchColumn();
							
							$stmt = $link->prepare('select * from officer where vin=?');
							$stmt->execute([$rs["vin"]]);
							$is_officer = $stmt->fetchColumn();
							
							$status_text = "";
							if($is_mce) $status_text = "Sure-KAP";
							else if($is_bce) $status_text = "Sure-KAG";
							else if($is_pl) $status_text = "Sure-PL";
							else if($is_hl) $status_text = "Sure-HL";
							else if($is_users) $status_text = "Sure-IT";
							else if($is_officer) $status_text = "Sure-IO";
							else {
								if($rs["ato"] == "" || $rs["ato"] == "0") $status_text = "";
								else $status_text = $rs["ato"];
							}
							
							$status_html = getStatusIndicator($status_text);
							
							echo "
							<tr>
								<td>$i.</td>
								<td>$disp_name</td>
								<td>" . sprintf("%04d", $rs["vin"]) . "</td>
								<td>$disp_sex</td>
								<td>$age</td>
								<td>$disp_precinct</td>
								<!--<td>$cluster_val</td>-->
								<td>$disp_address</td>
								<td class='hid-column'>$disp_barangay</td>
								<td class='hid-column'>$disp_city_mun</td>
								<td class='hid-column'>$status_html</td>
							</tr>";
							$i++;
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
	include("user_profile.php");
//	require("footer.php");
?>
</body>
</html>
