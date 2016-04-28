<?php
session_start();
include "db.php";
include "php/funcs.php";
include "php/vars.php";

if(!isset($_GET['p'])) $_GET['p']="index";
if(!file_exists("pages/$_GET[p].php")) $_GET['p']="index";

include "head.php";
include "pages/$_GET[p].php";
include "foot.php";
?>
