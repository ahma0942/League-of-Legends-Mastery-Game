<?php
if(!isset($_SESSION['game']))
{
	?>
	<script>
	var timer;
	var check;
	var res;
	var queue=7;

	function start_queue()
	{
		$("#queue").html('Stop Queue');
		ajax("sql/check.php",{action:"START_QUEUE"},"POST",function(res){
			check_queue(res);
		});
		timer=setInterval(function(){
			ajax("sql/check.php",{action:"FOUND_MATCH"},"POST",function(res){
				check_queue(res);
			});
		},3000);
	}

	function check_queue(res)
	{
		if(res[0]=="1"){
			clearInterval(timer);
			$("#dlgmsg").html(dlg("<p>Ready to play?</p><div class='counter'>"+((parseInt(res[1])+queue)-time())+"</div><div class='buttons'><button class='btn' onclick=\"$(this).parent().html('Accepted');accept();\"><img src='img/yes.png'/></button><button class='btn2' onclick=\"$(this).parent().html('Declined');decline();\"><img src='img/no.png'/></button></div>","Match found",1));
			
			counter(".counter",((parseInt(res[1])+queue)-time()),0,function(){
				update();
			},1);
		}
	}

	function stop_queue()
	{
		clearInterval(timer);
		ajax("sql/check.php",{action:"STOP_QUEUE"},"POST",function(res){});
		$("#queue").html('Queue Up');
	}

	function decline()
	{
		check=0;
		stop_queue();
	}

	function accept()
	{
		check=1;
		ajax("sql/check.php",{action:"ACCEPT"},"POST",function(res){});
	}

	function update()
	{
		ajax("sql/check.php",{action:"UPDATE_STATUS"},"POST",function(res){
			document.getElementById('dlg1').style.display='none';
			if(res=="1"){
				stop_queue();
				start_game();
			}
			else if(check!=1) stop_queue();
			else if(check==1) start_queue();
		});
	}

	function start_game()
	{
		location.reload();
	}
	</script>
	<div id='main'>
		<div id="dlgmsg"></div>
		<div style="width:35%;margin:auto;">
			<h2>Your current champion mastery is <span class='num'><?=$_SESSION['mastery']?></span></h2>
			<h2>Play some more to improve your skills!</h2>
			<div style="width:160px;margin:auto;">
				<button id='queue' class="btn" onclick="if($(this).html()!='Queue Up') stop_queue(); else start_queue();">Queue Up</button>
			</div>
		</div>
	</div>
	<?php
}
elseif(!isset($_SESSION['game']['game_over']))
{
	if($_SESSION['game']['p1']===true)
	{
		if(!isset($_SESSION['game']['done'])) $_SESSION['game']['done']=false;
		if($_SESSION['game']['done']===false)
		{
			sql("DELETE FROM match_found WHERE player1=$_SESSION[id] OR player2=$_SESSION[id]");
			$_SESSION['game']['done']=true;
		}
	}
	?>
	<script>
	var ARR_TIME=<?=$ARR['time']?>;
	var SESSION_TIMESTAMP=<?=$_SESSION['game']['timestamp']?>;
	var QNUM=<?=(isset($_SESSION['game']['qnum'])?$_SESSION['game']['qnum']:0)?>;
	
	$(document).ready(function(){
		ajax("sql/check.php",{action:"PING"},"POST",function(res){});
		setInterval(function(){
			ajax("sql/check.php",{action:"OPPONENT_ACTIVE"},"POST",function(res){
				if(res=="0") game_over();
			});
		},5000);
		counter(".counter",<?=($ARR['time']-(time()-$_SESSION['game']['timestamp'])-1)?>,0,function(){
			start_game();
		},1);
		$("button").on("click",function(){
			ajax("sql/check.php",{action:"ANSWER",answer:$(this).attr("num")},"POST",function(res){
				if(res=="1") $(".questions").prop("disabled",true);
				else alert("Something went wrong, please try again.");
			});
		});
	});
	
	function start_game()
	{
		ajax("sql/check.php",{action:"START_GAME"},"POST",function(res){
			if(res=="1"){
				$(".intro").html("");
				$(".head").show();
				questions();
			}
		});
	}
	
	function timer_reset()
	{
		$(".timer").html((1+QNUM)*ARR_TIME-(time()-SESSION_TIMESTAMP)-1);
		counter(".timer",(1+QNUM)*ARR_TIME-(time()-SESSION_TIMESTAMP)-1,0,function(){
			time_over();
		},1);
	}
	
	function time_over()
	{
		questions();
	}
	
	function game_over()
	{
		location.reload();
	}
	
	function questions()
	{
		ajax("sql/check.php",{action:"QUESTION"},"POST",function(res){
			if(res[0]==1)
			{
				QNUM++;
				timer_reset();
				res=JSON.parse(res[1]);
				console.log(res);
				$(".q").html(res.Q);
				$(".a1").html(res.C[1]);
				$(".a2").html(res.C[2]);
				$(".a3").html(res.C[3]);
				$(".a4").html(res.C[4]);
				$(".questions").show();
			}
			else if(res==2) game_over();
		});
	}
	</script>
	<br/>
	<div id='main'>
		<div style="width:80%;margin:auto;">
			<div class="intro">
				<h3>The game starts in <span class='counter'><?=($ARR['time']-(time()-$_SESSION['game']['timestamp'])-1)?></span> Seconds</h3>
				<h3>You will be asked 5 questions. Whoever gets the most correct answers, wins.</h3>
				<h3>There is a 15 seconds timer on each question.</h3>
			</div>
			<h3 style="display:none;" class="head">You have <span class='timer'></span> seconds to answer this question</h3>
			<h2>
				<table class="questions" style="width:50%;margin:auto;display:none;">
					<tr>
						<td class='q' colspan=2></td>
					</tr>
					<tr>
						<td><button class='btn a1' num=1></button></td>
						<td><button class='btn a2' num=2></button></td>
					</tr>
					<tr>
						<td><button class='btn a3' num=3></button></td>
						<td><button class='btn a4' num=4></button></td>
					</tr>
				</table>
			</h2>
		</div>
	</div>
	<?php
}
else
{
//	$sql=sql("SELECT ff,winner,game_end FROM game WHERE id=".$_SESSION['game']['id'],2);
/*
	echo "<pre><h2>";
	print_r($_SESSION['game']);
	echo "</h2></pre>";
//*/
	$you=sql("SELECT a1,a2,a3,a4,a5 FROM answers WHERE gid=".$_SESSION['game']['id']." AND uid=".$_SESSION['game']['oppo'],2);
	$me=sql("SELECT a1,a2,a3,a4,a5 FROM answers WHERE gid=".$_SESSION['game']['id']." AND uid=".$_SESSION['id'],2);
	$pts=[
		"me"=>0,
		"you"=>0
	];
	for($i=1;$i<=5;$i++)
	{
		if($you[0]["a$i"]==$_SESSION['game']['a'][$i-1][0]) $pts['you']++;
		if($me[0]["a$i"]==$_SESSION['game']['a'][$i-1][0]) $pts['me']++;
	}
	if($pts['you']>$pts['me']) $o="<font color='red'>Defeat</font>";
	elseif($pts['you']<$pts['me']) $o="<font color='lightgreen'>Victory</font>";
	else $o="<font color='grey'>Draw</font>";
	?>
	<script>
	function new_game()
	{
		ajax("sql/check.php",{action:"NEW_GAME"},"POST",function(res){
			location.reload();
		});
	}
	</script>
	<div style="width:160px;margin:auto;">
		<h1><?=$o?></h1>
		<table cellspacing="0" cellpadding="0">
			<tr>
				<td style="border-right:solid;border-bottom:solid;"><h3><?=$_SESSION['summoner_name']?></h3></td>
				<td style="border:black;border-left:solid;border-bottom:solid;"><h3><?=sql("SELECT summoner_name FROM league_mastery_game WHERE id=".$_SESSION['game']['oppo'],2)[0]['summoner_name']?></h3></td>
			</tr>
			<?php
			for($i=1;$i<=5;$i++)
			{
				echo "<tr>";
					echo "<td style='border-right:solid;'><img src='img/".($me[0]["a$i"]==$_SESSION['game']['a'][$i-1][0]?"right.png' width='100px":"wrong.png")."'/></td>";
					echo "<td style='border-left:solid;'><img src='img/".($you[0]["a$i"]==$_SESSION['game']['a'][$i-1][0]?"right.png' width='100px":"wrong.png")."'/></td>";
				echo "</tr>";
			}
			?>
		</table>
		<button class='btn' onclick="new_game()">New game</button>
	</div>
	<?php
}
?>
