<section id="home-sec">
	<div class="overlay text-center">
		<h1>League of Legends Mastery Game</h1>
		<p>Welcome to the League og Legends Mastery Game!</p>
	</div>
</section>
<section id="search-domain">
	<div class="container">
		<div class="row">
			<form>
				<input type="hidden" name="p" value="index"/>
				<div class="col-md-6">
					<input type="text" name="name" class="form-control input-cls"/>
				</div>
				<div class="col-md-2">
					<select name="server" class="input-cls">
						<?php
						$sql=sql("SELECT id,server,name FROM servers",2);
						foreach($sql as $num=>$arr) echo "<option value='$arr[id]'>".strtoupper($arr["server"])."</option>\n";
						?>
					</select>
				</div>
				<div class="col-md-1">
					<input type="submit" class="btn btn-info btn-lg btn-set" value="Login/Register"/>
				</div>
			</form>
		</div>
	</div>
</section>
