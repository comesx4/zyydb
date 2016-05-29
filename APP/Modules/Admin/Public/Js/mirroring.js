$(function(){
	$('select[name=type]').change(function(){
		 //类型为镜像时
		 if($(this).val()==1){
             $('.jx').show();
             $('form').attr('action',insert);

		 }else{
		 	//类型为操作系统时
		 	$('.jx').hide();
		 	$('form').attr('action',insertOs);
		 }
	});


	$('.chiden').click(function(){
		//勾选时
		if($(this).prop('checked')== true){
			//让所有同级别li下面的DIV隐藏
			$(this).parents('li').siblings('li').find('div').hide();
			$(this).parent().next().show().find('input').prop('checked',true);
		
		//取消勾选时候
		}else{
			$(this).parent().next().hide().find('input').prop('checked',false);
		}
	});
    

    $('.close').click(function(){
    	$(this).parents('div').hide();
    });

    if (type == 0) {
    	$('.jx').hide();
    }


});