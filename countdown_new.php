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
?>
<div style="padding:5px"></div>
<div style='width:100px;color:#FFF;text-align:center;font-size:14px;padding:10px;width:278px;border-radius:5px;background:#202149'><b>SET NEW COUNTDOWN</b></div>

<form action="countdown_new.php" method="POST">

<table class="no_style">
	<tr style="background:transparent;display:none">
		<td style="border:0">ID<br/>
			<?php
				$ex = $link->query("SELECT * FROM countdown");
				while($rs=$ex->fetch(PDO::FETCH_BOTH)){
				$id="".$rs[0]."";
			?>
				<input style="padding:6px 5px 6px 5px;width:285px" name="id" type="text" value="<?php echo"".$id.""?>" />
			<?php } ?>
		</td>
	</tr>
	<tr style="background:transparent">		
   		<td style="border:0;color:#FFF"><br/>&nbsp;&nbsp;Countdown Tile<br/>
			<input style="padding:6px 5px 6px 5px;width:300px" required name="name" type="text" placeholder="Countdown Title" />
		</td>
	</tr>	
	<tr style="background:transparent">		
   		<td style="border:0;color:#FFF"><br/>&nbsp;&nbsp;Countdown Reached Text<br/>
			<input style="padding:6px 5px 6px 5px;width:300px" required name="target" type="text" placeholder="Countdown Reached Text" />
		</td>
	</tr>	
	<tr style="background:transparent">
	   	<td style="border:0;color:#FFF"><br/>&nbsp;&nbsp;Countdown Date<br/>
			<select required style="padding:5px; width:97px" name="year">
				<option value="" selected="1">Year</option>
				<option value="<?php echo date("Y") ?>"><?php echo date("Y") ?></option>				
				<option value="<?php echo date("Y")+1 ?>"><?php echo date("Y")+1 ?></option>
				<option value="<?php echo date("Y")+2 ?>"><?php echo date("Y")+2 ?></option>
				<option value="<?php echo date("Y")+3 ?>"><?php echo date("Y")+3 ?></option>
				<option value="<?php echo date("Y")+4 ?>"><?php echo date("Y")+4 ?></option>
				<option value="<?php echo date("Y")+5 ?>"><?php echo date("Y")+5 ?></option>
				<option value="<?php echo date("Y")+6 ?>"><?php echo date("Y")+6 ?></option>
				<option value="<?php echo date("Y")+7 ?>"><?php echo date("Y")+7 ?></option>
				<option value="<?php echo date("Y")+8 ?>"><?php echo date("Y")+8 ?></option>
				<option value="<?php echo date("Y")+9 ?>"><?php echo date("Y")+9 ?></option>
				<option value="<?php echo date("Y")+10 ?>"><?php echo date("Y")+10 ?></option>
				<option value="<?php echo date("Y")+11 ?>"><?php echo date("Y")+11 ?></option>
				<option value="<?php echo date("Y")+12 ?>"><?php echo date("Y")+12 ?></option>
				<option value="<?php echo date("Y")+13 ?>"><?php echo date("Y")+13 ?></option>
				<option value="<?php echo date("Y")+14 ?>"><?php echo date("Y")+14 ?></option>
				<option value="<?php echo date("Y")+15 ?>"><?php echo date("Y")+15 ?></option>
				<option value="<?php echo date("Y")+16 ?>"><?php echo date("Y")+16 ?></option>
				<option value="<?php echo date("Y")+17 ?>"><?php echo date("Y")+17 ?></option>
				<option value="<?php echo date("Y")+18 ?>"><?php echo date("Y")+18?></option>
				<option value="<?php echo date("Y")+19 ?>"><?php echo date("Y")+19?></option>
				<option value="<?php echo date("Y")+20 ?>"><?php echo date("Y")+20 ?></option>
				<option value="<?php echo date("Y")+21 ?>"><?php echo date("Y")+21 ?></option>
				<option value="<?php echo date("Y")+22 ?>"><?php echo date("Y")+22 ?></option>
				<option value="<?php echo date("Y")+23 ?>"><?php echo date("Y")+23 ?></option>
				<option value="<?php echo date("Y")+24 ?>"><?php echo date("Y")+24 ?></option>
				<option value="<?php echo date("Y")+25 ?>"><?php echo date("Y")+25 ?></option>
				<option value="<?php echo date("Y")+26 ?>"><?php echo date("Y")+26 ?></option>
				<option value="<?php echo date("Y")+27 ?>"><?php echo date("Y")+27 ?></option>
				<option value="<?php echo date("Y")+28 ?>"><?php echo date("Y")+28 ?></option>
				<option value="<?php echo date("Y")+29 ?>"><?php echo date("Y")+29 ?></option>
				<option value="<?php echo date("Y")+30 ?>"><?php echo date("Y")+30 ?></option>
				<option value="<?php echo date("Y")+31 ?>"><?php echo date("Y")+31 ?></option>
				<option value="<?php echo date("Y")+32 ?>"><?php echo date("Y")+32 ?></option>
				<option value="<?php echo date("Y")+33 ?>"><?php echo date("Y")+33 ?></option>
				<option value="<?php echo date("Y")+34 ?>"><?php echo date("Y")+34 ?></option>
				<option value="<?php echo date("Y")+35 ?>"><?php echo date("Y")+35 ?></option>
				<option value="<?php echo date("Y")+36 ?>"><?php echo date("Y")+36 ?></option>
				<option value="<?php echo date("Y")+37 ?>"><?php echo date("Y")+37 ?></option>
				<option value="<?php echo date("Y")+38 ?>"><?php echo date("Y")+38 ?></option>
				<option value="<?php echo date("Y")+39 ?>"><?php echo date("Y")+39 ?></option>
				<option value="<?php echo date("Y")+40 ?>"><?php echo date("Y")+40 ?></option>
				<option value="<?php echo date("Y")+41 ?>"><?php echo date("Y")+41 ?></option>
				<option value="<?php echo date("Y")+42 ?>"><?php echo date("Y")+42 ?></option>
				<option value="<?php echo date("Y")+43 ?>"><?php echo date("Y")+43 ?></option>
				<option value="<?php echo date("Y")+44 ?>"><?php echo date("Y")+44 ?></option>
				<option value="<?php echo date("Y")+45 ?>"><?php echo date("Y")+45 ?></option>
				<option value="<?php echo date("Y")+46 ?>"><?php echo date("Y")+46 ?></option>
				<option value="<?php echo date("Y")+47 ?>"><?php echo date("Y")+47 ?></option>
				<option value="<?php echo date("Y")+48 ?>"><?php echo date("Y")+48 ?></option>
				<option value="<?php echo date("Y")+49 ?>"><?php echo date("Y")+49 ?></option>
				<option value="<?php echo date("Y")+50 ?>"><?php echo date("Y")+50 ?></option>
			</select>
			<select required style="padding:5px; width:97px" name="month">
				<option value="" selected="1">Month</option>
				<option value="01">01-Jan</option>				
				<option value="02">02-Feb</option>
				<option value="03">03-Mar</option>
				<option value="04">04-Apr</option>
				<option value="05">05-May</option>
				<option value="06">06-Jun</option>
				<option value="07">07-Jul</option>
				<option value="08">08-Aug</option>
				<option value="09">09-Sep</option>
				<option value="10">10-Oct</option>
				<option value="11">11-Nov</option>
				<option value="12">12-Dec</option>
			</select>			
			<select required style="padding:5px; width:97px" name="day">
				<option value="" selected="1">Day</option>
				<option value="01">01</option>
				<option value="02">02</option>
				<option value="03">03</option>
				<option value="04">04</option>
				<option value="05">05</option>
				<option value="06">06</option>
				<option value="07">07</option>
				<option value="08">08</option>
				<option value="09">09</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="16">16</option>
				<option value="17">17</option>
				<option value="18">18</option>
				<option value="19">19</option>
				<option value="20">20</option>
				<option value="21">21</option>
				<option value="22">22</option>
				<option value="23">23</option>
				<option value="24">24</option>
				<option value="25">25</option>
				<option value="26">26</option>
				<option value="27">27</option>
				<option value="28">28</option>
				<option value="29">29</option>
				<option value="30">30</option>
				<option value="31">31</option>				
			</select>
		</td>
	<tr style="background:transparent">		
       	<td style="border:0">
			<select required style="padding:5px; width:97px" name="hour">
				<option value="" selected="1">Hour</option>
				<option value="00">00</option>
				<option value="01">01</option>
				<option value="02">02</option>
				<option value="03">03</option>
				<option value="04">04</option>
				<option value="05">05</option>
				<option value="06">06</option>
				<option value="07">07</option>
				<option value="08">08</option>
				<option value="09">09</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="16">16</option>
				<option value="17">17</option>
				<option value="18">18</option>
				<option value="19">19</option>
				<option value="20">20</option>
				<option value="21">21</option>
				<option value="22">22</option>
				<option value="23">23</option>
			</select>
			<select required style="padding:5px; width:97px" name="min">
				<option value="" selected="1">Mimute</option>
				<option value="00">00</option>
				<option value="01">01</option>
				<option value="02">02</option>
				<option value="03">03</option>
				<option value="04">04</option>
				<option value="05">05</option>
				<option value="06">06</option>
				<option value="07">07</option>
				<option value="08">08</option>
				<option value="09">09</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="16">16</option>
				<option value="17">17</option>
				<option value="18">18</option>
				<option value="19">19</option>
				<option value="20">20</option>
				<option value="21">21</option>
				<option value="22">22</option>
				<option value="23">23</option>
				<option value="24">24</option>
				<option value="25">25</option>
				<option value="26">26</option>
				<option value="27">27</option>
				<option value="28">28</option>
				<option value="29">29</option>
				<option value="30">30</option>
				<option value="31">31</option>				
				<option value="32">32</option>
				<option value="33">33</option>
				<option value="34">34</option>
				<option value="35">35</option>				
				<option value="36">36</option>				
				<option value="37">37</option>				
				<option value="38">38</option>				
				<option value="39">39</option>				
				<option value="40">40</option>				
				<option value="41">41</option>				
				<option value="42">42</option>				
				<option value="43">43</option>				
				<option value="44">44</option>				
				<option value="45">45</option>				
				<option value="46">46</option>				
				<option value="47">47</option>				
				<option value="48">48</option>				
				<option value="49">49</option>				
				<option value="50">50</option>				
				<option value="51">51</option>				
				<option value="52">52</option>				
				<option value="53">53</option>				
				<option value="54">54</option>				
				<option value="55">55</option>				
				<option value="56">56</option>				
				<option value="57">57</option>				
				<option value="58">58</option>				
				<option value="59">59</option>				
			</select>			
			<select required style="padding:5px; width:97px" name="sec">
				<option value="" selected="1">Second</option>
				<option value="00">00</option>
				<option value="01">01</option>
				<option value="02">02</option>
				<option value="03">03</option>
				<option value="04">04</option>
				<option value="05">05</option>
				<option value="06">06</option>
				<option value="07">07</option>
				<option value="08">08</option>
				<option value="09">09</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="16">16</option>
				<option value="17">17</option>
				<option value="18">18</option>
				<option value="19">19</option>
				<option value="20">20</option>
				<option value="21">21</option>
				<option value="22">22</option>
				<option value="23">23</option>
				<option value="24">24</option>
				<option value="25">25</option>
				<option value="26">26</option>
				<option value="27">27</option>
				<option value="28">28</option>
				<option value="29">29</option>
				<option value="30">30</option>
				<option value="31">31</option>				
				<option value="32">32</option>
				<option value="33">33</option>
				<option value="34">34</option>
				<option value="35">35</option>				
				<option value="36">36</option>				
				<option value="37">37</option>				
				<option value="38">38</option>				
				<option value="39">39</option>				
				<option value="40">40</option>				
				<option value="41">41</option>				
				<option value="42">42</option>				
				<option value="43">43</option>				
				<option value="44">44</option>				
				<option value="45">45</option>				
				<option value="46">46</option>				
				<option value="47">47</option>				
				<option value="48">48</option>				
				<option value="49">49</option>				
				<option value="50">50</option>				
				<option value="51">51</option>				
				<option value="52">52</option>				
				<option value="53">53</option>				
				<option value="54">54</option>				
				<option value="55">55</option>				
				<option value="56">56</option>				
				<option value="57">57</option>				
				<option value="58">58</option>				
				<option value="59">59</option>				
			</select>
		</td>
	<tr/>
	<tr style="background:transparent">			
    	<td align="center" width="50%" style="border:0"><br/>
			<input style="padding:5px; width:65px" type="SUBMIT" value="Save" name='update' style="cursor:pointer">&nbsp;&nbsp;
			<a href="./"><input style="padding:5px; width:65px" type="button" value="Cancel"></a>
		</td>
	</tr>	
</table>

</form>
