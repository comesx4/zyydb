$(function(){
	$('.content-list').find('dt').toggle(function(){
		$(this).addClass('bbbb').nextAll('dd').hide();

	},function(){
		$(this).removeClass('bbbb').nextAll('dd').show();

	});
    
    //计算右边大块DIV的高度
	var conheight=$('.content-right').height();

	// //滚动事件
	// $(document).scroll(function(){

	// 	//产品属性
	// 	if($(this).scrollTop()>550){
	// 		$('.nav-wei').addClass('fixed');

	// 	}else{
	// 		$('.nav-wei').removeClass('fixed');

	// 	}
        
 //        //左边列表
	// 	if($(this).scrollTop()>$('.conts').height()+Number(108)){
	// 		$('.conts').addClass('fixed');


	// 	}else{
 //            $('.conts').removeClass('fixed');
	// 	}

	// });
	
	//滚动事件
	$(document).scroll(function(){
		if ( $(this).scrollTop() > 374) {
			$('.nav-wei').css('position','fixed');
		} else {
			$('.nav-wei').css('position','absolute');
		}
	});

	$('.max-right').height(conheight);

	/*
		@der 购买事件
	*/
	$('.buy').click(function(){
		var time = $(this).parent().prev().find('select').val();
		var id   = $(this).attr('pid');
		var gid  = $(this).attr('gid');
		$('form').append("<input type='hidden' name='id' value='"+id+"' /><input type='hidden' name='time' value='"+time+"' /><input type='hidden' name='gid' value='"+gid+"' />");

		$('input[type=submit]').click();
	});
});