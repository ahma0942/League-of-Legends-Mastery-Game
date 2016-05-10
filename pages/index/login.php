<div id="main">
	<div style="padding-left:20px;padding-right:20px;word-wrap: break-word;">
		<h1>League of Legends Mastery Game</h1>
		<p>Welcome to the League og Legends Mastery Game!</p>
		<form>
			<input type="hidden" name="p" value="index"/>
			<input type="text" name="name"/><br/>
			<select name="server">
				<?php
				$sql=sql("SELECT id,server,name FROM servers",2);
				foreach($sql as $num=>$arr) echo "<option value='$arr[id]'>".strtoupper($arr["server"])."</option>\n";
				?>
			</select><br/>
			<input type="submit" class="btn" value="Login/Register"/>
		</form>
	</div>
</div>
