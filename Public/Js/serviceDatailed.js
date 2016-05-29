$(function(){
	$('.spec').find('li').click(function(){
           var sid=$(this).attr('sid');
           $(this).siblings('li').removeClass('down');
           $(this).addClass('down');
           
           //异步获取价钱
           $.post(getPrice,{'sid':sid},function(data){
                    
                    //规格ID
                    $('#spec_id').val(sid);

                     //无折扣价的情况
                    if(data.discount==0){
                         $('.type1').show();
                         $('.type2').hide();
                         
                         $('.money').html(data.price);
                         $('#price').val(data.price);
                         
                    //有折扣价的情况    
                    }else{

                    	 $('.type1').hide();
                         $('.type2').show();

                         $('.no-money').html('￥'+data.price);
                         $('.discount').html(data.discount);
                    	 $('#price').val(data.discount);
                    }
           },'json');
	});
  
  $('.spec').find('li').eq(0).click()
  
});  