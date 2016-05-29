$(function(){

	/*
      单击一键投诉
	*/
	$('#advice').click(function(){
		//创建灰色背景图层
		$('<div class = "ybc"></div>').appendTo('body').css({
	 		'width' : $(document).width(),
	 		'height' : $(document).height(),
	 		'position' : 'absolute',
	 		'top' : 0,
	 		'left' : 0,
	 		'z-index' : 14,
	 		'opacity' : 0.3,
	 		'filter' : 'Alpha(Opacity = 30)',
	 		'backgroundColor' : '#000'
 		});

		$('#complaint').show();
		return false;
	});

	$('.close').click(function(){
		 $('.ybc').remove();
		 $('#complaint').hide();
	});





});