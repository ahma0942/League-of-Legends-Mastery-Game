<?php
$dir = new DirectoryIterator("img/bg/");
foreach($dir as $fileinfo) if(!$fileinfo->isDot()) $ARR["BGIMGS"][]=explode(".",$fileinfo->getFilename())[0];
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<meta name="description" content="" />
	<meta name="author" content="" />
	<!--[if IE]>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<![endif]-->
	<title>League of Legends Mastery Game</title>
	<style type="text/css">
	body {
		font-family:'Open Sans',sans-serif;
		background-image: url("img/bg/<?=$ARR["BGIMGS"][rand(0,count($ARR["BGIMGS"])-1)];?>.jpg");
		background-size:cover;
		background-attachment:fixed;
	}
	</style>
	<link href="css/css.css" rel="stylesheet"/>
	<script src='js/jquery.js'></script>
	<script src='js/js.js'></script>
</head>
<body>
	<?php include "menu.php"; ?>
	<div id="output"></div>
	<div id="content">