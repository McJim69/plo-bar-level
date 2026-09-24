<?php
	require("connect.php");	
	require("head.php");
	
	$ex=$link->query("select * from voters limit 0,1");
	$rs=$ex->fetch(PDO::FETCH_BOTH);
	$_SESSION["city_mun"]=$rs["city_mun"];
?>

<?php require("menu.php"); ?>

<script> setActive("home"); </script>

<div class="container my-5">
	<div class="row d-flex justify-content-center align-items-center">
		<div class="col-lg-8 col-md-10">
			<!-- Premium User Greeting Card -->
			<div class="card-glass mb-5 animate__animated animate__fadeIn d-flex flex-column flex-md-row align-items-center justify-content-center gap-4" id="greeting-card">
				<?php include("user_profile.php"); ?>
				<div>
					<h2 class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2" style="font-size:20px;margin-bottom: 0;">
						<span style="color: #ea580c;">Bonjour!</span> 
						<span style="color: var(--text-muted)">Buenas Dias</span>
						<span style="color: var(--primary)">
							<?php if ($_SESSION["gender"]=="M") echo "Señor"; else echo"Señorita";?> <b class="text-uppercase"><?PHP echo $_SESSION["user"];?>!</b>
						</span> &nbsp; &nbsp;
						<span class="badge bg-primary px-3 py-2" style="border-radius: var(--radius-sm);">
							<?PHP echo $_SESSION["access"]; ?>
						</span> &nbsp; &nbsp;
						<span class="badge bg-danger px-3 py-2" style="border-radius: var(--radius-sm);cursor:pointer" onclick='sessionEnd()' title='Logout'>
							Logout
						</span>
					</h2>
				</div>
			</div>

			<!-- Premium Bootstrap Carousel -->
			<div id="indexCarousel" class="bg-white carousel slide carousel-fade mb-5" data-bs-ride="carousel" style="border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-lg); border: 4px solid var(--primary);">
				<div class="carousel-inner" align="center">
					<?php
						for($i=1;$i<8;$i++) {
							$active = ($i === 1) ? 'active' : '';
							echo "
							<div class='carousel-item $active'>
								<img src='images/bcg/$i.jpg?".date("h:i:s")."' class='d-block' alt='Slide $i' style='max-width:400px;aspect-ratio: 2/2; object-fit: cover;'>
							</div>";
						}
					?>
				</div>
				<button class="carousel-control-prev" type="button" data-bs-target="#indexCarousel" data-bs-slide="prev">
					<span class="carousel-control-prev-icon" aria-hidden="true"></span>
					<span class="visually-hidden">Previous</span>
				</button>
				<button class="carousel-control-next" type="button" data-bs-target="#indexCarousel" data-bs-slide="next">
					<span class="carousel-control-next-icon" aria-hidden="true"></span>
					<span class="visually-hidden">Next</span>
				</button>
			</div>
		</div>
	</div>
</div>

<?php require("footer.php");?>	

</body>

</html>