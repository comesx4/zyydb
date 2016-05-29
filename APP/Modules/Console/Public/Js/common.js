	/*
		@der 获取异步的data值
	*/
	function get_data(checkbox){
		var str	 = '';
		$.each(checkbox , function(index,data){			
			str += $(this).val()+',';
		});
		//去掉最右边的 ","
		str = str.substr(0,str.length-1);
		return str;
	}

	/*
		@der计时器
	*/
	function timers(obj , locations){
		obj.click(function(){
			var i =60;
			$(this).attr('disabled','disabled').html("剩余"+i+"秒");
			var time = setInterval(function(){
				if (i>0) {
					i--;
					obj.attr('disabled','disabled').html("剩余"+i+"秒");
					
				} else {
					//恢复手机号码可修改
					obj.attr('disabled',false).html('获取验证码');
					window.clearInterval(time);
				}
			},1000);

			// 发送短信
			$.post(locations,{},function(data){
				if (data.status == 0) {
					alert(data.message);
				}
			},'json');
		});
	}

	/*
		@der 处理删除的统一方法
	*/
	function new_delete(is_all ,fun){
		if (is_all == undefined) {
			is_all = true;
		}

		//删除事件
		$('.delete').click(function(){

			
			var checkbox = $('.tid:checked');
			if (checkbox.length == 0) {
				new_alert('请先勾选');
				return false;
			}
			if ( !is_all ) {
				if (checkbox.length != 1) {
					new_alert('一次只能删除一个');
					return false;
				}
			}

			if ( !confirm('确认删除？删除后无法恢复!') ) {
				return false;
			}
			//显示等待
			my_alert();

			var id = get_data(checkbox);
			$.post(info_delete,{id:id},function(data){
				remove_alert();
				if (data.status == 0) {
					new_alert('删除失败！');
				} else {
					new_alert('删除成功！');
					window.location = '';
				}
			},'json');
		});
	}

	/*
		弹出框
	*/
	function my_alert(html){
		if (html == undefined) {
			html = '处理中...';
		}

		$('#position').html(html).show();

		$("<div id='pachar'></div>").css({
			'height' 	 : $(document).height(),
			'width'  	 : $(document).width(),
			'background' : '#000',
			'opacity' 	 : 0.3,
				'filter' 	 : 'Alpha(Opacity = 30)',
				'position'   : 'absolute',
				'top'		 : 0,
				'left'	     : 0,
				'z-index'    : 10
		}).appendTo($('body'));
	}

	/*弹出框消失*/
	function remove_alert(){
		$('#position').hide();
		$('#pachar').remove();
	}

	/*
		@der 弹出一会就消失
	*/
	function new_alert(html,time){
		if (html == undefined) {
			html = '处理中...';
		}

		//默认时间 2秒
		if (time == undefined) {
			time = 2000;
		}

		$('#position').html(html).show();

		setTimeout(function(){
			
			remove_alert();
		},time);
	}
	
$(function(){
	/*全选和反选*/
	var isAll = 1;
	$('input[name=ids]').click(function(){

		if (isAll) {
			$('.tid').prop('checked',true);
			isAll = 0;
		} else {
			$('.tid').prop('checked',false);
			isAll = 1;
		}
	});

	$('body').click(function(){
		$('.search').hide();
	});

	$('.alert-search').click(function(event){
		//取消事件冒泡
		event.stopPropagation();
		$('.search').show();
	});

	/*
		@der 日历
	*/
	$('#min_date').focus(function(){

		$('#laydate_box').show();
			laydate({
		    elem: '#min_date',
		    format: 'YYYY-MM-DD',
		    festival: true, 
		    choose: function(datas){ 
		        
		    }
		});
	});

	$('#max_date').focus(function(){

		$('#laydate_box').show();
			laydate({
		    elem: '#max_date',
		    format: 'YYYY-MM-DD',
		    festival: true, 
		    choose: function(datas){ 
		        
		    }
		});
	});
	



});