$(function(){
	
	//日历插件
	$('#date').focus(function(){
		$('#laydate_box').show();
			laydate({
		    elem: '#date',
		    format: 'YYYY-MM-DD',
		    festival: true, //显示节日
		    choose: function(datas){ //选择日期完毕的回调
		        
		    }
		});
	});

	//日历插件
	$('#date1').focus(function(){

		$('#laydate_box').show();
			laydate({
		    elem: '#date1',
		    format: 'YYYY-MM-DD',
		    festival: true, //显示节日
		    choose: function(datas){ //选择日期完毕的回调
		        
		    }
		});
	});

	//状态的选择事件
	$('#status').change(function(){
		$('.submit').click();
	});
	

	
});