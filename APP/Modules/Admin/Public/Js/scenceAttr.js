$(function(){
	$('select[name=type]').change(function(){
		var value=$(this).val();

		//单选按钮和复选框
		if(value==1||value==3){
             $('.name').show();
             $('.title').show();
         //文本框    
		}else if(value==0){
             $('.title').show();
             $('.name').hide();
         //标题
		}else if(value==2){
             $('.title').hide();
             $('.name').show();
		}
	});
});