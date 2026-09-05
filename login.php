<?php
	require("connect.php");		
	require("head.php");	
	
	if(isset($_SESSION["user"]) && $_SESSION["user"] !== ""){
		header("Location: index.php");
		exit();
	}
	
	$m="";
	if(isset($_POST["login"])){
		$_SESSION["city_mun"]=isset($_POST["t_city_mun"]) ? $_POST["t_city_mun"] : "";
		$stmt = $link->prepare("select * from users where username=? and password=?");
		$stmt->execute([$_POST["user"], $_POST["pass"]]);
		$ex = $stmt;
		if($rs=$ex->fetch(PDO::FETCH_BOTH)){
			$stmt = $link->prepare("select * from validity where validity>?");
			$stmt->execute([date("Y-m-d")]);
			$exx = $stmt;
			if($rs1=$exx->fetch(PDO::FETCH_BOTH)){
				$_SESSION["vin"]=$rs["vin"];
				$_SESSION["user"]=$rs["username"];
				$_SESSION["name"]=$rs["fullname"];
				$_SESSION["access"]=$rs["access"];
				$_SESSION["gender"]=$rs["gender"];
				$_SESSION["city_mun_assigned"]=$rs["city_mun"];
				$_SESSION["barangay_assigned"]=$rs["barangay"];
				$_SESSION["city_mun"]=($rs["access"] === 'SuperAdmin' || $rs["access"] === 'Admin') ? $_POST["t_city_mun"] : $rs["city_mun"];
				$_SESSION["barangay"]=($rs["access"] === 'SuperAdmin' || $rs["access"] === 'Admin') ? $_POST["t_barangay"] : $rs["barangay"];
				
				echo"<script>window.location='index.php';</script>";
				exit();
			}else{
				$m="<div class='alert alert-danger'><b>ACCESS DENIED!</b> Your access validity has expired. Contact your system administrator for assistance.</div>";
				$err=1;
			}
		}else{
			$m="<div class='alert alert-danger'><b>ACCESS DENIED!</b> Either username or password is invalid.</div>";
			$err=1;
		}
	}
?>

<style>
	body {
		background: linear-gradient(135deg, #7f1d1d 0%, #1e1b4b 100%) !important;
		min-height: 100vh;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		padding: 20px;
	}
	
	.login-card {
		background: rgba(255, 255, 255, 0.08);
		backdrop-filter: blur(16px);
		-webkit-backdrop-filter: blur(16px);
		border: 1px solid rgba(255, 255, 255, 0.15);
		border-radius: var(--radius-lg);
		box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
		padding: 40px 30px;
		width: 100%;
		max-width: 440px;
		color: #ffffff;
		transition: var(--transition);
	}
	
	.login-card:hover {
		border-color: rgba(255, 255, 255, 0.25);
		box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
	}
	
	.login-logo {
		margin-bottom: 20px;
		text-align: center;
		width: 100%;
		max-width: 440px;
	}
	
	.login-logo img {
		max-width: 100%;
		height: auto;
		border-radius: var(--radius-md);
		box-shadow: var(--shadow-md);
	}
	
	.form-label {
		font-weight: 500;
		color: #e2e8f0;
		font-size: 13px;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}
	
	.login-input {
		background: rgba(255, 255, 255, 0.1) !important;
		border: 1px solid rgba(255, 255, 255, 0.2) !important;
		color: #ffffff !important;
		border-radius: var(--radius-md) !important;
		padding: 12px 18px !important;
		width: 100% !important;
	}
	
	.login-input::placeholder {
		color: #cbd5e1;
	}
	
	.login-input:focus {
		background: rgba(255, 255, 255, 0.15) !important;
		border-color: #ffffff !important;
		box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2) !important;
	}
	
	.btn-login {
		background: #ffffff !important;
		color: #7f1d1d !important;
		font-weight: 600 !important;
		border-radius: var(--radius-md) !important;
		padding: 12px !important;
		width: 100%;
		font-size: 15px !important;
		transition: var(--transition) !important;
		border: none !important;
	}
	
	.btn-login:hover {
		background: #f8fafc !important;
		transform: translateY(-2px);
		box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
	}
	
	.btn-login:active {
		transform: translateY(0);
	}
	
	.system-version {
		text-align: center;
		margin-top: 30px;
		color: #94a3b8;
		font-size: 12px;
	}
</style>

<div class="login-logo">
	<img src="images/header.png" alt="PLO Logo">
</div>

<div class="login-card">
	<?php if($m != "") echo $m; ?>
	
	<form method="post" class="space-y-4">
		<div>
			<label class="form-label mb-2">City / Municipality</label>
			<select class="login-input" name="t_city_mun" required>
				<?php
					$ex = $link->query("select city_mun from voters group by city_mun");
					while($rs=$ex->fetch(PDO::FETCH_BOTH)) {
						echo "<option style='color:#000;'>".htmlspecialchars($rs["city_mun"])."</option>";
					}
				?>
			</select>
		</div>

		<div>
			<label class="form-label mb-2">Barangay</label>
			<select class="login-input" name="t_barangay" id="t_barangay" required>
				<!-- Dynamically populated -->
			</select>
		</div>
		
		<div>
			<label class="form-label mb-2">Username</label>
			<input class="login-input" type="text" placeholder="Enter username" autofocus required name="user" />
		</div>
		
		<div>
			<label class="form-label mb-2">Password</label>
			<input class="login-input" type="password" placeholder="Enter password" required name="pass" />
		</div>
		
		<div style="padding-top: 10px;">
			<button class="btn-login" type="submit" name="login">Sign In</button>
		</div>
	</form>

	<script>
		function updateBarangays() {
			var citySelect = document.getElementsByName("t_city_mun")[0];
			if(!citySelect) return;
			var cityMun = citySelect.value;
			var url = "ajax/get_barangays.php?city_mun=" + encodeURIComponent(cityMun);
			fetch(url)
				.then(response => response.text())
				.then(data => {
					document.getElementById("t_barangay").innerHTML = data;
				})
				.catch(err => console.error("Error fetching barangays:", err));
		}

		document.addEventListener("DOMContentLoaded", function() {
			var citySelect = document.getElementsByName("t_city_mun")[0];
			if(citySelect) {
				citySelect.addEventListener("change", updateBarangays);
				updateBarangays();
			}
		});
	</script>
	
	<div class="system-version">
		PLO System v5.23
	</div>
</div>

<?php 
//	require("footer.php"); 
	if(isset($err) && $err == 1) {
		echo "<script>$('.alert').addClass('animate__animated animate__shakeX');</script>";
	}
?>
</body>
</html>