<?php
$api="4038b276-02ca-4e36-a2e9-ebce41783ab6";
$ARRAY_ALLOWED_CHARS=array('!','"','#','$','%','&',"'",'(',')','*','+',',','-','.','/','0','1','2','3','4','5','6','7','8','9',':',';','<','=','>','?','@','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','[',']','^','_','`','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','{','|','}','~',' ');

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

function ValidateLogin($str=false)
{
	if(!$str) return 1;
	$str=dc($str);
	$str=explode(":",decrypt($str,strlen($str)));
	if(!isset($str[1]) OR count($str)!=2) return 2;
	$sql=sql("SELECT id FROM league_mastery_game WHERE summoner_name='".esc($str[0])."' AND server='".esc($str[1])."'",1);
	if(!$sql) return 3;
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
?>
