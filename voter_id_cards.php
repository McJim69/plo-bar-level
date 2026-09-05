<?php
	require("connect.php");
	require("head.php");

	// Safe parameter handling
	$t_search = isset($_POST["t_search"]) ? $_POST["t_search"] : "";
	$barangay = isset($_GET["barangay"]) ? $_GET["barangay"] : "";
	$p = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
	$rec = 10; // 10 cards per page (fits nicely in 2x5 grid on A4 print layouts)
	if($p < 1) $p = 1;
	$from = ($p - 1) * $rec;

	$filter = "";
	if (!empty($barangay) && $barangay !== "All barangays") {
		$filter = " barangay = " . $link->quote($barangay) . " AND ";
	}

	$search_cond = "";
	if (!empty($t_search)) {
		$search_cond = " (vname LIKE " . $link->quote("%".$t_search."%") . " OR precinct LIKE " . $link->quote("%".$t_search."%") . ") AND ";
	}

	// Count total voters matching criteria
	$count_query = "SELECT count(*) FROM voters WHERE {$filter} {$search_cond} ato = 'Sure' AND city_mun = ?";
	$stmt = $link->prepare($count_query);
	$stmt->execute([$_SESSION["city_mun"]]);
	$total_rows = $stmt->fetchColumn();
	$total_pages = ceil($total_rows / $rec);
	if ($total_pages < 1) $total_pages = 1;

	// Query paginated voters
	$data_query = "SELECT * FROM voters WHERE {$filter} {$search_cond} ato = 'Sure' AND city_mun = ? ORDER BY vname LIMIT $from, $rec";
	$stmt = $link->prepare($data_query);
	$stmt->execute([$_SESSION["city_mun"]]);
	$voters = $stmt->fetchAll(PDO::FETCH_ASSOC);

	require("menu.php");
?>

