$(function(){

	// $('form').submit(function(){
	// 	if ( !$('.tid:checked').length ) {
	// 		new_alert('请选择一个实例');
	// 		return false;
	// 	}
		
	// });

	/*
		@der 续费的单击事件
	*/
	$('.renew').click(function(){
		var checkbox = $('.tid:checked');

		if (checkbox.length == 0) {
			new_alert('请选择一台实例!');
			return false;
		}
		if (checkbox.length != 1) {
			new_alert('一次只能续费一台!');
			return false;
		}

		window.open( "/index.php/goods_renew/renew/id/"+checkbox.attr('lid'));
	});

	/*
		@der 升级的单击事件
	*/
	$('.upgrade').click(function(){
		var checkbox = $('.tid:checked');

		if (checkbox.length == 0) {
			new_alert('请选择一台实例!');
			return false;
		}
		if (checkbox.length != 1) {
			new_alert('一次只能升级一台!');
			return false;
		}

		window.open("/index.php/goods_renew/index/id/"+checkbox.attr('lid'));
	});

	/*
		@der 重启的单击事件
	*/	
	$('button[name=restart]').click(function(){
		//获取数据
		var result 	 = handle_data(function(checkbox){
			var bool = true;
			$.each(checkbox,function(index,data){
				if ($(this).attr('status') != 2) {
					new_alert('只能重启运行状态的机器');
					bool = false;
					return false;
				}
			});

			return bool;

		});

		if (!result) {
			return false;
		}

		//发送异步请求
		send_request(result,restartAction,3,2,"<img src='"+publicPath+"/Images/wait.gif'>重启中...");
	});

	/*
		@der 开机操作
	*/
	$('.startLiving').click(function(){
		//获取数据
		var result 	 = handle_data(function(checkbox){
			var bool = true;
			$.each(checkbox,function(index,data){
				if ($(this).attr('status') != 4) {
					new_alert('只能开启关机状态的机器');
					bool = false;
					return false;
				}
			});

			return bool;

		});

		if (!result) {
			return false;
		}

		//发送异步请求
		send_request(result,restartAction,1,2,"<img src='"+publicPath+"/Images/wait.gif'>开机中...");
	});

	/*
		@der 关机操作
	*/
	$('.closeLiving').click(function(){
		//获取数据
		var result 	 = handle_data(function(checkbox){
			var bool = true;
			$.each(checkbox,function(index,data){
				if ($(this).attr('status') != 2) {
					new_alert('只能关闭运行状态的机器');
					bool = false;
					return false;
				}
			});

			return bool;
		});

		if (!result) {
			return false;
		}

		//发送异步请求
		send_request(result,restartAction,2,4,"<img src='"+publicPath+"/Images/wait.gif'> 关机中...",'已关机');
	});

	/*
		@der 重装系统
	*/
	$('.reOs').click(function(){
		var checkbox  = $('.tid:checked');
		if (checkbox.length == 0) {
			new_alert('请选择一台实例!');
			return false;
		}
		if (checkbox.length != 1) {
			new_alert('一次只能重装一台!');
			return false;
		}
		if (checkbox.attr('status') != 2) {
			new_alert('只能重装运行状态的实例！');
			return false;
		}	

		//获取原来的状态
		// checkbox.attr('first_status',checkbox.attr('status'));
		//状态名称
		// checkbox.attr('status_remark',checkbox.parents('tr').find('td').eq(3).html());

		$('input[name=osid]').val(0);
		$('input[name=server_id]').val(checkbox.val());

		$('#alertCheck').modal();
	});

	$('.handerNext').click(function(){
		$('.teleCode').blur();
		$('.emailCode').blur();
		if (!bool && !bool2) {
			return false;
		}
		$('#alertCheck').modal('hide');
		$('body').css('padding',0);
		$('#reInstallOs').modal();
	});

	/*
		@der 重置密码
	*/
	$('.rePassword').click(function(){
		
		//获取数据
		var checkbox  = $('.tid:checked');

		if (checkbox.length == 0) {
			new_alert('请选择一台实例!');
			return false;
		}

		if (checkbox.length != 1) {
			new_alert('密码一次只能重置一台!');
			return false;
		}

		if (checkbox.attr('status') != 2) {
			new_alert('只能重置运行状态的实例！');
			return false;
		}	
		
		$('input[name=server_id]').val(checkbox.val());

		$('#exampleModal').modal();

	});

	/*
		@der 改名
	*/
	$('.reName').click(function(){
		//获取数据
		var checkbox  = $('.tid:checked');
		var server_id = $('.server_id');

		if (checkbox.length == 0) {
			new_alert('请选择一台实例!');
			return false;
		}

		//清空
		server_id.html('');
		
		$.each(checkbox,function(index,data){
			server_id.append("<input type='hidden' name='server_id[]' value='"+$(this).val()+"'/>");
		});
		$('.server_sum').html(checkbox.length);
		$('#reName').modal();		
	});

	/*
		@der 改名的表单提交
	*/
	$('.rename_submit').click(function(){
		if ( !$('input[name=newName]').val().match(/^\S+$/ig) ) {
			$(this).siblings('.error').html('名称不能为空');
			return false;
		}
	});

	/*
		@der 自动请求
	*/
	if ($('.continue').length > 0) {
		change_status({id:get_data($('.continue')) , status:2} , 5000 , $('.continue'));
	}
	//已关机
	if ($('.continue2').length > 0) {
		change_status({id:get_data($('.continue2')) , status:4} , 5000 , $('.continue2'),4,'已关机');
	}
	//重装系统
	// if ($('.reInstallOs').length > 0) {
	// 	$.each($('.reInstallOs'),function(index,data){
	// 		change_status({id:$(this).val() , status:$(this).attr('first_status')} , 5000 , $('.reInstallOs'),4,$(this).attr('status_remark'));
	// 	});
		
	// }

	//获取验证码
	timers($('.getTeleCode'),sendInformation);
	timers($('.getEmailCode'),sendInformation2);

	// 	return true;
	// });
	var bool  = true;
	var bool2 = true;
	verify($('input[name=password]').eq(0) , function(obj){
		if ( !obj.val().match(/^[a-z,0-9,A-Z]{2,12}$/ig) ) {
			bool = false;
			return false;
		} else {
			bool = true;
			return true;
		}
	},'密码有误:请输入2-12位的数字或字母');

	verify($('input[name=password]').eq(1) , function(obj){
		if ( !obj.val().match(/^[a-z,0-9,A-Z]{2,12}$/ig) ) {
			bool2 = false;
			return false;
		} else {
			bool2 = true;
			return true;
		}
	},'密码有误:请输入2-12位的数字或字母');

	$('.code1').blur(function(){
		var error  = $(this).parents('.modal-body').next().find('.error');

		if ( !$(this).val().match(/^\S+$/ig) ) {
			error.html('验证码不能为空!');
			bool = false;
			return false;
		} else {
			error.html('');
			bool = true;
		}

		//异步验证验证码
		$.ajax({
		    type: "POST",
		    url: checkVerify,
		    data:{verify:$(this).val()},			   
		    async	 : false,
		    success:function(data){
		    	if (data == 'false') {
					error.html('验证码有误!');
					bool = false;
					return false;
				} else {
					error.html('');
					bool = true;
				}
		    }
		});
		
		return true;
	});

	$('.code2').blur(function(){
		var error  = $(this).parents('.modal-body').next().find('.error');
		if ( !$(this).val().match(/^\S+$/ig) ) {
			 error.html('验证码不能为空!');
			bool2 = false;
			return false;

		} else {			
			 error.html('');
			bool2 = true;
		}

		//异步验证验证码
		$.ajax({
		    type: "POST",
		    url: checkVerify2,
		    data:{verify:$(this).val()},			   
		    async	 : false,
		    success:function(data){
		    	if (data == 'false') {
					 error.html('验证码有误!');
					bool2 = false;
					return false;
				} else {
					 error.html('');
					bool2 = true;
				}
		    }
		});
		return true;
	});
	
	/*表单提交时*/
	$('.handerPwd1').click(function(){
		$('input[name=code]').eq(0).blur();
		if ( !bool ) {
			return false;
		}
		$('input[name=password]').eq(0).blur();
		if ( !bool ) {
			return false;
		}
		
	});

	/*表单提交时*/
	$('.handerPwd2').click(function(){
		$('input[name=code]').eq(1).blur();
		if ( !bool2 ) {
			return false;
		}
		$('input[name=password]').eq(1).blur();
	
		if ( !bool2 ) {
			return false;
		}
	});

	/*
		@der 重置密码导航的切换
	*/
	$('.checkType').find('li').click(function(){
		var index = $(this).index();
		$(this).siblings('li').removeClass('active');
		$(this).addClass('active');
		
		$('.form').hide().eq(index).show();
	});

	/*
		@der 统一验证导航的切换
	*/
	$('.max-checkType').find('li').click(function(){
		var index = $(this).index();
		$(this).siblings('li').removeClass('active');
		$(this).addClass('active');
		
		$('.unificationCheck').hide().eq(index).show();
	});

	

	/*
		@der 重装系统导航的切换
	*/
	$('.osType').find('li').click(function(){
		var index = $(this).index();
		$(this).siblings('li').removeClass('active');
		$(this).addClass('active');
		
		$('.os_type').hide().eq(index).show();
	});

	/*
		@der 系统的选择
	*/
	$('.os_type').find('li').click(function(){
		$('input[name=osid]').val($(this).attr('osid'));
		$(this).parents('.dropdown').find('.dropdown-toggle').html($(this).html()+' <span class="glyphicon glyphicon-menu-down"></span>');
	});

	/*重装系统表单提交时*/
	$('.os-submit').click(function(){
		var newPassword = $('input[name=newPassword]');
		var rePassword  = $('input[name=rePassword]');

		if ( !newPassword.val().match(/^[a-z,0-9,A-Z]{2,12}$/ig) ){
			$('#osErrorMsg').html('密码必须为2-12位的字母或数字');
			return false;
		}

		if (newPassword.val() != rePassword.val()) {
			$('#osErrorMsg').html('两次密码不一致');
			return false;
		}

		if ($('input[name=osid]').val() == 0) {
			$('#osErrorMsg').html('请选择一个操作系统');
			return false;
		}


	});
	
	/**
	 *	@der 定时器，定时请求(改变实例状态)
	 *	@param object $obj 发送的数据
	 *	@param int $time 时间间隔(秒)
	 *  @param object checkbox 被选中的input
	 *  @param int status 要改变的状态
	 *   
	 */
	function change_status(obj , time , checkbox, status , sucess){
		if ( sucess == undefined) {
			sucess = '运行中';
		}
		status == undefined ? status = 2 : status = status;

		timer = setInterval(function(){
			$.post(response_timer , obj , function(data){
				
				//全部成功
				if (data.status == 2) {
					//删除定时器
					window.clearInterval(timer);
				}
				
				/*只要有一台实例重启成功!*/
				if (data.status == 1 || data.status == 2) {
					/*重启完成之后的节点操作*/
					$.each(data.id ,function(index,reqult){
						$.each(checkbox , function(index2,reqult2){
							if ($(this).val() == reqult) {
								$(this).attr({'disabled':false,'status':status}).parent().nextAll('.status').html(sucess);
							}
						});
					});
				}
			} , 'json');	
		},time);
	}

	/*
		@der 异步请求的数据获取
	*/
	function handle_data(fun){
		var checkbox  = $('.tid:checked');

		/*为空验证*/
		if ( !checkbox.length ) {
			new_alert('请选择一个实例');
			return false;
		}
		
		if (fun != undefined) {
			if ( !fun(checkbox) ) {
				return false;
			}
		}


		checkbox.attr({'disabled':true , 'checked':false});
		var str = get_data(checkbox);

		return [str,checkbox];
	}

	/*
		@der 发送异步请求
		@param array result 数据
		@param url location 异步地址
		@param int type 类型 
		@param int status 要获取的状态
		@param string html 提示的消息
	*/
	function send_request(result,location,type,status,html,success){
		var attr = 0;
		switch (type) {
			case 1: //开机中
				attr = 8;
				break;
			case 2: //关机中
				attr = 9;
				break;
			case 3: //重启中
				attr = 7;
				break;
		}

		/*发送异步重启请求*/
		$.post(location , {id:result['0'],type:type} , function(data){

			if (data.status == 1) {
				
				result['1'].attr('status',attr).parent().nextAll('.status').html(html);
				//定时器
				change_status({id:result['0'] , status:status} , 10000 , result['1'],status,success);
			} else {
				new_alert(data.message);
			}

		},'json');
	}

	function verify(obj , fun , html){
		obj.blur(function(){
			if ( !fun(obj) ) {
				$(this).parents('.modal-body').next('.modal-footer').find('span').html(html);
				return false;
			} else {
				$(this).parents('.modal-body').next('.modal-footer').find('span').html('');
			}

			return true;
		});
		
	}
	
	

				
});