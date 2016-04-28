$(document).ready(function(){
	$("form.ajax").on('submit',function(e){
		e.preventDefault();
		var url=$(this).attr("action");
		var form=$(':input:visible',this).serialize()+"&"+$('input[type=hidden]',this).serialize();
		$.ajax({
			cache:false,
			type:"POST",
			url:url,
			data:form,
			success:function(data){
				var dat=data;
				if($(data).filter('div.output').length) dat=$(data).filter('div.output').html();
				alert(dat);
				if($(data).filter('div.cookie').length){
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
			}
		});
	});
});
