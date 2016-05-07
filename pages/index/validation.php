<h1> Validation</h1>
<p>To validate your identity, set the following code as 1 of your mastery pages' name, and click Validate:</p>
<h2><?=$code?></h2>
<form action="sql/check.php" method="POST" class="ajax">
	<input type="hidden" name="summoner_id" value="<?=$chk?>"/>
	<input type="hidden" name="server" value="<?=$_GET['server']?>"/>
	<input type="hidden" name="action" value="VALIDATION"/>
	<input type="submit" class="btn btn-info btn-lg btn-set" value="Validate"/>
</form>
