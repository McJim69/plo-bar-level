<script type='text/javascript' src='scripts/jquery.counter.js?ver=1.10.2'></script>
<script type="text/javascript" src="scripts/jquery.countdown.js"></script>

<?php
	$ex = $link->query("SELECT * FROM countdown");
	while($rs=$ex->fetch(PDO::FETCH_BOTH)){
	$id="".$rs[0]."";
	$name="".$rs['name']."";
	$target="".$rs['target']."";
	$y="".$rs['year']."";	
	$m="".$rs['month']."";
	$d="".$rs['day']."";
	$h="".$rs['hour']."";
	$i="".$rs['min']."";
	$s="".$rs['sec']."";	
?>
<script type="text/javascript">
var $j = jQuery.noConflict();
$j(function () {
var austDay = new Date("<?php echo"".$y." ".$m." ".$d." ".$h.":".$i.":".$s.""?>");
	$j('#defaultCountdown').countdown({until: austDay, layout: '{dn} {dl}, {hn} {hl}, {mn} {ml}, and {sn} {sl}'});
	$j('#year').text(austDay.getFullYear());
	});
</script>
<center>
	<div style="width:510px;background:#375ba2;border-radius:5px;box-shadow:0 2px 5px #333;padding:5px;margin-top:10px;z-index:1">
	<?php
		$date=$y."-".$m."-".$d." ".$h.":".$i.":".$s;
			if($date<date('Y-m-d h:i:s')){
				
				echo"<div style='color:#FFF;font-size:20px'>".$name."</div>";
				
				echo"<small style='color:#FFF'><i>".$date."</i></small>";
				
				echo"<div style='font-size:20px;width:90%;margin:5px;color:red;text-transform:uppercase;background:#fff;padding:5px;border-radius:3px;box-shadow:0 2px 5px #333'>";
				echo"<marquee style='margin-left:5px;margin-right:5px' behavior='scroll' direction='left' scrollamount='2' scrolldelay='50' truespeed onmouseover=this.stop() onmouseout=this.start()>";
				echo"<b>".$target."</b>";
				echo"</marquee>";
				echo"</div>";

				echo"<div style='padding:5px;opacity:0.8'>";
				echo"<a rel='facebox' href='countdown_update.php'><input type='button' value='UpDate Countdown'/></a>&nbsp;&nbsp;";
				echo"<a rel='facebox' href='countdown_new.php'><input type='button' value='SetNew Countdown'/></a>";
				echo"</div>";
				
			}else{
			
				echo"<div style='color:#bbb;font-size:20px'>".$name."</div>";
				echo"<small style='color:#bbb'><i>Estimated time remaining:</i></small>";
				echo"<div style='font-weight:bold;width:90%;margin:5px;color:#FFF;background:#202149;padding:5px;border-radius:3px;box-shadow:0 2px 5px #333' id='defaultCountdown'></div>";
				
				echo"<div style='padding:5px;opacity:0.8'>";
				echo"<a rel='facebox' href='countdown_update.php'><input type='button' value='UpDate Countdown'/></a>&nbsp;&nbsp;";
				echo"<a rel='facebox' href='countdown_new.php'><input type='button' value='SetNew Countdown'/></a>";
				echo"</div>";
			}
		}
	?>
		
	</div><!--end counter-->
</center>
