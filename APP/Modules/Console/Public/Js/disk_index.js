$(function(){
	/*全选和反选*/
	var isAll = 1;
	$('input[name=ids]').click(function(){

		if (isAll) {
			$('.did').prop('checked',true);
			isAll = 0;
		} else {
			$('.did').prop('checked',false);
			isAll = 1;
		}
	});
	
	//创建快照
	$('.createSnap').click(function(){
		var checked = $('.did:checked');
		if (checked.length == 0) {
			alert('请选择一块磁盘');
			return false;
		}
		if (checked.length != 1) {
			alert('一次只能创建一个');
			return false;
		}
		$('.disk_name').html(checked.parents('tr').find('td').eq(3).html());
		$('#createSnap').modal();
	});

	/*
		@der 创建快照提交时
	*/
	var is_create = true;
	$('.snap_submit').click(function(){
		if (!is_create) {
			return false;
		}

		if (is_create) {
			is_create = false;
		}

		var checked   = $('.did:checked').eq(0);
		var snap_name = $('#snap_name').val();
		$.post(createSnap , {id:checked.val(),snap_name:snap_name} ,function(data){
			if (data.status == 1) {
				alert('创建成功');
				$('#createSnap').modal('hide');
			} else {
				alert('创建失败了');
			}
			is_create = true;
		},'json');
	});

	/*
		@der 改名
	*/
	$('.reName').click(function(){
		//获取数据
		var checkbox  = $('.did:checked');
		var server_id = $('.server_id');

		if (checkbox.length == 0) {
			alert('请选择一个云磁盘!');
			return false;
		}

		//清空
		server_id.html('');
		
		$.each(checkbox,function(index,data){
			server_id.append("<input type='hidden' name='disk_id[]' value='"+$(this).val()+"'/>");
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

});