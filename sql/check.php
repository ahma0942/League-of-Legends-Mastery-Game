<?php
session_start();
include "../db.php";
include "../php/vars.php";
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
elseif($_POST['action']=="START_QUEUE")
{
	sql("UPDATE league_mastery_game SET queue=1 WHERE id='$_SESSION[id]'");
	$fm=FoundMatch();
	echo "<div class='output'>$fm</div>";
	echo "<div class='return'>".time()."</div>";
	echo "<div class='noalert'></div>";
}
elseif($_POST['action']=="FOUND_MATCH")
{
	$fm=FoundMatch();
	echo "<div class='output'>$fm</div>";
	if($fm!=1) echo "<div class='return'>".time()."</div>";
	else
	{
		$sql=sql("SELECT timestamp FROM match_found WHERE player1=$_SESSION[id] OR player2=$_SESSION[id]",2);
		echo "<div class='return'>".$sql[0]['timestamp']."</div>";
	}
	echo "<div class='noalert'></div>";
}
elseif($_POST['action']=="STOP_QUEUE")
{
	sql("UPDATE league_mastery_game SET queue=0 WHERE id=$_SESSION[id]");
	echo "<div class='noalert'></div>";
}
elseif($_POST['action']=="ACCEPT")
{
	$sql=sql("SELECT player1,player2 FROM match_found WHERE player1=$_SESSION[id] OR player2=$_SESSION[id]",2);
	if($sql[0]['player1']==$_SESSION['id']) $ext="p1a";
	else $ext="p2a";
	sql("UPDATE match_found SET $ext=1 WHERE player".substr($ext,1,1)."=$_SESSION[id]");
	echo "<div class='output'>test</div>";
	echo "<div class='noalert'></div>";
}
elseif($_POST['action']=="UPDATE_STATUS")
{
	$sql=sql("SELECT player1 FROM match_found WHERE (player1=$_SESSION[id] OR player2=$_SESSION[id]) AND (p1a=0 OR p2a=0)",1);
	if($sql){
		sql("DELETE FROM match_found WHERE player1=$_SESSION[id] OR player2=$_SESSION[id]");
		echo "<div class='output'>0</div>";
	}
	else{
		$sql=sql("SELECT player1 FROM match_found WHERE player1=$_SESSION[id] OR player2=$_SESSION[id]",1);
		if($sql){
			echo "<div class='output'>1</div>";
			$sql=sql("SELECT player1,player2 FROM match_found WHERE player1=$_SESSION[id] OR player2=$_SESSION[id]",2);
			if($sql[0]['player1']!=$_SESSION['id']) $_SESSION['game']=$sql[0]['player1'];
			else $_SESSION['game']=$sql[0]['player2'];
		}
		else echo "<div class='output'>0</div>";
	}
	echo "<div class='noalert'></div>";
}
?>
