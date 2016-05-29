$(function(){
	
	$('.left-list').find('.liv').find('span').toggle(function(){
		$(this).addClass('show').nextAll('div').show();
	},function(){
		$(this).removeClass('show').nextAll('div').hide();
	});

   //经过所有分类时
   $('.all-cat').find('h3').hover(function(){

   	     //隐藏详细列表
   	     $('.xiangxi').hide();
   	     $('.max-width').show();

   	     //判断是否添加了事件
   	     if(!$.data($('.max-width')[0],'events')){   
			   	     
			   	     $('.max-width').hover(function(){
			       //判断最左边有没有添加事件
			    	 if(!$.data($('.ding')[0],'events')){
						
						//最左边的经过效果
						$('.ding').hover(function(){
							 $('.hnavbar1').hide().find('dl').hide();
							 var value=$(this).attr('value');

						     $('.hnavbar1').eq(0).show().find('dl[value='+value+']').show();
						});
			            
			            //判断中间部分是否添加了事件
						if(!$.data($('.zhong')[0],'events')){   
						  
						     //中间部分的经过效果
						     $('.zhong').hover(function(){
						     	 $('.hnavbar1').eq(1).hide().find('dl').hide();
						     	 var value=$(this).attr('value');		     

						     	  $('.hnavbar1').eq(1).show().find('dl[value='+value+']').show();
						     

						     });

						     //中间部分没有子分类的事件
						     $('.none').hover(function(){
						     	 $('.hnavbar1').eq(1).hide().find('dl').hide();


						     });
						}
				             
				            
					 }
					
				},function(){
					$('.hnavbar1').hide().find('dl').hide();
					$(this).hide();
					 $('.xiangxi').show();
				});
	    }

   });

   
    
    //自动展开
    $('.diceng').parents('.liv').find('span').addClass('down').nextAll('.hidden').show();
    
    //设置高度
    setTimeout(function(){
    	if($('#mian').height()<500){
    		$('#mian').height(550);
    	}else{
    		var height=$('.main-left').height()>$('.main-right').height()?$('.main-left').height():$('.main-right').height();
    		$('#mian').height(height);
    	}
    },1000);

    	

    


    
   
});	