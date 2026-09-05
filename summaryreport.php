<?php
	require("connect.php");
	require("head.php");
	
	$barangay_name = isset($_GET["barangay"]) ? $_GET["barangay"] : '';
	if ($barangay_name === '') {
		$stmt = $link->prepare('select barangay from voters where city_mun=? and barangay != "" group by barangay order by barangay limit 1');
		$stmt->execute([$_SESSION["city_mun"]]);
		$rs_default = $stmt->fetch(PDO::FETCH_BOTH);
		if ($rs_default) {
			$barangay_name = $rs_default[0];
		}
	}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
	/* Custom styles for printing */
	@media print {
		#trcontrols-card {
			display: none !important;
		}
		#header-print {
			display: block !important;
		}
		#cssmenu-wrapper {
			display: none !important;
		}
		body {
			background: #ffffff !important;
			color: #000000 !important;
		}
		.card-glass {
			box-shadow: none !important;
			border: none !important;
			background: none !important;
			padding: 0 !important;
		}
		#report-table-card {
			display: block !important;
		}
	}
	
	.legend-box {
		background: var(--card-bg) !important;
		border: 1px solid var(--card-border) !important;
		border-radius: var(--radius-md);
		box-shadow: var(--shadow-sm);
		padding: 16px;
		margin-top: 24px;
	}
</style>

<script>
	function printF(){				
		window.print(); 
	}

	function toggleTable() {
		var card = document.getElementById("report-table-card");
		var btn = document.getElementById("btn-toggle-table");
		if (card.style.display === "none") {
			card.style.display = "block";
			btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i> Hide Table';
			btn.className = "btn btn-outline-danger";
		} else {
			card.style.display = "none";
			btn.innerHTML = '<i class="fa-solid fa-eye"></i> Show Table';
			btn.className = "btn btn-danger";
		}
	}
</script>

<?php require("menu.php"); ?>	
<script> setActive("sum"); </script>

