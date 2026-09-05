<?php require("connect.php");?>

<style>
	.user-profile-badge {
		position: fixed;
		top: 75px;
		right: 20px;
		z-index: 999;
		background: var(--card-bg);
		backdrop-filter: blur(10px);
		border: 1px solid var(--card-border);
		border-radius: var(--radius-md);
		padding: 12px;
		box-shadow: var(--shadow-md);
		transition: var(--transition);
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 6px;
	}
	
	.user-profile-badge:hover {
		box-shadow: var(--shadow-lg);
		border-color: var(--primary);
	}
	
	.user-profile-badge img {
		height: 80px;
		width: 80px;
		object-fit: cover;
		border-radius: 50%;
		border: 3px solid var(--primary);
		cursor: pointer;
		transition: var(--transition);
	}
	
	.user-profile-badge img:hover {
		transform: scale(1.05);
		filter: brightness(0.85);
	}
	
	/* Index Page Inline Integration */
	#greeting-card .user-profile-badge {
		position: static;
		box-shadow: none;
		border: none;
		background: none;
		backdrop-filter: none;
		padding: 0;
		margin: 0;
		z-index: auto;
	}
	
	#greeting-card .user-profile-badge [id^="div_browse_"] {
		display: none !important;
	}
	
	@media (max-width: 1200px) {
		.user-profile-badge {
			position: static;
			margin: 20px auto;
			width: max-content;
			flex-direction: row;
		}
	}
</style>

<form method="post" enctype="multipart/form-data" style="margin: 0;">
	<?php
		if(isset($_SESSION['user'])){

			$sql = 'select * from voters v, users m where ';
			$params = [];
			
			if(isset($_GET["barangay"]) && $_GET["barangay"] != "All barangays" && $_GET["barangay"] != "") {
				$sql .= 'v.barangay = ? and ';
				$params[] = $_GET["barangay"];
			}
			
			if(isset($_GET["access"]) && $_GET["access"] != "All access" && $_GET["access"] != "") {
				$sql .= 'm.access = ? and ';
				$params[] = $_GET["access"];
			}
			
			$sql .= 'm.vin=v.vin and m.username=? order by access';
			$params[] = $_SESSION["user"];
			
			$stmt = $link->prepare($sql);
			$stmt->execute($params);
			$ex = $stmt;

			while($rs=$ex->fetch(PDO::FETCH_BOTH)){
								
				if(isset($_POST["b_remove_".$rs[0]])){
					$stmt = $link->prepare('delete from users where vin=?');
					$stmt->execute([$rs[0]]);
					jump("");
				}
				if(isset($_POST["b_upImg_".$rs[0]])){
					move_uploaded_file($_FILES["b_file_".$rs[0]]["tmp_name"], "images/voters/".$rs[0].".jpg");
					$stmt = $link->prepare('update voters set ispicset=1 where vin=?');
					$stmt->execute([$rs[0]]);
					jump("");
				}			
					
				echo "
				<div class='user-profile-badge' id='div_".$rs[0]."' onmouseout=\"getID('div_controls_".$rs[0]."').style.visibility='hidden';getID('div_browse_".$rs[0]."').style.visibility='hidden';\" onmousemove=\"getID('div_controls_".$rs[0]."').style.visibility='visible';getID('div_browse_".$rs[0]."').style.visibility='visible';\" >";
				
					if(file_exists("images/voters/".$rs[0].".jpg")){
						echo " <img src='images/voters/".$rs[0].".jpg?".date("h:i:s")."' alt='Profile' title='Click to Change Photo' onclick=\"\$('#b_file_".$rs[0]."').click();\" />";
					}
					else {
						echo " <img src='images/blank.png' alt='Profile' title='Click to Change Photo' onclick=\"\$('#b_file_".$rs[0]."').click();\" />";
					}
					
					echo "
					<div style='position:absolute;bottom:-35px;z-index:100;visibility:hidden' id='div_browse_".$rs[0]."' >
						<input type=file name='b_file_".$rs[0]."' id='b_file_".$rs[0]."' style='visibility:hidden;width:0px;height:0px;' onchange=\"if(this.value!='')\$('#b_upImg_".$rs[0]."').click();\" /> 
						<input type=submit name='b_upImg_".$rs[0]."' id='b_upImg_".$rs[0]."' value='Upload' style='display:none' />  
						<input type=button value='Change Photo' onclick=\"\$('#b_file_".$rs[0]."').click();\" class='btn btn-sm btn-dark' style='font-size:10px;padding:4px 8px;' />  
					</div>";
					
				echo "	
				</div>";
			}
		}
	?>
</form>