$(function(){
	$('.main-left').height($('#main').height());
	

	$('.main-left').find('dd').hover(function(){
		$(this).find('span').css('color','#fff');
	},function(){
		$(this).find('span').css('color','#AEB9C2');		
	});

	/*列表的显示和收缩*/
	
	$('.list-child').click(function(){
		//收缩
		if ($(this).hasClass('living')) {
			$(this).removeClass('living').find('.glyphicon-menu-down').removeClass('glyphicon-menu-down').addClass('glyphicon-menu-right');
			$(this).next().slideUp(200,function(){
				$(this).hide();
			});
			
		 //展开
		} else {
			$(this).addClass('living').find('.glyphicon-menu-right').removeClass('glyphicon-menu-right').addClass('glyphicon-menu-down');
			$(this).next().slideDown(200);			
		}
	});

	//选中了云服务器时，上面自动展开
	$('.list-child').next().find('.on').parent().show().prev().addClass('living').find('span').last().removeClass('glyphicon-menu-right').addClass('glyphicon-menu-down');
	
});