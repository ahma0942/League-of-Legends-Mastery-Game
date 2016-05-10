<br/>
<table cellspacing="0" cellpadding="0" style="margin:auto;" width="60%">
	<?php
	$sql=sql("SELECT id,p1,p2,questions FROM game WHERE p1=$_SESSION[id] OR p2=$_SESSION[id]",2);
	$i=-1;
	foreach($sql as $row)
	{
		$i++;
		$a=array();
		$q=explode(",",$sql[$i]['questions']);
		foreach($q AS $qs) $a[]=json_decode(sql("SELECT json FROM questions WHERE id=$qs",2)[0]['json'])->A;
		if($sql[$i]['p1']==$_SESSION['id']) $oppo=$sql[$i]['p2'];
		else $oppo=$sql[$i]['p1'];
		$name=sql("SELECT summoner_name FROM league_mastery_game WHERE id=$oppo",2)[0]['summoner_name'];
		$you=sql("SELECT a1,a2,a3,a4,a5 FROM answers WHERE gid=".$sql[$i]['id']." AND uid=$oppo",2);
		$me=sql("SELECT a1,a2,a3,a4,a5 FROM answers WHERE gid=".$sql[$i]['id']." AND uid=$_SESSION[id]",2);
		
		$pts=[
			"me"=>0,
			"you"=>0
		];
		for($t=1;$t<=5;$t++)
		{
			if($you[0]["a$t"]==$a[$t-1]) $pts['you']++;
			if($me[0]["a$t"]==$a[$t-1]) $pts['me']++;
		}
		
		if($pts['you']>$pts['me']) $o="red";
		elseif($pts['you']<$pts['me']) $o="green";
		else $o="orange";
		
		echo "<tr style='background-color:rgb(244,235,206);'>";
			echo "<td rowspan=2 style='background-color:$o;border-bottom:1px solid black;' width='20px'></td>";
			echo "<td rowspan=2 style='vertical-align:top;padding:20px;color:black;border-bottom:1px solid black;'>$_SESSION[summoner_name]</td>";
			for($t=1;$t<=5;$t++) echo "<td><img style='width:50px;' src='img/".($me[0]["a$t"]==$a[$t-1]?"right":"wrong").".png'/></td>";
			echo "<td rowspan=2 style='vertical-align:bottom;padding:20px;color:black;border-bottom:1px solid black;'>$name</td>";
		echo "</tr><tr style='background-color:rgb(244,235,206);'>";
			for($t=1;$t<=5;$t++) echo "<td style='border-bottom:1px solid black;'><img style='width:50px;' src='img/".($you[0]["a$t"]==$a[$t-1]?"right":"wrong").".png'/></td>";
		echo "</tr>";
	}
	?>
</table>
