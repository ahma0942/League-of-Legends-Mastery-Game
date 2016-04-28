<?php
session_start();
include "../db.php";
include "../php/funcs.php";

if(!isset($_POST['action'])) exit;

if($_POST['action']=="VALIDATION")
{
	if(!isset($_POST['summoner_id']) OR !isset($_POST['server'])) die("Couldn't find summoner id/server");
	$sql=sql("SELECT summoner_id FROM league_mastery_game WHERE summoner_id='".esc($_POST['summoner_id'])."'",1);
	if($sql) $sql=sql("SELECT validation,summoner_name FROM league_mastery_game WHERE summoner_id='".esc($_POST['summoner_id'])."'",2);
	$sql2=sql("SELECT server FROM servers WHERE id='".$_POST['server']."'",2);
	$chk=ValidateMasteryPage($_POST['summoner_id'],$sql2[0]['server'],$sql[0]['validation']);
	if($chk)
	{
		echo "<div class='output'>Validation successful</div>\n";
		echo "<div class='redir'>./?p=index</div>";
		$login=CreateLogin($sql[0]['summoner_name'].":".$_POST['server']);
		$_SESSION['login']=$login;
		echo "<div class='cookie' name='Login' exp='".(60*60*24*365*100)."'>$login</div>";
	}
	else echo "Validation NOT successful";
}
?>
