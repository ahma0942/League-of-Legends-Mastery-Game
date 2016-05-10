<?php
include "API.php";
$ARRAY_ALLOWED_CHARS=array('!','"','#','$','%','&',"'",'(',')','*','+',',','-','.','/','0','1','2','3','4','5','6','7','8','9',':',';','<','=','>','?','@','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','[',']','^','_','`','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','{','|','}','~',' ');

function randnum($min=1,$max=10,$amount=1,$unique=false)
{
	if(!is_numeric($min)) $min=1;
	if(!is_numeric($max)) $max=10;
	if(!is_numeric($amount) OR $amount<1) $amount=1;
	if($amount==1) return rand($min,$max);
	if($max-$min>$amount AND $unique===true) $amount=$max-$min;
	$ret=array();
	for($i=1;$i<=$amount;$i++)
	{
		if(!$unique) $ret[]=rand($min,$max);
		else
		{
			$in=rand($min,$max);
			while(in_array($in,$ret)) $in=rand($min,$max);
			$ret[]=$in;
		}
	}
	return $ret;
}

function updateMastery()
{
	global $api;
	$link="https://$_SESSION[server].api.pvp.net/championmastery/location/$_SESSION[server]1/player/$_SESSION[summoner_id]/score?api_key=$api";
	$curl=curl($link);
	$sql=sql("SELECT id FROM league_mastery_game WHERE id='$_SESSION[id]' AND mastery='$curl'",1);
	if(!$sql){
		$sql=sql("UPDATE league_mastery_game SET mastery='$curl' WHERE id='$_SESSION[id]'");
		$_SESSION['mastery']=$curl;
	}
}

function ValidateMasteryPage($id,$server,$code)
{
	global $api;
	$link="https://$server.api.pvp.net/api/lol/$server/v1.4/summoner/$id/masteries?api_key=$api";
	$curl=json_decode(curl($link));
	foreach($curl->$id->pages as $page) if($page->name==$code) return true;
	return false;
}

function redir($str)
{
	?>
	<script>
	window.location="<?php echo $str; ?>";
	</script>
	<?php
	exit;
}

function alert($str)
{
	?>
	<script>
	window.alert("<?php echo $str; ?>");
	</script>
	<?php
}

function validateSummoner($name,$server)
{
	global $api;
	$link="https://$server.api.pvp.net/api/lol/$server/v1.4/summoner/by-name/$name?api_key=$api";
	$curl=json_decode(curl($link));
	if(!isset($curl->$name->id)) return false;
	return $curl->$name->id;
}

function curl($link)
{
	$cookie1="a.txt";
	$options1=array(
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_AUTOREFERER => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_COOKIEFILE => $cookie1,
		CURLOPT_COOKIEJAR => $cookie1,
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_SSL_VERIFYHOST => 0
	);
	$ch=curl_init($link);
	curl_setopt_array($ch,$options1);
	$content=curl_exec($ch);
	curl_close($ch);
	return $content;
}

function rand_str($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
{
	$str = '';
	$count = strlen($charset);
	while($length--) $str .= $charset[mt_rand(0, $count-1)];
	return $str;
}

function FoundMatch()
{
	global $ARR;
	$sql=sql("SELECT player1 FROM match_found WHERE player1=$_SESSION[id] OR player2=$_SESSION[id]",1);
	if($sql) return 1;
	$sql=sql("SELECT id FROM league_mastery_game WHERE queue=1 AND id!=$_SESSION[id]",1);
	if($sql)
	{
		$sql=sql("SELECT id FROM league_mastery_game WHERE queue=1 AND id!=$_SESSION[id]",2);
		$id=$sql[0]['id'];
		$sql=sql("SELECT player1 FROM match_found WHERE player1=$_SESSION[id] OR player2=$_SESSION[id]",1);
		if(!$sql){
			$sql1=sql("INSERT INTO match_found (player1,player2,timestamp) VALUES($_SESSION[id],$id,".time().")");
			$sql2=sql("UPDATE league_mastery_game SET queue=0 WHERE id=$_SESSION[id] OR id=$id");
		}
		return 1;
	}
	return 0;
}

function h($str)
{
	return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
}

function updateLastOn()
{
	if(isset($_SESSION['id'])) sql("UPDATE league_mastery_game SET last_on=".time()." WHERE id=$_SESSION[id]");
}

function ValidateLogin($str=false)
{
	if(!$str) return 1;
	$str=dc($str);
	$str=explode(":",decrypt($str,strlen($str)));
	if(!isset($str[1]) OR count($str)!=2) return 2;
	$sql=sql("SELECT id FROM league_mastery_game WHERE summoner_name='".esc($str[0])."' AND server_id='".esc($str[1])."'",1);
	if(!$sql) return 3;
	$sql=sql("SELECT id,summoner_id,summoner_name,server_id,mastery,timestamp,rank FROM league_mastery_game WHERE summoner_name='".esc($str[0])."' AND server_id='".esc($str[1])."'",2);
	foreach($sql[0] AS $name=>$val) $_SESSION[$name]=$val;
	$sql=sql("SELECT server FROM servers WHERE id='$_SESSION[server_id]'",2);
	$_SESSION['server']=$sql[0]['server'];
	return $str;
}

function CreateLogin($str=false)
{
	if(!$str) return -1;
	return ec(encrypt($str,strlen($str)));
}

function esc($str)
{
	global $GLOBAL_DB;
	return $GLOBAL_DB->real_escape_string(stripslashes($str));
}

function ec($string,$key=false)
{
	global $api;
	if($key===false) $key=md5($api).md5("131:euw");
	$iv = mcrypt_create_iv
	(
		mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC),
		MCRYPT_DEV_URANDOM
	);

	$string = base64_encode
	(
		$iv.
		mcrypt_encrypt
		(
			MCRYPT_RIJNDAEL_256,
			hash('sha256', $key, true),
			$string,
			MCRYPT_MODE_CBC,
			$iv
		)
	);
	return $string;
}

