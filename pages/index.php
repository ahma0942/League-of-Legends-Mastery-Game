<?php
if(isset($_COOKIE['Login']))
{
	$chk=ValidateLogin($_COOKIE['Login']);
	alert($chk);
	if(is_array($chk)){
		include "/pages/index/play.php";
	}
	else{
		unset($_COOKIE['Login']);
		setcookie('Login',null,-1,'/');
		include "/pages/index/login.php";
	}
}
else
{
	if(isset($_GET['name']) AND isset($_GET['server']))
	{
		$sql=sql("SELECT server FROM servers WHERE id='$_GET[server]'",2);
		$chk=validateSummoner($_GET['name'],$sql[0]['server']);
		if($chk===false) die("<br/><br/><br/>The given summoner name doesn't exist in the given server");
		$sql=sql("SELECT id FROM league_mastery_game WHERE summoner_name='".esc($_GET['name'])."'",1);
		$code=rand_str(10);
		if($sql) $sql=sql("UPDATE league_mastery_game SET validation='$code' WHERE summoner_name='".esc($_GET['name'])."'");
		else $sql=sql("INSERT INTO league_mastery_game (summoner_id,summoner_name,server_id,mastery,timestamp,validation) VALUES ('$chk','$_GET[name]','$_GET[server]','0','".time()."','$code')");
		if(!$sql) die("Something went wrong, please try again");
		include "/pages/index/validation.php";
	}
	else include "/pages/index/login.php";
}
exit;
if(!isset($_COOKIE['Login']) OR !$_GET['name'])
{
	
}
else
{
	$chk=ValidateLogin($_COOKIE['Login']);
}
?>
