$(function(){
	$('.all').toggle(function(){

		$('.box').attr('checked',true);
	},function(){
		$('.box').attr('checked',false);
	});

	//单击开启时
    stcl('.start',0);

	//单击关闭时
	stcl('.close',2);


	//处理异步开启和关闭的方法
	function stcl(obj,status){
		$('table').on('click',obj,function(){
			var lid=$(this).attr('lid');
			var _this=$(this);
		
			$.post(isTrue,{'id':lid,'status':status},function(data){
				    if(data==1){

				    	   //判断是开启还是关闭
                           if(status){
                             
                                _this.html('开启').removeClass('close').addClass('start').parent().prev().prev().prev().html('关闭');
                           }else{
                           	
                           	   _this.html('关闭').removeClass('start').addClass('close').parent().prev().prev().prev().html('开通中');

                           }
				    }else{
				    	alert('修改失败请重试');
				    }

			});
		});


	}
});