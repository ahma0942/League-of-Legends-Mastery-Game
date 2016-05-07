$(document).ready(function(){
	$("form.ajax").on('submit',function(e){
		e.preventDefault();
		var url=$(this).attr("action");
		var form=$(':input:visible',this).serialize()+"&"+$('input[type=hidden]',this).serialize();
		ajax(url,form,"POST",function(res){});
	});
});

function counter(time,delay,callback,extra)
{
	var timeFunc=(function(res){
		var start=time;
		var timer=setInterval(function(){
			if(start==0) clearInterval(timer);
			start--;
			if(start>=0) $(".counter").html(start);
		},1000);
		setTimeout(function(){callback()},(isset(extra)?extra*1000:0)+time*1000);
	});
	if(delay>0) setTimeout(function(){timeFunc()},delay*1000);
	else timeFunc();
}

function ajax(url,query,method,callback)
{
	$.ajax({
		cache:false,
		type:method,
		url:url,
		data:query,
		success:function(data){
			var res=ajaxdata(data);
			callback(res);
		}
	});
}

function ajaxdata(data)
{
	var dat=false;
	var ret=false;
	if($(data).filter('div.output').length) dat=$(data).filter('div.output').html();
	if($(data).filter('div.return').length) ret=$(data).filter('div.return').html();
	if(!$(data).filter('div.noalert').length) alert(dat);
	if($(data).filter('div.cookie').length)
	{
		var exp=60*60*24*365;
		var name=$(data).filter('div.cookie').attr('name');
		var attr=$(data).filter('div.cookie').attr('exp');
		var val=$(data).filter('div.cookie').html();
		if(typeof attr!=='undefined' && attr!==false) exp=$(data).filter('div.cookie').attr('exp');
		var now = new Date();
		now.setTime(now.getTime()+exp*1000);
		document.cookie = name+"="+encodeURIComponent(val)+"; expires=" + now.toUTCString() + "; path=/";
	}
	if($(data).filter('div.redir').length) window.location=$(data).filter('div.redir').html();
	return (dat!==false?(ret!==false?[dat,ret]:dat):(ret!==false?[data,ret]:data));
}

function isset(elm)
{
	if(typeof elm!==typeof undefined && elm!==false) return true;
	return false;
}

function dlg(msg,header,dlg_num)
{
	return "<div class=\"dlg\" id=\"dlg"+(isset(dlg_num)?dlg_num:1)+"\">\
		<div class=\"bot\">\
			<b class='header'><font size='+2' style='text-align:center;padding:10px;'>"+header+"</font></b>\
			<hr style='width:100%;border: 1px solid black;'/>\
			<span>"+msg+"</span>\
		</div>\
	</div>";
}

function time()
{
	return Math.floor(Date.now()/1000);
}
