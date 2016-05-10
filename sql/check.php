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
	echo "<div class='noalert'></div>";
	sql("UPDATE league_mastery_game SET queue=1 WHERE id='$_SESSION[id]'");
	$fm=FoundMatch();
	if($fm==1){
		$_SESSION['game']=array();
		$_SESSION['game']['p1']=false;
	}
	echo "<div class='output'>$fm</div>";
	echo "<div class='return'>".time()."</div>";
}
elseif($_POST['action']=="FOUND_MATCH")
{
	echo "<div class='noalert'></div>";
	if(!isset($_SESSION['game']))
	{
		$_SESSION['game']=array();
		$_SESSION['game']['p1']=true;
	}
	$fm=FoundMatch();
	echo "<div class='output'>$fm</div>";
	if($fm!=1) echo "<div class='return'>".time()."</div>";
	else
	{
		$sql=sql("SELECT timestamp FROM match_found WHERE player1=$_SESSION[id] OR player2=$_SESSION[id]",2);
		echo "<div class='return'>".$sql[0]['timestamp']."</div>";
	}
}
elseif($_POST['action']=="STOP_QUEUE")
{
	sql("UPDATE league_mastery_game SET queue=0 WHERE id=$_SESSION[id]");
	echo "<div class='noalert'></div>";
	if(!isset($_SESSION['game']['q'])) unset($_SESSION['game']);
}
elseif($_POST['action']=="ACCEPT")
{
	$sql=sql("SELECT player1,player2 FROM match_found WHERE player1=$_SESSION[id] OR player2=$_SESSION[id]",2);
	if($sql[0]['player1']==$_SESSION['id']) $ext="p1a";
	else $ext="p2a";
	sql("UPDATE match_found SET $ext=1 WHERE player".substr($ext,1,1)."=$_SESSION[id]");
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
			$sql=sql("SELECT player1,player2 FROM match_found WHERE player1=$_SESSION[id] OR player2=$_SESSION[id]",2);
			if($sql[0]['player1']!=$_SESSION['id']) $_SESSION['game']['oppo']=$sql[0]['player1'];
			else $_SESSION['game']['oppo']=$sql[0]['player2'];
			if($_SESSION['game']['p1']===true)
			{
				$num=sql("SELECT COUNT(*) AS num FROM questions",2)[0]['num'];
				$_SESSION['game']['q']=randnum(1,$num,5,true);
				foreach($_SESSION['game']['q'] AS $nums){
					$sql=json_decode(sql("SELECT json FROM questions WHERE id=$nums",2)[0]['json']);
					$_SESSION['game']['a'][]=[$sql->A,$sql->C->{$sql->A}];
				}
				$_SESSION['game']['timestamp']=time();
				sql("INSERT INTO game (questions,p1,p2,timestamp) VALUES ('".implode(",",$_SESSION['game']['q'])."',$_SESSION[id],".$_SESSION['game']['oppo'].",".time().")");
				$_SESSION['game']['id']=sql("SELECT id FROM game WHERE (p1=$_SESSION[id] OR p2=$_SESSION[id]) AND game_end=0",2)[0]['id'];
				echo "<div class='output'>1</div>";
			}
			else
			{
				$sql=sql("SELECT p1 FROM game WHERE (p1=$_SESSION[id] OR p2=$_SESSION[id]) AND game_end=0",1);
				$i=0;
				while(!$sql){
					$i++;
					sleep(1);
					if($i==5){
						echo "<div class='output'>0</div>";
						exit;
					}
					$sql=sql("SELECT p1 FROM game WHERE (p1=$_SESSION[id] OR p2=$_SESSION[id]) AND game_end=0",1);
				}
				$sql=sql("SELECT p1 FROM game WHERE (p1=$_SESSION[id] OR p2=$_SESSION[id]) AND game_end=0",1);
				$sql=sql("SELECT id,timestamp FROM game WHERE (p1=$_SESSION[id] OR p2=$_SESSION[id]) AND game_end=0",2);
				$_SESSION['game']['id']=$sql[0]['id'];
				$_SESSION['game']['timestamp']=$sql[0]['timestamp'];
				$_SESSION['game']['q']=explode(",",sql("SELECT questions FROM game WHERE id=".$_SESSION['game']['id'],2)[0]['questions']);
				foreach($_SESSION['game']['q'] AS $nums){
					$sql=json_decode(sql("SELECT json FROM questions WHERE id=$nums",2)[0]['json']);
					$_SESSION['game']['a'][]=[$sql->A,$sql->C->{$sql->A}];
				}
				echo "<div class='output'>1</div>";
			}
		}
		else echo "<div class='output'>0</div>";
	}
}
elseif($_POST['action']=="OPPONENT_ACTIVE")
{
	echo "<div class='noalert'></div>";
	if(!isset($_SESSION['id']) OR !isset($_SESSION['game'])) echo "<div class='refresh'></div>";
	else
	{
		updateLastOn();
		if(sql("SELECT id FROM league_mastery_game WHERE id=".$_SESSION['game']['oppo']." AND last_on<".(time()-10),1))
		{
			sql("UPDATE game SET winner='".$_SESSION['id']."', ff='".$_SESSION['game']['oppo']."' WHERE id='".$_SESSION['game']['id']."'");
			echo "<div class='output'>0</div>";
		}
		else echo "<div class='output'>1</div>";
	}
}
elseif($_POST['action']=="PING")
{
	echo "<div class='noalert'></div>";
	if(!isset($_SESSION['id'])) echo "<div class='refresh'></div>";
	else updateLastOn();
}
elseif($_POST['action']=="START_GAME")
{
	echo "<div class='noalert'></div>";
	if($ARR['time']-(time()-$_SESSION['game']['timestamp'])<=0) echo "<div class='output'>1</div>";
	else echo "<div class='output'>0</div>";
}
elseif($_POST['action']=="QUESTION")
{
	if(!isset($_SESSION['game']['qnum'])) $_SESSION['game']['qnum']=0;
	if((1+(isset($_SESSION['game']['qnum'])?$_SESSION['game']['qnum']:0))*$ARR['time']-(time()-$_SESSION['game']['timestamp'])>0) echo "CHEATER CHEATER CHEATER!!!";
	else
	{
		echo "<div class='noalert'></div>";
		if(!isset($_SESSION['game']['timestamp']))
		{
			unset($_SESSION['game']);
			die("<div class='refresh'></div>");
		}
		$i=1;
		while($i<=5 AND time()-($_SESSION['game']['timestamp']+$i*$ARR['time'])>=15) $i++;
		if($i==6){
			$_SESSION['game']['game_over']=true;
			sql("UPDATE game SET game_end=".time()." WHERE id=".$_SESSION['game']['id']);
			echo "<div class='output'>2</div>";
			exit;
		}
		$_SESSION['game']['qnum']=$i-1;
		if($_SESSION['game']['qnum']<5)
		{
			if($_SESSION['game']['qnum']==0 AND !isset($_SESSION['game']['aid']))
			{
				sql("INSERT INTO answers (gid,uid) VALUES (".$_SESSION['game']['id'].",$_SESSION[id])");
				$_SESSION['game']['aid']=sql("SELECT aid FROM answers WHERE gid=".$_SESSION['game']['id']." AND uid=$_SESSION[id]",2)[0]['aid'];
			}
			$json=json_decode(sql("SELECT json FROM questions WHERE id=".$_SESSION['game']['q'][$_SESSION['game']['qnum']],2)[0]['json']);
			$json->A="CHEATER CHEATER CHEATER!!!";
			$json=json_encode($json,JSON_UNESCAPED_SLASHES);
			echo "<div class='output'>1</div>";
			echo "<div class='return'>".$json."</div>";
		}
		else echo "<div class='output'>0</div>";
	}
}
elseif($_POST['action']=="ANSWER")
{
	echo "<div class='noalert'></div>";
	if(isset($_POST['answer']))
	{
		if(!is_numeric($_POST['answer']) OR $_POST['answer']<1 OR $_POST['answer']>4) exit;
		else $answer=$_POST['answer'];
		if($_SESSION['game']['qnum']>=0 AND $_SESSION['game']['qnum']<=4)
		{
			sql("UPDATE answers SET a".($_SESSION['game']['qnum']+1)."='$answer', t".($_SESSION['game']['qnum']+1)."='".time()."' WHERE aid='".$_SESSION['game']['aid']."'");
			echo "<div class='output'>1</div>";
		}
		else echo "<div class='output'>0</div>";
	}
	else echo "<div class='output'>0</div>";
}
elseif($_POST['action']=="NEW_GAME")
{
	echo "<div class='noalert'></div>";
	unset($_SESSION['game']);
}
?>
