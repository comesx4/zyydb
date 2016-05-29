$(function(){
	/*
		@der 改名
	*/
	$('.reName').click(function(){
		//获取数据
		var checkbox  = $('.tid:checked');
		var server_id = $('.server_id');

		if (checkbox.length == 0) {
			alert('请选择一个镜像!');
			return false;
		}

		//清空
		server_id.html('');
		
		$.each(checkbox,function(index,data){
			server_id.append("<input type='hidden' name='id[]' value='"+$(this).val()+"'/>");
		});
		$('.server_sum').html(checkbox.length);
		$('#reName').modal();		
	});

	new_delete(false);

	/*
		@der 自动请求
	*/
	if ($('.continue').length > 0) {
		change_status({id:get_data($('.continue')) , status:1} , 5000 , $('.continue'));
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