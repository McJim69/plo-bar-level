<?php
	require("connect.php");
	require("head.php");
?>

<style>
	/* Custom styling for voters search modal */
	.modal-content-glass {
		background: rgba(255, 255, 255, 0.95);
		backdrop-filter: blur(10px);
		border: 1px solid var(--card-border);
		border-radius: var(--radius-md);
	}
	
	.modal-header-custom {
		background: var(--primary);
		color: #ffffff;
		border-bottom: none;
	}
	
	.voters-search-input {
		background: #ffffff;
		border: 1px solid #cbd5e1;
		border-radius: var(--radius-sm);
		padding: 8px 16px;
		width: 100%;
	}
</style>

<script>
	var table = "mce";
	var hlvotno = 0;
								
	function getVoters(value){	
		xmlhttp.onreadystatechange = function(){
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
				getID("query_voters").innerHTML = xmlhttp.responseText;
			}
		}						
		xmlhttp.open("GET", "ajax/getusers.php?value=" + encodeURIComponent(value) + "&id=" + hlvotno + "&table=" + table, true);
		xmlhttp.send();
	}
	
	function showAddModal() {
		getVoters('');
		var addModal = new bootstrap.Modal(document.getElementById('votersModal'));
		addModal.show();
	}
</script>

<?php require("menu.php"); ?>

<!-- Voters Selector Modal -->
<div class="modal fade" id="votersModal" tabindex="-1" aria-labelledby="votersModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-scrollable">
		<div class="modal-content modal-content-glass">
			<div class="modal-header modal-header-custom">
				<h5 class="modal-title" id="votersModalLabel"><i class="fa-solid fa-user-plus"></i> Select Voter to Add as User</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="mb-3">
					<input type="text" class="voters-search-input" placeholder="Type a name or keyword to search..." onkeyup="getVoters(this.value)" />
				</div>
				<div id="query_voters"></div>
			</div>
		</div>
	</div>
</div>

