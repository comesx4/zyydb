$(function(){
    
    /*
      问题类型的选择选择事件
    */
    $('.type').click(function(){
    	var cid=$(this).attr('cid');


    	$.post(getAttr,{'cid':cid},function(data){

    		$('.temporary').remove();
    		if(data!=0){
    			$('.prev').before(data);
    		}
 
    	});

    });
    
  

});