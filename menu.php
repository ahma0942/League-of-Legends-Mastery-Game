<div id="menu">
	<ul>
		<li><a href="?p=index"<?=($_GET['p']=="index"?" class='active-menu-item'":"")?>>Play</a></li>
		<li><a href="?p=stats"<?=($_GET['p']=="stats"?" class='active-menu-item'":"")?>>Statistics</a></li>
		<?php
		if(isset($_SESSION['id'])) echo "<li><a href='?p=profile".($_GET['p']=="profile"?" class='active-menu-item'":"")."'>Profile</a></li>";
		?>
	</ul>
</div>
