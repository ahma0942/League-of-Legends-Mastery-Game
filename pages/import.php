<?php
if(isset($_POST['text']))
{
	echo "<h2><pre>";
	$str=explode("\n",$_POST['text']);
	foreach($str as $line)
	{
		$row=explode(";",$line);
		if($row[0][0]=='"' && $row[0][strlen($row[0])-1]=='"') $row[0]="Which champion has the following quote, <quote>".$row[0]."</quote>";
		$out=[
			"Q"=>$row[0],
			"C"=>array(
				1=>$row[1],
				2=>$row[2],
				3=>$row[3],
				4=>$row[4]
			),
			"A"=>$row[5]
		];
		sql("INSERT INTO questions (json) VALUES ('".esc(json_encode($out))."')");
	}
	print_r($out);
	echo "</pre></h2>";
}
?>
<br/><br/><br/>
<form method="POST">
	<textarea name="text" rows=30 cols=200><?=(isset($_POST['text'])?$_POST['text']:"")?></textarea><br/>
	<input type="submit" value="Send"/>
</form>
