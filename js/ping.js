$(document).ready(function(){
	setInterval(function(){
		ajax("sql/check.php",{action:"UPDATE_LAST_ON"},"POST",function(res){});
	},5000);
});