<div class="container my-4">
	<!-- Control Panel Card -->
	<div class="card-glass mb-4">
		<form method="post" class="row g-3 align-items-center">
			<div class="col-lg-3 col-md-6">
				<input placeholder="Type keyword to search..." type="text" name="t_search" id="t_search" value="<?php if(isset($_POST["t_search"])){echo htmlspecialchars($_POST["t_search"]);} ?>" />
			</div>
			<div class="col-lg-3 col-md-6 d-flex gap-2">
				<button class="btn btn-danger w-100" type="submit" name="b_search"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
				<a class="btn btn-outline-secondary w-100" href="users.php"><i class="fa-solid fa-arrows-rotate"></i> Reset</a>
			</div>
			
			<div class="col-lg-4 col-md-6 d-flex gap-2 justify-content-lg-end">
				<?php 
					if($_SESSION["access"] == "SuperAdmin"){
						echo '<button class="btn btn-success" type="button" onclick="showAddModal()"><i class="fa-solid fa-user-plus"></i> Add User</button>';
					}
				?>
				<button class="btn btn-dark" type="button" onclick="printF()"><i class="fa-solid fa-print"></i> List</button>
				<button class="btn btn-dark" type="button" onclick="printFt()"><i class="fa-solid fa-image"></i> Cards</button>
			</div>

			<div class="col-lg-2 col-md-6">
				<select onchange="jump('?barangay=' + encodeURIComponent(this.value))">
					<option value="All barangays">All barangays</option>
					<?php
						$stmt = $link->prepare('select barangay from voters where city_mun=? group by barangay order by barangay');
						$stmt->execute([$_SESSION["city_mun"]]);
						$ex2 = $stmt;
						while($rs = $ex2->fetch(PDO::FETCH_BOTH)){
							$selected = (isset($_GET["barangay"]) && $_GET["barangay"] === $rs[0]) ? "selected" : "";
							echo "<option $selected>".$rs[0]."</option>";
						}
					?>
				</select>
			</div>
		</form>
	</div>

	<!-- Print Section Header (Hidden on screen) -->
	<div id="header" class="text-center my-4 d-none">	
		<div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
			<img src="images/alayon-big.png" height="70px"/>
			<div>
				<h3 class="text-uppercase mb-1" style="font-weight: 700;">List of PLO Users</h3>
				<p class="mb-0 text-muted" style="font-size: 14px;">
					<?php
						if(isset($_GET["access"]) && $_GET["access"] != "All access" && $_GET["access"] != "") {
							echo $_GET["access"] . " - ";
						}
					?>
					City of <?php echo $_SESSION["city_mun"]; ?>
				</p>
			</div>
			<img src="images/iloveyudark.png" height="80px"/>
		</div>
	</div>

	<!-- List View (For Print and Screen Table) -->
	<div id="grid" class="d-none card-glass">
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>		
						<th style="width: 60px; text-align: center;">NO.</th>
						<th style="width: 80px; text-align: center;">PHOTO</th>	
						<th>FULLNAME</th>
						<th>ACCESS</th>
						<th>USERNAME</th>
						<th>BARANGAY</th>
						<th>CONTACT</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$sql_cond = " where m.vin=v.vin and v.city_mun = ?";
						$params = [$_SESSION["city_mun"]];
						
						if(isset($_GET["barangay"]) && $_GET["barangay"] != "All barangays" && $_GET["barangay"] != "") {
							$sql_cond .= " and v.barangay = ?";
							$params[] = $_GET["barangay"];
						}
						
						if(isset($_POST["b_search"])){
							$search = $_POST["t_search"];
							$sql_cond .= " and (v.vname LIKE CONCAT('%', ?, '%') or v.precinct LIKE CONCAT('%', ?, '%') or v.address LIKE CONCAT('%', ?, '%') or v.barangay LIKE CONCAT('%', ?, '%') or v.city_mun LIKE CONCAT('%', ?, '%'))";
							$params = array_merge($params, [$search, $search, $search, $search, $search]);
						}
						
						$stmt = $link->prepare("select * from users m, voters v" . $sql_cond . " order by access");
						$stmt->execute($params);
						$ex = $stmt;
						
						$i = 1;
						while($rs3 = $ex->fetch(PDO::FETCH_BOTH)){
							$img_src = file_exists("images/voters/".$rs3[0].".jpg") ? "images/voters/".$rs3[0].".jpg" : "images/blank.jpg";
							echo "
							<tr>
								<td style='text-align:center;'>".$i."</td>
								<td style='text-align:center; padding: 2px;'><img src='$img_src?".date("h:i:s")."' height='50' style='border-radius:4px; object-fit:cover;' /></td>
								<td>".htmlspecialchars($rs3["vname"])."</td>
								<td>".htmlspecialchars($rs3["access"])."</td>
								<td>".htmlspecialchars($rs3["username"])."</td>
								<td>".htmlspecialchars($rs3["barangay"])."</td>
								<td>".htmlspecialchars($rs3["contact"])."</td>
							</tr>";
							$i++;
						}
					?>
				</tbody>
			</table>
		</div>
	</div>

	<!-- Thumbnails Grid View -->
	<div id="thumbnails" class="tomb-grid">
		<?php
			$sql_cond = " where m.vin=v.vin and v.city_mun = ?";
			$params = [$_SESSION["city_mun"]];
			
			if(isset($_GET["barangay"]) && $_GET["barangay"] != "All barangays" && $_GET["barangay"] != "") {
				$sql_cond .= " and v.barangay = ?";
				$params[] = $_GET["barangay"];
			}
			
			if(isset($_POST["b_search"])){
				$search = $_POST["t_search"];
				$sql_cond .= " and (v.vname LIKE CONCAT('%', ?, '%') or v.precinct LIKE CONCAT('%', ?, '%') or v.address LIKE CONCAT('%', ?, '%') or v.barangay LIKE CONCAT('%', ?, '%') or v.city_mun LIKE CONCAT('%', ?, '%'))";
				$params = array_merge($params, [$search, $search, $search, $search, $search]);
			}
			
			$stmt = $link->prepare("select * from users m, voters v" . $sql_cond . " order by vname");
			$stmt->execute($params);
			$ex = $stmt;
			
			$value = isset($_POST["t_search"]) ? strtoupper($_POST["t_search"]) : "";
			$rep = "<b style='color:#0014d0;background:#ffa0a0'>".$value."</b>";
			
			while($rs = $ex->fetch(PDO::FETCH_BOTH)){
				$img_src = file_exists("images/voters/".$rs[0].".jpg") ? "images/voters/".$rs[0].".jpg" : "images/blank.png";
				
				// Highlight search term
				$disp_name = ($value != "") ? str_ireplace($value, $rep, $rs["vname"]) : $rs["vname"];
				$disp_access = ($value != "") ? str_ireplace($value, $rep, $rs["access"]) : $rs["access"];
				$disp_username = ($value != "") ? str_ireplace($value, $rep, $rs["username"]) : $rs["username"];
				$disp_contact = ($value != "") ? str_ireplace($value, $rep, $rs["contact"]) : $rs["contact"];
				
				echo "
				<div class='tomb card-glass' id='div_".$rs[0]."' 
					onmouseout=\"getID('div_controls_".$rs[0]."').style.visibility='hidden';\" 
					onmousemove=\"getID('div_controls_".$rs[0]."').style.visibility='visible';\">
					
					<img src='$img_src?".date("h:i:s")."' alt='User Photo'>
					
					<div class='mb-2'>
						<h5 style='font-size: 15px; font-weight: 700; margin-bottom: 4px; text-transform: uppercase;'>$disp_name</h5>
						<p class='text-muted mb-0' style='font-size: 13px;'><i class='fa-solid fa-id-card'></i> ID: ".sprintf("%04d", $rs["vin"])."</p>
					</div>
					
					<div class='small text-muted space-y-1' style='font-size: 12px;'>
						<div>Access Level: <strong class='text-danger'>$disp_access</strong></div>
						<div>Username: <strong>$disp_username</strong></div>
						<div>Contact: <strong>$disp_contact</strong></div>
					</div>
					
					<div class='mt-3 d-flex gap-2' style='visibility: hidden; transition: var(--transition);' id='div_controls_".$rs[0]."'>
						<a rel='facebox' href='usersupdate.php?users=".$rs[0]."' class='btn btn-sm btn-outline-danger px-3'><i class='fa-solid fa-pen-to-square'></i> Update</a>
						<button onclick=\"deleteusers('".$rs[0]."')\" class='btn btn-sm btn-dark px-3'><i class='fa-solid fa-trash-can'></i> Remove</button>
					</div>
				</div>";
			}
		?>
	</div>