function dc($string,$key=false)
{
	global $api;
	if($key===false) $key=md5($api).md5("131:euw");
	$data = base64_decode($string);
	$iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC));
	
	$string = rtrim
	(
		mcrypt_decrypt
		(
			MCRYPT_RIJNDAEL_256,
			hash('sha256', $key, true),
			substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)),
			MCRYPT_MODE_CBC,
			$iv
		),
		"\0"
	);
	return $string;
}

function sql($where,$loop=FALSE,$extra=FALSE)
{
	global $GLOBAL_DB;
	$sql=$GLOBAL_DB->query($where);
	if($GLOBAL_DB->error) die($GLOBAL_DB->error);
	if($loop!==FALSE)
	{
		if($loop==1) return $sql->num_rows;
		elseif($loop==2)
		{
			if($extra!==FALSE) while($row=$sql->fetch_array(MYSQLI_ASSOC)) $result[]=$row[$extra];
			else while($result[]=$sql->fetch_array(MYSQLI_ASSOC)){}
		}
		elseif($loop==3) while($result[]=$sql->fetch_assoc()){}
	}
	else return $sql;
	if($GLOBAL_DB->error) die($GLOBAL_DB->error);
	return array_filter($result);
}

function sql_get($table,$select,$field,$value)
{
	$sql=sql("SELECT `$select` FROM `$table` WHERE `$field`='$value'",2);
	if(isset($sql[0])) return $sql[0][$select];
	return false;
}

function sql_check($table,$field,$value)
{
	return (sql("SELECT `$field` FROM `$table` WHERE `$field`='$value'",1)>0?true:false);
}

function encrypt($str,$num=5,$i=0)
{
	global $ARRAY_ALLOWED_CHARS;
	if($i>10) $i=10;
	if($i<0) $i=0;
	if(!is_numeric($num))
	{
		$num1=0;
		foreach(str_split($num) as $char) $num1+=ord($char);
		$num=$num1;
	}
	if($num>1000000) while($num>1000000) $num=(int)($num/2);
	
	$result="";
	$str_arr=str_split($str);
	$arr_num=count($ARRAY_ALLOWED_CHARS);
	foreach($str_arr as $char)
	{
		$i++;
		$key=array_search($char,$ARRAY_ALLOWED_CHARS);
		if($key===FALSE) die("Allowed characters are [".implode(style(",","blue"),$ARRAY_ALLOWED_CHARS)."].");
		$new_key=$i*$num+$key;
		if($new_key>$arr_num-1)
		{
			$per=(int)($new_key/$arr_num);
			$new_key=$new_key-$per*$arr_num;
		}
		$result.=$ARRAY_ALLOWED_CHARS[$new_key];
	}
	return $result;
}

function decrypt($str,$num=5,$i=0)
{
	global $ARRAY_ALLOWED_CHARS;
	if($i>10) $i=10;
	if($i<0) $i=0;
	if(!is_numeric($num))
	{
		$num1=0;
		foreach(str_split($num) as $char) $num1+=ord($char);
		$num=$num1;
	}
	if($num>1000000) while($num>1000000) $num=(int)($num/2);
	
	$result="";
	$str_arr=str_split($str);
	$arr_num=count($ARRAY_ALLOWED_CHARS);
	foreach($str_arr as $char)
	{
		$i++;
		$key=array_search($char,$ARRAY_ALLOWED_CHARS);
		if($key===FALSE) die("Allowed characters are [".implode(",",$ARRAY_ALLOWED_CHARS)."].");
		$new_key=$key-$i*$num;
		if($new_key<0) while($new_key<0) $new_key+=$arr_num;
		$result.=$ARRAY_ALLOWED_CHARS[$new_key];
	}
	return $result;
}

function dlg($msg,$header="Information",$dlg_num=0)
{
	return <<<EOF
	<div class="dlg" id="dlg$dlg_num">
		<div class="bot">
			<b class='header'><font size='+2' style='text-align:center;padding:10px;'>$header</font></b>
			<hr style='width:100%;border: 1px solid black;'/>
			<span>$msg</span>
		</div>
	</div>
EOF;
}
?>
