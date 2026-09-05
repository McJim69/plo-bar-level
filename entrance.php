<?php
	session_start();	

	define('DB_SERVER', 'mcjim-server.com');
	define('DB_USERNAME', 'McJim');
	define('DB_PASSWORD', 'Restricted654123');
	define('DB_NAME', 'plo_records_2019');

	try {
		$link = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD, [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_BOTH,
			PDO::ATTR_EMULATE_PREPARES => true,
		]);
	} catch (PDOException $e) {
		die("ERROR: Could not connect. " . $e->getMessage());
	}
	
	require("head.php");	
	
	$m="";
	if(isset($_POST["login"])){
		$stmt = $link->prepare("select * from users where username=? and password=?");
		$stmt->execute([$_POST["user"], $_POST["pass"]]);
		$ex = $stmt;
		if($rs=$ex->fetch(PDO::FETCH_BOTH)){
			$stmt = $link->prepare("select * from validity where validity>?");
			$stmt->execute([date("Y-m-d")]);
			$exx = $stmt;
			if($rs1=$exx->fetch(PDO::FETCH_BOTH)){
				$_SESSION["city_mun"]=!empty($_POST["t_city_mun"]) ? $_POST["t_city_mun"] : $rs["city_mun"];
				$_SESSION["barangay"]=$rs["barangay"];
				$_SESSION["city_mun_assigned"]=$rs["city_mun"];
				$_SESSION["barangay_assigned"]=$rs["barangay"];
				$_SESSION["vin"]=$rs["vin"];
				$_SESSION["user"]=$rs["username"];
				$_SESSION["name"]=$rs["fullname"];
				$_SESSION["access"]=$rs["access"];
				$_SESSION["gender"]=$rs["gender"];
				
				echo"<script>window.location='index.php';</script>";
				exit();
			}else {
				$m="<div class='alert alert-danger'><b>ACCESS DENIED!</b> Your access validity has expired. Contact your system administrator for assistance.</div>";
			}
		}		
		else {
			$m="<div class='alert alert-danger'><b>ACCESS DENIED!</b> Either username or password is invalid.</div>";
			$err=1;
		}
	}
?>

<style>
	body {
		background: linear-gradient(135deg, #1e3a8a 0%, #1e1b4b 100%) !important;
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
		z-index: 10;
	}
	
	.login-card:hover {
		border-color: rgba(255, 255, 255, 0.25);
		box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
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
		color: #1e3a8a !important;
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
	
	.system-version {
		text-align: center;
		margin-top: 30px;
		color: #94a3b8;
		font-size: 12px;
	}
	
	.footer-logo {
		margin-top: 20px;
		opacity: 0.8;
		z-index: 1;
	}
</style>

<div class="login-card">
	<div style="text-align: center; margin-bottom: 20px;">
		<h2 style="font-weight: 700; color: #ffffff; letter-spacing: 0.5px;">PLO SYSTEM</h2>
		<p style="color: #94a3b8; font-size: 14px;">Please login to access your portal</p>
	</div>
	
	<?php if($m != "") echo $m; ?>
	
	<form method="post" class="space-y-4">
		<div>
			<label class="form-label mb-2">City / Municipality</label>
			<select class="login-input" name="t_city_mun" required>
				<?php
					$ex = $link->query("select city_mun from voters group by city_mun");
					while($rs=$ex->fetch(PDO::FETCH_BOTH)) {
						echo "<option style='color:#000;'>".$rs["city_mun"]."</option>";
					}
				?>
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
	
	<div class="system-version">
		PLO System v5.19
	</div>
</div>

<div class="footer-logo">
	<img src="images/foot1.png" style="max-width: 250px;"/>
</div>

<?php 
	require("footer.php"); 
?>
</body>
</html>