<style>
	.id-card-grid {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
		gap: 20px;
		margin-bottom: 30px;
	}
	.id-card-print-wrapper {
		display: flex;
		justify-content: center;
	}
	.id-card {
		width: 85.6mm;
		height: 54mm;
		background: url(images/id-bg.webp)no-repeat;
		background-size:cover;
		background-position:center center;
		border: 1px solid rgba(255, 255, 255, 0.1);
		border-radius: 12px;
		box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
		position: relative;
		overflow: hidden;
		padding: 10px 12px;
		color: #ffffff;
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		font-family: 'Roboto', sans-serif;
		box-sizing: border-box;
	}
	/* Red theme accent bar at the top */
	.id-card::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 4px;
		background: #dc2626;
	}
	.id-card-header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		border-bottom: 1px solid rgba(255, 255, 255, 0.15);
		padding-bottom: 4px;
		margin-bottom: 6px;
	}
	.id-card-logo-txt {
		font-size: 11px;
		font-weight: 700;
		color: #ffffff;
		display: flex;
		align-items: center;
		gap: 4px;
	}
	.id-card-logo-txt i {
		color: #ef4444;
	}
	.id-card-title {
		font-size: 8px;
		font-weight: 700;
		letter-spacing: 0.5px;
		text-transform: uppercase;
		color: #94a3b8;
	}
	.id-card-body {
		display: flex;
		flex: 1;
		gap: 12px;
		align-items: center;
	}
	.id-card-photo-wrapper {
		position: relative;
		width: 90px;
		height: 90px;
		border: 1.5px solid #dc2626;
		border-radius: 6px;
		overflow: hidden;
		background: #ffffff;
		flex-shrink: 0;
	}
	.id-card-photo {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}
	.id-card-info {
		flex: 1;
		display: flex;
		flex-direction: column;
		gap: 1px;
		min-width: 0;
	}
	.id-card-name {
		font-size: 12px;
		font-weight: 700;
		text-transform: uppercase;
		color: #ffffff;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		margin-bottom: 2px;
	}
	.id-card-role {
		font-size: 8px;
		font-weight: 700;
		color: #f87171; /* Vibrant light red role title */
		text-transform: uppercase;
		letter-spacing: 0.3px;
		margin-top: -2px;
		margin-bottom: 3px;
	}
	.id-card-meta {
		font-size: 8px;
		color: #cbd5e1;
		line-height: 1.3;
	}
	.id-card-meta strong {
		color: #ffffff;
	}
	.id-card-footer {
		display: flex;
		align-items: center;
		justify-content: space-between;
		border-top: 1px solid rgba(255, 255, 255, 0.1);
		padding-top: 4px;
		margin-top: 4px;
		font-size: 8px;
	}
	.id-card-barcode-placeholder {
		width: 90px;
		height: 14px;
		background: repeating-linear-gradient(90deg, #ffffff, #ffffff 1.5px, #0f172a 1.5px, #0f172a 3px);
		border: 1px solid rgba(255,255,255,0.4);
		border-radius: 1px;
	}
	.id-card-badge {
		background: #dc2626;
		color: #ffffff;
		padding: 1px 5px;
		border-radius: 3px;
		font-weight: 700;
		font-size: 7.5px;
		text-transform: uppercase;
	}

	@media print {
		body {
			background: #ffffff !important;
			color: #000000 !important;
			margin: 0 !important;
			padding: 0 !important;
		}
		#cssmenu-wrapper,
		#searchForm,
		.d-print-none,
		.btn,
		footer,
		.header-right-controls {
			display: none !important;
		}
		.id-card-grid {
			display: grid !important;
			grid-template-columns: 1fr 1fr !important; /* Force exactly two columns */
			gap: 4mm !important; /* Perfect A4 row/column spacing gaps */
			margin: 0 !important;
			padding: 0 !important;
			width: 100% !important;
		}
		.id-card-print-wrapper {
			display: block !important;
			margin: 0 !important;
			padding: 0 !important;
			page-break-inside: avoid !important;
		}
		.id-card {
			box-shadow: none !important;
			border: 1px solid #94a3b8 !important;
			background: #D3CCE3;  /* fallback for old browsers */
			background: -webkit-linear-gradient(to right, #E9E4F0, #D3CCE3);  /* Chrome 10-25, Safari 5.1-6 */
			background: linear-gradient(to right, #E9E4F0, #D3CCE3); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
			color: #0f172a !important; /* Dark text for print */
			-webkit-print-color-adjust: exact !important;
			print-color-adjust: exact !important;
		}
		.id-card-logo-txt {
			color: #0f172a !important;
		}
		.id-card-logo-txt span {
			color: #0f172a !important;
		}
		.id-card-title {
			color: #475569 !important;
		}
		.id-card-name {
			color: #000000 !important; /* Extremely bold/vivid name */
		}
		.id-card-role {
			color: #b91c1c !important; /* Vivid red role title */
		}
		.id-card-meta {
			color: #334155 !important;
		}
		.id-card-meta strong {
			color: #000000 !important; /* Vivid black text for meta details */
		}
		.id-card-footer {
			border-top: 1px solid #cbd5e1 !important;
		}
		.id-card-barcode-placeholder {
			border: 1px solid #000000 !important;
			background: repeating-linear-gradient(90deg, #000000, #000000 1.5px, #ffffff 1.5px, #ffffff 3px) !important; /* High contrast barcode for light background */
		}
	}
</style>

<div class="container my-4">
	<!-- Search & Filters Card -->
	<form method="post" enctype="multipart/form-data" class="mb-4 d-print-none" id="searchForm">
		<div class="card-glass p-3">
			<div class="row g-3 align-items-center">
				<div class="col-md-3">
					<div class="input-group">
						<span class="input-group-text border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
						<input type="text" name="t_search" id="t_search" class="form-control border-start-0 ps-2" 
							placeholder="Type a keyword..." value="<?php echo htmlspecialchars($t_search); ?>" />
					</div>
				</div>
				<div class="col-md-5 d-flex gap-2">
					<button type="submit" name="b_search" class="btn btn-danger flex-fill fw-bold"><i class="fa-solid fa-search"></i> Search</button>
					<button type="button" class="btn btn-secondary flex-fill fw-bold" onclick="window.print()"><i class="fa-solid fa-print"></i> Print ID Cards</button>
					<button type="button" class="btn btn-success flex-fill fw-bold" onclick="jump('voterslist.php')"><i class="fa-solid fa-users"></i> Voters List</button>
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
				<div class="col-md-2">
					<select class="form-select" onchange="jump('?page='+this.value+'&barangay=<?php echo isset($_GET["barangay"]) ? urlencode($_GET["barangay"]) : ''; ?>')">
						<?php
							for($j=1; $j<=$total_pages; $j++){
								$sel = ($p == $j) ? "selected" : "";
								echo "<option value='$j' $sel>Page $j</option>";
							}
						?>
					</select>				
				</div>
			</div>
		</div>
	</form>
	<!-- ID Cards Grid -->
	<div class="id-card-grid">
		<?php
			if (count($voters) > 0) {
				foreach ($voters as $v) {
					// Fetch voter image
					$img_src = file_exists("images/voters/".$v["vin"].".jpg") ? "images/voters/".$v["vin"].".jpg" : "images/blank.jpg";
					
					// Fetch turnout cluster
					$stmt = $link->prepare("select cluster from clusters where precinct LIKE CONCAT('%', ?, '%')");
					$stmt->execute([$v["precinct"]]);
					$c_row = $stmt->fetch(PDO::FETCH_BOTH);
					$v_cluster = $c_row ? $c_row[0] : "-";

					// Fetch role title
					$stmt = $link->prepare("
						SELECT 
							(SELECT COUNT(*) FROM mce WHERE vin = ?) as is_mce,
							(SELECT COUNT(*) FROM bce WHERE vin = ?) as is_bce,
							(SELECT COUNT(*) FROM pl WHERE vin = ?) as is_pl,
							(SELECT COUNT(*) FROM hl WHERE vin = ?) as is_hl,
							(SELECT COUNT(*) FROM hl_children WHERE vin = ?) as is_hlc
					");
					$stmt->execute([$v["vin"], $v["vin"], $v["vin"], $v["vin"], $v["vin"]]);
					$roles = $stmt->fetch(PDO::FETCH_ASSOC);

					$role_title = "Voter";
					if ($roles["is_mce"] > 0) {
						$role_title = "Chairman";
					} elseif ($roles["is_bce"] > 0) {
						$role_title = "Kagawad";
					} elseif ($roles["is_pl"] > 0) {
						$role_title = "Precinct Leader";
					} elseif ($roles["is_hl"] > 0) {
						$role_title = "Household Leader";
					} elseif ($roles["is_hlc"] > 0) {
						$role_title = "HH Member";
					}
		?>
					<div class="id-card-print-wrapper">
						<div class="id-card">
							<div class="id-card-header">
								<div class="id-card-logo-txt">
									<i class="fa-solid fa-users-rectangle"></i> VICTORY <span>MOVEMENT</span>
								</div>
								<div class="id-card-title">Member Card</div>
							</div>
							
							<div class="id-card-body">
								<div class="id-card-photo-wrapper">
									<img src="<?php echo $img_src; ?>" class="id-card-photo" alt="Voter Photo" />
								</div>
								
								<div class="id-card-info">
									<div class="id-card-name"><?php echo htmlspecialchars($v["vname"]); ?></div>
									<div class="id-card-role"><?php echo htmlspecialchars($role_title); ?></div>
									<div class="id-card-meta">
										ID No: <strong><?php printf("%04d", $v["vin"]); ?></strong><br>
										Precinct: <strong><?php echo htmlspecialchars($v["precinct"]); ?></strong><br> 
										Cluster Number: <strong><?php echo htmlspecialchars($v_cluster); ?></strong><br>
										Barangay: <strong><?php echo htmlspecialchars($v["barangay"]); ?></strong><br>
										Municipality: <strong><?php echo htmlspecialchars($v["city_mun"]); ?></strong>
									</div>
								</div>
							</div>
							
							<div class="id-card-footer">
								<div class="id-card-barcode-placeholder"></div>
								<span class="id-card-badge"><?php echo htmlspecialchars($v["ato"] ?: "Voter"); ?></span>
							</div>
						</div>
					</div>
		<?php
				}
			} else {
				echo "<div class='alert alert-warning w-100 text-center d-print-none'>No voter records found matching parameters.</div>";
			}
		?>
	</div>


</div>

</body>

</html>
