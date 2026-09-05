<?php 
require("connect.php");

	if(isset($_POST['update'])){	
		$id = $_POST['id'];
		$name = $_POST['name'];
		$target = $_POST['target'];
		$year = $_POST['year'];
		$month = $_POST['month'];
		$day = $_POST['day'];
		$hour = $_POST['hour'];
		$min = $_POST['min'];
		$sec = $_POST['sec'];

	$stmt = $link->prepare('UPDATE countdown set
		id = ?,	
		name = ?, 
		target = ?, 
		year = ?,
		month = ?,
		day = ?,
		hour = ?,
		min = ?,
		sec = ? where id = ?');
	if($stmt->execute([$id, $name, $target, $year, $month, $day, $hour, $min, $sec, $id])){
		echo"<script>alert('New Countdown was Set Succesfully!');
		window.location.href = './';</script>";
			
		}else
			
		echo"<script>alert('Ooppss! Cannot Set New Countdown');
		window.location.href = './';</script>";	
	}	

$ex = $link->query("SELECT * FROM countdown");
while($rs=$ex->fetch(PDO::FETCH_BOTH)){
	$id = "".$rs[0]."";
	$name = "".$rs['name']."";
	$target = "".$rs['target']."";
	$y = "".$rs['year']."";	
	$m = "".$rs['month']."";
	$d = "".$rs['day']."";
	$h = "".$rs['hour']."";
	$i = "".$rs['min']."";
	$s = "".$rs['sec']."";	
?>
<div style="padding:5px"></div>
<div style='width:100px;color:#FFF;text-align:center;font-size:14px;padding:10px;width:278px;border-radius:5px;background:#202149'><b>UPDATE COUNTDOWN</b></div>

<form action="countdown_update.php" method="POST">
<table class="no_style">
	<tr style="background:transparent;display:none">
		<td style="border:0">ID<br/>
			<input style="padding:6px 5px 6px 5px;width:300px" name="id" type="text" value="<?php echo"".$id.""?>" />
		</td>
	</tr>	
	<tr style="background:transparent">
   		<td style="border:0;color:#FFF"><br/>&nbsp;&nbsp;Countdown Tile:<br/>
			<input style="padding:6px 5px 6px 5px;width:300px" required name="name" type="text" value="<?php echo"".$name.""?>" />
		</td>
	</tr>	
	<tr style="background:transparent">
   		<td style="border:0;color:#FFF"><br/>&nbsp;&nbsp;Countdown Reached Text:<br/>
			<input style="padding:6px 5px 6px 5px;width:300px" required name="target" type="text" value="<?php echo"".$target.""?>" />
		</td>
	</tr>	
	<tr style="background:transparent">
		<td style="border:0;color:#FFF"><br/>&nbsp;&nbsp;Countdown Date:<br/>
			<input style="padding:6px 5px 6px 5px;width:97px" required name="year" type="text" value="<?php echo"".$y.""?>" />
			<input style="padding:6px 5px 6px 5px;width:97px" required name="month" type="text" value="<?php echo"".$m.""?>" />
			<input style="padding:6px 5px 6px 5px;width:97px" required name="day" type="text" value="<?php echo"".$d.""?>" />			
		</td>
	</tr>	
	<tr style="background:transparent">		
		<td style="border:0">
			<input style="padding:6px 5px 6px 5px;width:97px" required name="hour" type="text" value="<?php echo"".$h.""?>" />
			<input style="padding:6px 5px 6px 5px;width:97px" required name="min" type="text" value="<?php echo"".$i.""?>" />
			<input style="padding:6px 5px 6px 5px;width:97px" required name="sec" type="text" value="<?php echo"".$s.""?>" />			
		</td>
    </tr>
	<tr style="background:transparent">			
		<td align="center" width="50%" style="border:0;margin-bottom:0px"><br/>
			<input style="padding:5px; width:65px" type="SUBMIT" value="Save" name='update' style="cursor:pointer">&nbsp;&nbsp;
			<a href="./"><input style="padding:5px; width:65px" type="button" value="Cancel"></a>
		</td>
	</tr>				
</table>

</form>

<?php } ?>