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
	else echo "<div class='output'>Validation NOT successful</div>";
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
	echo "<div class='noalert'></div>";
	$sql=sql("SELECT player1 FROM match_found WHERE (player1=$_SESSION[id] OR player2=$_SESSION[id]) AND (p1a=0 OR p2a=0)",1);
	if($sql)
	{
		sql("DELETE FROM match_found WHERE player1=$_SESSION[id] OR player2=$_SESSION[id]");
		echo "<div class='output'>0</div>";
	}
	else{
		$sql=sql("SELECT player1 FROM match_found WHERE player1=$_SESSION[id] OR player2=$_SESSION[id]",1);
		if($sql){
			echo "<div class='output'>1</div>";
			$sql=sql("SELECT player1,player2 FROM match_found WHERE player1=$_SESSION[id] OR player2=$_SESSION[id]",2);
			if($sql[0]['player1']!=$_SESSION['id']) $_SESSION['oppo']=$sql[0]['player1'];
			else $_SESSION['oppo']=$sql[0]['player2'];
			$sql=sql("SELECT p1 FROM game WHERE (p1=$_SESSION[id] OR p2=$_SESSION[id]) AND game_end=0",1);
			if(!$sql)
			{
				$_SESSION['p1']=true;
				$num=sql("SELECT COUNT(*) AS num FROM questions",2)[0]['num'];
				$_SESSION['q']=randnum(1,$num,5,true);
				sql("INSERT INTO game (questions,p1,p2,timestamp) VALUES ('".implode(",",$_SESSION['q'])."',$_SESSION[id],$_SESSION[oppo],".time().")");
				$_SESSION['game']=sql("SELECT id FROM game WHERE (p1=$_SESSION[id] OR p2=$_SESSION[id]) AND game_end=0",2)[0]['id'];
			}
			else
			{
				$_SESSION['p1']=false;
				$_SESSION['game']=sql("SELECT id FROM game WHERE (p1=$_SESSION[id] OR p2=$_SESSION[id]) AND game_end=0",2)[0]['id'];
				$_SESSION['q']=explode(",",sql("SELECT questions FROM game WHERE id=$_SESSION[game]",2)[0]['questions']);
			}
		}
		else echo "<div class='output'>0</div>";
	}
}
elseif($_POST['action']=="UPDATE_LAST_ON")
{
	sql("UPDATE league_mastery_game SET last_on=".time()." WHERE id=$_SESSION[id]");
	echo "<div class='noalert'></div>";
}
?>
