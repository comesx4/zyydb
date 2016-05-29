$(function(){
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
	});



	/*
      新手向导导航栏切换
	*/
	$('.box').find('li').click(function(){
		var index=$(this).index();
        $(this).siblings('li').removeClass('down');
        $(this).addClass('down');

        $('.bd').hide().eq(index).show();

	});

	
	


});