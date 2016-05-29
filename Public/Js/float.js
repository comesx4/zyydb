
	

/*
 *even 触发弹窗的单击元素
  eventobj 要弹出的对话框
  moveevent 要移动的区域
 *
 *
 * */

function createdialog(even,eventobj,moveevent){
even.click(function(){
	 moveevent.css('cursor','move');
	
	//创建灰色背景图层
	$('<div class = "ybc"></div>').appendTo('body').css({
 		'width' : $(document).width(),
 		'height' : $(document).height(),
 		'position' : 'absolute',
 		'top' : 0,
 		'left' : 0,
 		'z-index' : 100,
 		'opacity' : 0.3,
 		'filter' : 'Alpha(Opacity = 30)',
 		'backgroundColor' : '#000'
 	});

	//定位对话的位置为居中，并且显示

eventobj.css({
	'top':($(window).height()-eventobj.height())/2,
    'left':($(window).width()-eventobj.width())/2,
	'z-index':101
        
 

}).show();


//处理拖动
var bool=false;
moveevent.mousedown(function(event){
	var sy=event.screenY;
		var sx=event.screenX;
		    lefts=sx-parseInt(eventobj.css('left'));
	        tops=sy-parseInt(eventobj.css('top'));
	bool=true;

});
$(document).mousemove(function(event){
		if(!bool) return ;
		
		eventobj.css({
			'left':event.screenX-lefts,
			'top':event.screenY-tops

		
		});
	



}).mouseup(function(){
    bool=false;
   

});






});

	//关闭事件
$('.close').click(function(){
	eventobj.hide();
	$('.ybc').hide();










});
}



   