</div>

<script>
	function printF(){
		$('#header').removeClass('d-none');
		$('#grid').removeClass('d-none');
		$('#thumbnails').addClass('d-none');
		$('.card-glass').first().addClass('d-none');
		$('#cssmenu-wrapper').addClass('d-none');
		window.print();
		$('#header').addClass('d-none');
		$('#grid').addClass('d-none');
		$('#thumbnails').removeClass('d-none');
		$('.card-glass').first().removeClass('d-none');
		$('#cssmenu-wrapper').removeClass('d-none');
	}
	
	function printFt(){
		$('#header').removeClass('d-none');
		$('.card-glass').first().addClass('d-none');
		$('#cssmenu-wrapper').addClass('d-none');
		window.print();
		$('#header').addClass('d-none');
		$('.card-glass').first().removeClass('d-none');
		$('#cssmenu-wrapper').removeClass('d-none');
	}

	function addmce(id, row){
		table = "mce";
		var vin = id;
		xmlhttp.onreadystatechange = function(){
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
				if(xmlhttp.responseText == "Success"){
					$("#q_tr_" + row).animate({
						opacity: 0
					}, 500, function(){
						$("#q_tr_" + row).css("display", "none");
					});
				} else {
					alert(xmlhttp.responseText);
				}
			}
		}						
		xmlhttp.open("GET", "ajax/addusers.php?table=" + table + "&vin=" + vin, true);
		xmlhttp.send();
	}
			
	function deleteusers(vin){	
		if(confirm("Are you Sure?")){
			xmlhttp.onreadystatechange = function(){
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
					if(xmlhttp.responseText == "Success"){
						$("#div_" + vin).fadeOut(500);
					}
				}
			}						
			xmlhttp.open("GET", "ajax/deleteusers.php?vin=" + vin, true);
			xmlhttp.send();
		}
	}
</script>

<?php
	include("user_profile.php");	
	require("footer.php");
?>
</body>
</html>