$(function(){

	/*
		@der 删除快照
	*/
	new_delete(false);

	/*
		@der 创建镜像弹出框
	*/
	$('.createImage').click(function(){
		var checkbox  = $('.tid:checked');

		if (checkbox.length == 0) {
			alert('请选择一个快照!');
			return false;
		}

		if (checkbox.length != 1) {
			alert('一次只能创建一个镜像!');
			return false;
		}

		$('.cloud_snap_id').val(checkbox.val());
		$('#createImage').modal();
	});

	/*
		@der 改名
	*/
	$('.reName').click(function(){
		//获取数据
		var checkbox  = $('.tid:checked');
		var server_id = $('.server_id');

		if (checkbox.length == 0) {
			alert('请选择一个快照!');
			return false;
		}

		//清空
		server_id.html('');
		
		$.each(checkbox,function(index,data){
			server_id.append("<input type='hidden' name='snap_id[]' value='"+$(this).val()+"'/>");
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
		@der 回滚快照提示框
	*/
	$('.rollback').click(function(){
		var checkbox  = $('.tid:checked');

		if (checkbox.length == 0) {
			alert('请选择一个快照!');
			return false;
		}

		if (checkbox.length != 1) {
			alert('一次只能回滚一个快照!');
			return false;
		}
		$('input[name=snap_id]').val(checkbox.val());

		var td   = checkbox.parents('tr').find('td');
		var html = "您确定要把实例<span style='color:red;'>"+td.eq(1).html()+"</span> 的磁盘<span style='color:red;'>"+td.eq(2).html()+"</span>的数据回滚到<span style='color:red;'>"+td.eq(6).html()+"</span>吗？";
		$('.prompt').html(html);

		$('#rollback').modal();
	});

	$('.nextStep').click(function(){
		$('body').css('padding-right',0);
		$('#rollback').modal('hide');

		$('#exampleModal').modal();
		
	});

	/*
		@der 回滚快照导航的切换
	*/
	$('.checkType').find('li').click(function(){
		var index = $(this).index();
		$(this).siblings('li').removeClass('active');
		$(this).addClass('active');
		
		$('.form').hide().eq(index).show();
	});

	//获取验证码
	timers($('#information1'),sendInformation);
	timers($('#information2'),sendInformation2);

	var bool  = true;
	var bool2 = true;
	
	$('input[name=code]').eq(0).blur(function(){
		
		if ( !$(this).val().match(/^\S+$/ig) ) {
			$('#errorMsg1').html('验证码不能为空!');
			bool = false;
			return false;
		} else {
			$('#errorMsg1').html('');
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
					$('#errorMsg1').html('验证码有误!');
					bool = false;
					return false;
				} else {
					$('#errorMsg1').html('');
					bool = true;
				}
		    }
		});
		
		
		return true;
	});

	$('input[name=code]').eq(1).blur(function(){
		
		if ( !$(this).val().match(/^\S+$/ig) ) {
			$('#errorMsg2').html('验证码不能为空!');
			bool2 = false;
			return false;

		} else {			
			$('#errorMsg2').html('');
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
					$('#errorMsg2').html('验证码有误!');
					bool2 = false;
					return false;
				} else {
					$('#errorMsg2').html('');
					bool2 = true;
				}
		    }
		});
		return true;
	});
	
	/*表单提交时*/
	$('#handerPwd1').click(function(){
		$('input[name=code]').eq(0).blur();
		
		if ( !bool ) {

			return false;
		}
	});

	/*表单提交时*/
	$('#handerPwd2').click(function(){
		$('input[name=code]').eq(1).blur();
	
		if ( !bool2 ) {
			return false;
		}
	});

	/*
		@der 自动请求
	*/
	if ($('.continue').length > 0) {
		change_status({id:get_data($('.continue')) , status:2} , 5000 , $('.continue'));
	}

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
			sucess = '创建成功';
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
});