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
			$("#output").html(dlg("<p>Ready to play?</p><div class='counter'>"+((parseInt(res[1])+queue)-time())+"</div><div class='buttons'><button class='btn' onclick=\"$(this).parent().html('Accepted');accept();\"><img src='img/yes.png'/></button><button class='btn2' onclick=\"$(this).parent().html('Declined');decline();\"><img src='img/no.png'/></button></div>","Match found",1));
			
			counter(((parseInt(res[1])+queue)-time()),0,function(){
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
	<div style="width:160px;margin:auto;">
		<button id='queue' class="btn" onclick="if($(this).html()!='Queue Up') stop_queue(); else start_queue();">Queue Up</button>
	</div>
	<?php
}
else
{
	echo "<br/><br/><br/>";
	if($_SESSION['p1']===true)
	{
		if(!isset($_SESSION['done'])) $_SESSION['done']=false;
		if($_SESSION['done']===false)
		{
			sql("DELETE FROM match_found WHERE player1=$_SESSION[id] OR player2=$_SESSION[id]");
			$_SESSION['done']=true;
		}
	}
	print_r($_SESSION['q']);
}
?>