<div class="container my-4">
	<!-- Control Panel Card -->
	<div class="card-glass mb-5" id="trcontrols-card">
		<div class="row align-items-center">
			<div class="col-md-4 d-flex align-items-left flex-wrap gap-3 p-2">
				<h3 class="mb-0" style="font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-chart-line text-danger"></i> Summary Report</h3>
			</div>
			<div class="col-md-4 d-flex align-items-center flex-wrap gap-3 p-2">
				<select class="form-select me-2" onchange="jump('?barangay='+encodeURIComponent(this.value))">
					<?php
						$stmt = $link->prepare('select barangay from voters where city_mun=? and barangay != "" group by barangay order by barangay');
						$stmt->execute([$_SESSION["city_mun"]]);
						$ex_bar = $stmt;
						while($rs_bar=$ex_bar->fetch(PDO::FETCH_BOTH)){
							$sel = ($barangay_name === $rs_bar[0]) ? "selected" : "";
							echo "<option value='".htmlspecialchars($rs_bar[0])."' $sel>".htmlspecialchars($rs_bar[0])."</option>";
						}
					?>
				</select>
			</div>
			<div class="col-md-4 d-flex align-items-right flex-wrap gap-3 p-2">
				<button class="btn btn-outline-danger" type="button" id="btn-toggle-table" onclick="toggleTable()"><i class="fa-solid fa-eye-slash"></i> Hide Table</button>
				<a class="btn btn-outline-secondary" href="summaryreport.php?barangay=<?php echo urlencode($barangay_name); ?>"><i class="fa-solid fa-arrows-rotate"></i> Refresh</a>
				<button class="btn btn-outline-primary" onclick="printF()"><i class="fa-solid fa-print"></i> Print Report</button>
			</div>
		</div>
	</div>

	<!-- Printable Header Section (Hidden on screen) -->
	<div id="header-print" class="d-none text-center my-4">
		<div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
			<img src="images/alayon-big.png" height="70px"/>
			<div>
				<h4 style="font-weight: 700; margin-bottom: 2px;">PRECINCT LEVEL ORGANIZATION (PLO)</h4>
				<h5 class="mb-1">CITY OF <?php echo htmlspecialchars($_SESSION["city_mun"]); ?></h5>
				<?php if($barangay_name != "") echo "<h5 class='text-danger'>BARANGAY " . htmlspecialchars($barangay_name) . "</h5>"; ?>
				<p class="mb-0 text-muted small">As of <?php echo date("F d, Y"); ?></p>
			</div>
			<img src="images/iloveyudark.png" height="80px"/>
		</div>
	</div>
	<div style="margin-top:-45px"></div>
	
	<?php
		// Fetch Barangay data for all barangays in the municipality (Municipal Level Analysis)
		$bar_labels = [];
		$bar_eto = [];
		$bar_actual = [];
		$stmt_bar = $link->prepare("select barangay, count(*) as trv from voters where city_mun=? and barangay != '' group by barangay order by barangay");
		$stmt_bar->execute([$_SESSION["city_mun"]]);
		while ($b = $stmt_bar->fetch(PDO::FETCH_BOTH)) {
			$bar_labels[] = $b["barangay"];
			$eto_val = $b["trv"] * 0.8;
			$bar_eto[] = $eto_val;
			
			// Fetch count of MCE, BCE, PL, HL, IO, SOL, MEM in this barangay
			$stmt_cnt = $link->prepare("
				select 
					(select count(*) from mce m, voters v where m.vin=v.vin and v.barangay=?) +
					(select count(*) from bce b, voters v where b.vin=v.vin and v.barangay=?) +
					(select count(*) from pl p, voters v where p.vin=v.vin and v.barangay=?) +
					(select count(*) from hl h, voters v where h.vin=v.vin and v.barangay=?) +
					(select count(*) from officer io, voters v where io.vin=v.vin and v.barangay=?) +
					(select count(*) from special sp, voters v where sp.vin=v.vin and v.barangay=?) +
					(select count(*) from hl_children hlc, voters v where hlc.vin=v.vin and v.barangay=?) as total_actual
			");
			$stmt_cnt->execute([$b["barangay"], $b["barangay"], $b["barangay"], $b["barangay"], $b["barangay"], $b["barangay"], $b["barangay"]]);
			$bar_actual[] = $stmt_cnt->fetchColumn();
		}
	?>
	
	<!-- 2-Column Situational Graphs -->
	<div class="row g-4 mt-4" id="graphs-container">
		<!-- Purok Chart (Barangay Level) -->
		<div class="col-lg-6">
			<div class="card-glass p-3 h-100">
				<h5 style="font-weight: 700; margin-bottom: 15px;"><i class="fa-solid fa-chart-bar text-danger"></i> Purok Analysis (Barangay Level)</h5>
				<div style="position: relative; height: 350px;">
					<canvas id="purokChart"></canvas>
				</div>
			</div>
		</div>
		
		<!-- Barangay Chart (Municipal Level) -->
		<div class="col-lg-6">
			<div class="card-glass p-3 h-100">
				<h5 style="font-weight: 700; margin-bottom: 15px;"><i class="fa-solid fa-chart-column text-danger"></i> Barangay Analysis (Municipal Level)</h5>
				<div style="position: relative; height: 350px;">
					<canvas id="barangayChart"></canvas>
				</div>
			</div>
		</div>
	</div><br>

	<!-- Summary Report Grid Table Card -->
	<div class="card-glass p-0 overflow-hidden" id="report-table-card">
		<div class="table-responsive">
			<table class="table table-hover table-striped mb-0">
				<thead>
					<tr>
						<th>NO.</th>
						<th style="min-width: 150px; text-align: left;">PUROK</th>
						<th>TRV<br><small class="text-white-50">Total Reg</small></th>
						<th>ETO<br><small class="text-white-50">TRV * 80%</small></th>
						<th>TARGET<br><small class="text-white-50">ETO * 65%</small></th>
						<th>SOLID<br><small class="text-white-50">ETO * 55%</small></th>
						<th>CAP</th>
						<th>KAG</th>
						<th>PL</th>
						<th>HL</th>
						<th>IO</th>
						<th>SOL</th>
						<th>MEM</th>
						<th>MEM<br><small class="text-white-50">Target</small></th>
						<th>DILI</th>
						<th style="min-width: 130px;">SURE VOTES</th>
						<th>REM</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$stmt = $link->prepare("select address from voters where barangay = ? group by address order by address");
						$stmt->execute([$barangay_name]);
						$ex = $stmt;
						
						$purok_labels = [];
						$purok_eto = [];
						$purok_actual = [];
						
						$i = 1;
						$totvoters = 0; $totTRV = 0; $totTO = 0; $solidTot = 0;
						$totMCE = 0; $tBCE = 0; $totPL = 0; $tHL = 0;
						$tIO = 0; $tSP = 0; $tHLC = 0; $tSOL = 0;
						$targetMemTot = 0; $tOverTot = 0;
						
						while($rs = $ex->fetch(PDO::FETCH_BOTH)){
							$total = 0;
							
							// Total Registered Voters (TRV)
							$stmt = $link->prepare('select count(*) from voters v where v.address=? and v.barangay=? ');
							$stmt->execute([$rs[0], $barangay_name]);
							$rstv = $stmt->fetch(PDO::FETCH_BOTH);
							$trv_count = $rstv[0];
							$totvoters += $trv_count;
							
							// ETO, Target, Solid calculations
							$t_out = $trv_count * 0.8;
							$tro = $t_out * 0.65;
							$totTRV += $t_out;
							$totTO += $tro;
							
							$solid = $t_out * 0.55;
							$solidTot += $solid;
							
							// CAP (MCE)
							$stmt = $link->prepare('select count(*) from mce m, voters v where m.vin=v.vin and v.address=? and v.barangay=? ');
							$stmt->execute([$rs[0], $barangay_name]);
							$rsmce = $stmt->fetch(PDO::FETCH_BOTH);
							$totMCE += $rsmce[0];
							$total += $rsmce[0];
							
							// KAG (BCE)
							$stmt = $link->prepare('select count(*) from bce b, voters v where b.vin=v.vin and v.address=? and v.barangay=? ');
							$stmt->execute([$rs[0], $barangay_name]);
							$rsbce = $stmt->fetch(PDO::FETCH_BOTH);
							$tBCE += $rsbce[0];
							$total += $rsbce[0];
							
							// PL
							$stmt = $link->prepare('select count(*) from pl m, voters v where m.vin=v.vin and v.address=? and v.barangay=? ');
							$stmt->execute([$rs[0], $barangay_name]);
							$rspl = $stmt->fetch(PDO::FETCH_BOTH);
							$totPL += $rspl[0];
							$total += $rspl[0];
							
							// HL
							$stmt = $link->prepare('select count(*) from hl h, voters v where h.vin=v.vin and v.address=? and v.barangay=? ');
							$stmt->execute([$rs[0], $barangay_name]);
							$rshl = $stmt->fetch(PDO::FETCH_BOTH);
							$tHL += $rshl[0];
							$total += $rshl[0];
							
							// IO
							$stmt = $link->prepare('select count(*) from officer io, voters v where io.vin=v.vin and v.address=? and v.barangay=? ');
							$stmt->execute([$rs[0], $barangay_name]);
							$rsio = $stmt->fetch(PDO::FETCH_BOTH);
							$tIO += $rsio[0];
							$total += $rsio[0];
							
							// SOL
							$stmt = $link->prepare('select count(*) from special sp, voters v where sp.vin=v.vin and v.address=? and v.barangay=? ');
							$stmt->execute([$rs[0], $barangay_name]);
							$rssp = $stmt->fetch(PDO::FETCH_BOTH);
							$tSP += $rssp[0];
							$total += $rssp[0];
							
							// MEM (HL children)
							$stmt = $link->prepare('select count(*) from hl_children hlc, voters v where hlc.vin=v.vin and v.address=? and v.barangay=? ');
							$stmt->execute([$rs[0], $barangay_name]);
							$rshlc = $stmt->fetch(PDO::FETCH_BOTH);
							$tHLC += $rshlc[0];
							$total += $rshlc[0];
							
							// Target members
							$targetMem = $solid - 11 - $rsmce[0];
							$targetMemTot += $targetMem;
							
							// Dili Ato
							$stmt = $link->prepare('select count(*) from sollist sol, voters v where sol.vin=v.vin and v.address=? and v.barangay=? ');
							$stmt->execute([$rs[0], $barangay_name]);
							$rssol = $stmt->fetch(PDO::FETCH_BOTH);
							$tSOL += $rssol[0];
							
							$tOverTot += $total;
							
							$purok_labels[] = $rs[0];
							$purok_eto[] = $t_out;
							$purok_actual[] = $total;
							
							$pct = ($t_out > 0) ? ($total / $t_out * 100) : 0;
							$rem_icon = ($pct >= 55) ? "<b style='color:green'>&#10004;</b>" : "<span style='color:red'>&#10060;</span>";
							
							echo "
							<tr>
								<td>$i.</td>
								<td style='text-align: left; font-weight: 600;'>$rs[0]</td>
								<td>" . number_format($trv_count) . "</td>
								<td>" . number_format($t_out) . "</td>
								<td>" . number_format($tro) . "</td>
								<td>" . number_format($solid) . "</td>
								<td>$rsmce[0]</td>
								<td>$rsbce[0]</td>
								<td>$rspl[0]</td>
								<td>$rshl[0]</td>
								<td>$rsio[0]</td>
								<td>$rssp[0]</td>
								<td>$rshlc[0]</td>
								<td>" . number_format($targetMem, 0) . "</td>
								<td>" . number_format($rssol[0]) . "</td>
								<td><strong>" . number_format($total) . "</strong> <small class='text-primary'>(" . number_format($pct, 1) . "%)</small></td>
								<td>$rem_icon</td>
							</tr>";
							$i++;
						}
						
						// Totals Row
						$total_pct = ($totTRV > 0) ? ($tOverTot / $totTRV * 100) : 0;
						$total_rem = ($total_pct >= 55) ? "<b class='text-white'>&#10004;</b>" : "<span class='text-white'>&#10060;</span>";
					?>
				</tbody>
				<tfoot class="table-dark" style="font-weight: 700; border-top: 2px solid var(--primary-hover);">
					<tr>
						<td></td>
						<td style="text-align: left;">TOTALS</td>
						<td><?php echo number_format($totvoters); ?></td>
						<td><?php echo number_format($totTRV); ?></td>
						<td><?php echo number_format($totTO); ?></td>
						<td><?php echo number_format($solidTot); ?></td>
						<td><?php echo number_format($totMCE); ?></td>
						<td><?php echo number_format($tBCE); ?></td>
						<td><?php echo number_format($totPL); ?></td>
						<td><?php echo number_format($tHL); ?></td>
						<td><?php echo number_format($tIO); ?></td>
						<td><?php echo number_format($tSP); ?></td>
						<td><?php echo number_format($tHLC); ?></td>
						<td><?php echo number_format($targetMemTot, 0); ?></td>
						<td><?php echo number_format($tSOL); ?></td>
						<td><?php echo number_format($tOverTot); ?> <small class="text-white-50">(<?php echo number_format($total_pct, 1); ?>%)</small></td>
						<td><?php echo $total_rem; ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<!-- Legend Card -->
		<div style="margin:25px 10px -10px 20px">
			<h5 style="font-weight: 700; font-size: 15px;"><i class="fa-solid fa-circle-info"></i> Column Legend</h5>
		</div>
		<div class="row g-2 text-muted small" style="padding:20px;font-size: 13px;">
			<div class="col-lg-3 col-md-6"><strong>TRV:</strong> Total Registered Voters</div>
			<div class="col-lg-3 col-md-6"><strong>ETO:</strong> Expected Turn-Out (TRV * 80%)</div>
			<div class="col-lg-3 col-md-6"><strong>TARGET:</strong> Target Voters (ETO * 65%)</div>
			<div class="col-lg-3 col-md-6"><strong>SOLID:</strong> Solid Target (ETO * 55%)</div>
			
			<div class="col-lg-3 col-md-6"><strong>CAP:</strong> Barangay Captain</div>
			<div class="col-lg-3 col-md-6"><strong>KAG:</strong> Barangay Kagawad</div>
			<div class="col-lg-3 col-md-6"><strong>PL:</strong> Precinct Leader</div>
			<div class="col-lg-3 col-md-6"><strong>HL:</strong> Household Leader</div>
			
			<div class="col-lg-3 col-md-6"><strong>IO:</strong> Information Officer</div>
			<div class="col-lg-3 col-md-6"><strong>SOL:</strong> Special Operation List</div>
			<div class="col-lg-3 col-md-6"><strong>MEM:</strong> Household Member</div>
			<div class="col-lg-3 col-md-6"><strong>DILI:</strong> Dili Ato (Opponent)</div>
		</div>
	</div>
<br><br>

	<script>
		document.addEventListener("DOMContentLoaded", function() {
			// Get current theme text color to style the charts
			const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
			const textColor = isDark ? '#cbd5e1' : '#475569';
			const gridColor = isDark ? 'rgba(51, 65, 85, 0.3)' : 'rgba(226, 232, 240, 0.8)';

			// 1. Purok Chart
			const purokCtx = document.getElementById('purokChart').getContext('2d');
			new Chart(purokCtx, {
				type: 'bar',
				data: {
					labels: <?php echo json_encode($purok_labels); ?>,
					datasets: [
						{
							label: 'Expected Turn-Out (ETO)',
							data: <?php echo json_encode($purok_eto); ?>,
							backgroundColor: 'rgba(239, 68, 68, 0.25)',
							borderColor: '#ef4444',
							borderWidth: 2,
							borderRadius: 6
						},
						{
							label: 'Actual PLO Members',
							data: <?php echo json_encode($purok_actual); ?>,
							backgroundColor: 'rgba(5, 150, 105, 0.25)',
							borderColor: '#059669',
							borderWidth: 2,
							borderRadius: 6
						}
					]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: {
							labels: { color: textColor, font: { family: 'Outfit', size: 12 } }
						}
					},
					scales: {
						x: {
							grid: { color: gridColor },
							ticks: { color: textColor, font: { family: 'Outfit' } }
						},
						y: {
							grid: { color: gridColor },
							ticks: { color: textColor, font: { family: 'Outfit' } }
						}
					}
				}
			});

			// 2. Barangay Chart
			const barangayCtx = document.getElementById('barangayChart').getContext('2d');
			new Chart(barangayCtx, {
				type: 'bar',
				data: {
					labels: <?php echo json_encode($bar_labels); ?>,
					datasets: [
						{
							label: 'Expected Turn-Out (ETO)',
							data: <?php echo json_encode($bar_eto); ?>,
							backgroundColor: 'rgba(59, 130, 246, 0.25)',
							borderColor: '#3b82f6',
							borderWidth: 2,
							borderRadius: 6
						},
						{
							label: 'Actual PLO Members',
							data: <?php echo json_encode($bar_actual); ?>,
							backgroundColor: 'rgba(217, 119, 6, 0.25)',
							borderColor: '#d97706',
							borderWidth: 2,
							borderRadius: 6
						}
					]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: {
							labels: { color: textColor, font: { family: 'Outfit', size: 12 } }
						}
					},
					scales: {
						x: {
							grid: { color: gridColor },
							ticks: { color: textColor, font: { family: 'Outfit' } }
						},
						y: {
							grid: { color: gridColor },
							ticks: { color: textColor, font: { family: 'Outfit' } }
						}
					}
				}
			});
		});
	</script>
</div>

<?php require("footer.php"); ?>

</body>

</html>
