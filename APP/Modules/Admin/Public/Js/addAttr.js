$(function(){
	//选择样式的时候异步获取信息
	$('#css').change(function(){
		var sid=$(this).val();

		//异步获取信息
		$.post(getChange,{'id':sid},function(data){
			   //判断获取是否成功
			   if(data.status){
                  
                  //属性名称的值
                  $('input[name=attr]').val(data.name);

                  //属性的内容
                  $('textarea').val(data.content);

                  //判断是否为标题
                  if(data.istitle==1){
                  	$('input[name=istitle]').eq(0).attr('checked',true);

                  }else{
                  	$('input[name=istitle]').eq(1).attr('checked',true);

                  }



			   }else{


			   }

		},'json');


	});

});
