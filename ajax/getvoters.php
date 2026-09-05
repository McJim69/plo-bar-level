<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require("../connect.php");
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
?>

<style>
	.t_voters td{
		border-bottom:1px dotted #aaa;
		padding:2px;
	}
	a{
		color:red;
		text-decoration:none;
	}
	a:hover{
		text-decoration:underline;color:#fff;
	}
</style>

<!-- 
Debug Info:
Session user: <?php echo isset($_SESSION["user"]) ? $_SESSION["user"] : "NOT SET"; ?>
Session city_mun: <?php echo isset($_SESSION["city_mun"]) ? $_SESSION["city_mun"] : "NOT SET"; ?>
GET value: <?php echo isset($_GET["value"]) ? $_GET["value"] : "NOT SET"; ?>
-->
<table width=100% class='t_voters' >
	<tr STYLE='FONT:bold 12px arial;color:#FFF'>
		<th ALIGN=center style='padding:10px;font:bold 15px arial;' >VOTER'S NAME</th>
		<th id=small ALIGN=center>SEX</th>
		<th id=small ALIGN=center>AGE</th>
		<th id=small ALIGN=center>PUROK</th>
		<th id=small ALIGN=center >ACTIONS</th>
	</tr>

	<?php
		$table=$_GET["table"];
		$votno=$_GET["id"];
		if($votno==""){
			$votno=0;
		}
		
		$value=strtoupper($_GET["value"]);
		$rep="<b style='color:#0014d0;background:#ffa0a0'>".$value."</b>";
		
		try {
			if (!empty($_SESSION["barangay"]) && $_SESSION["barangay"] !== "All barangays") {
				$stmt = $link->prepare("select * from voters v where v.city_mun=? and v.barangay=? and (v.ato LIKE CONCAT('%', ?, '%') or v.vname LIKE CONCAT('%', ?, '%')) order by v.vname limit 0,20");
				$stmt->execute([$_SESSION["city_mun"], $_SESSION["barangay"], $_GET["value"], $_GET["value"]]);
			} else {
				$stmt = $link->prepare("select * from voters v where v.city_mun=? and (v.ato LIKE CONCAT('%', ?, '%') or v.vname LIKE CONCAT('%', ?, '%')) order by v.vname limit 0,20");
				$stmt->execute([$_SESSION["city_mun"], $_GET["value"], $_GET["value"]]);
			}
			$ex = $stmt;
		} catch (Exception $e) {
			echo "<tr><td colspan=10><B style='color:red'>Database Query Error: " . htmlspecialchars($e->getMessage()) . "</B></td></tr>";
			echo "</table>";
			exit();
		}
		
		$ctr=1;
		
		if($ex->rowCount()<1){
			echo "<tr><td colspan=10><B style='color:red'>No records found! :(</B></td></tr>";
		}else{
			while($rs=$ex->fetch(PDO::FETCH_BOTH)){
				$found=false;
				$rem="";
				
				// 1. Check mce
				$stmt = $link->prepare('select * from mce where vin=?');
				$stmt->execute([$rs["vin"]]);
				$qq = $stmt;
				if($rss=$qq->fetch(PDO::FETCH_BOTH)){
					$found=true;
					$rem=" <a href='mceinfo.php?mce=".$rs["vin"]."'>- KAP</a>";
				} else {
					// 2. Check bce
					$stmt = $link->prepare('select * from bce where vin=?');
					$stmt->execute([$rs["vin"]]);
					$qq = $stmt;
					if($rss=$qq->fetch(PDO::FETCH_BOTH)){
						$found=true;
						$rem=" <a href='bceinfo.php?bce=".$rs["vin"]."'> - KAG</a>";
					} else {
						// 3. Check pl
						$stmt = $link->prepare('select * from pl where vin=?');
						$stmt->execute([$rs["vin"]]);
						$qq = $stmt;
						if($rss=$qq->fetch(PDO::FETCH_BOTH)){
							$found=true;
							$rem=" <a href='plinfo.php?pl=".$rs["vin"]."'> - PL</a>";
						} else {
							// 4. Check hl
							$stmt = $link->prepare('select * from hl where vin=?');
							$stmt->execute([$rs["vin"]]);
							$qq = $stmt;
							if($rss=$qq->fetch(PDO::FETCH_BOTH)){
								$found=true;
								$rem=" <a href='hlinfo.php?hl=".$rs["vin"]."'> - HL</a>";
							} else {
								// 5. Check hl_children
								$stmt = $link->prepare('select * from hl_children where vin=?');
								$stmt->execute([$rs["vin"]]);
								$qq = $stmt;
								if($rss=$qq->fetch(PDO::FETCH_BOTH)){
									$found=true;
									$rem=" - Member";
								} else {
									// 6. Check officer
									$stmt = $link->prepare('select * from officer where vin=?');
									$stmt->execute([$rs["vin"]]);
									$qq = $stmt;
									if($rss=$qq->fetch(PDO::FETCH_BOTH)){
										$found=true;
										$rem=" - Information Officer";
									} else {
										// 7. Check special
										$stmt = $link->prepare('select * from special where vin=?');
										$stmt->execute([$rs["vin"]]);
										$qq = $stmt;
										if($rss=$qq->fetch(PDO::FETCH_BOTH)){
											$found=true;
											$rem=" - SOL";
										} else {
											// 8. Check sollist
											$stmt = $link->prepare('select * from sollist where vin=?');
											$stmt->execute([$rs["vin"]]);
											$qq = $stmt;
											if($rss=$qq->fetch(PDO::FETCH_BOTH)){
												$found=true;
												$rem=" - Dili Ato";
											}
										}
									}
								}
							}
						}
					}
				}
				
				$birthDate = $rs["birth"];
				$age = "-";
				if (!empty($birthDate) && $birthDate !== '0000-00-00') {
					$birthObj = date_create($birthDate);
					if ($birthObj !== false) {
						$age = date_diff($birthObj, date_create('today'))->y;
					}
				}
				
				echo "
				<tr class='odd' id='q_tr_".$ctr."' style=\"height:0px;";
				if($found==true){
					echo "background:#222;color:#fff;";
				}
				echo ";font:12px arial;\"  >
					<td style='text-transform:capitalize;text-align:left' >".$ctr."). &nbsp;&nbsp;&nbsp;".$rs["vname"]." <b style='color:red' ></b></td>						
					<td>".$rs["sex"]."</td>
					<td>".$age."</td>
					<td>".$rs["address"]."</td>
					<td style='text-align:center' >";
				if($found==false) {
					echo "<input type='hidden' id='q_sel_sure_".$ctr."' value='Sure' />";
					echo "<input type='hidden' id='q_sel_rem_".$ctr."' value='' />";
					echo "<input alt='Add' type='image' src='images/accept.png' title='Click to Add' onclick=\"addmce('".$rs["0"]."',".$ctr.");\" />";
				}
				echo "</td>
				</tr>";
				
				$ctr++;
				if($ctr==21){
					break;
				}
			}
		}
	?>
</table>
