$(function(){
   
	$('.del').click(function(){
		var bool=confirm('确认删除?');

	     if(bool){
				_this=$(this);
		        if(_this.attr('cid')!=null){
				   var cid=_this.attr('cid');
				   var data={'cid':cid};
			    }else{
			       var pid=_this.attr('pid');
				   var data={'pid':pid};
			    }
				
		       
				$.post(delCat,data,function(data){
					
					 if(data==1){
					 	_this.parent().parent('tr').remove();

					 }else{
					 	
					 	alert('删除失败请重试');
					 }

				});
		 }
	});
});