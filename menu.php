<div id="menu">
	<ul>
		<li><a href="?p=index">Play</a></li>
		<li><a href="?p=stats">Statistics</a></li>
		<?php
		if(isset($_SESSION['id'])) echo "<li><a href='?p=profile'>Profile</a></li>";
		?>
		<li><a href="?p=help">Help</a></li>
	</ul>
</div>
