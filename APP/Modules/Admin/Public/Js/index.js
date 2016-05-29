$(function(){
    $('.list').find('li span').toggle(function(){
        $(this).removeClass('jia').addClass('jian').next('ul').show();

    },function(){
        $(this).removeClass('jian').addClass('jia').next('ul').hide();

    });
    
    //问题的删除
    $('.del').click(function(){
		var bool=confirm('确认删除?');

	     if(bool){
				_this=$(this);
	
				   var cid=_this.attr('cid');
				   var data={'cid':cid};
		       
				$.post(problemDel,data,function(data){
					
					 if(data==1){
					 	_this.parent().parent('tr').remove();

					 }else{
					 	
					 	alert('删除失败请重试');
					 }

				});
		 }
	});
    
    $('button').click(function(){
    	var bin='.bin'+$(this).attr('cid');    	
    	$(bin).siblings().hide();
    	$(bin).show();
    });

});