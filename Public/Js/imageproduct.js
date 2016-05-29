$(function(){


//图片轮播
$('#img-show').find('li').bind('click',function(){
	index = $(this).index();
	cli = index;
	$('.leri').animate({
		'left':index*(-1023)
	},500);

	$(this).siblings('li').removeClass('to');
	$(this).addClass('to');
});


//自动轮播
var cli=1;
setInterval(function(){	
	$('#img-show').find('li').eq(cli).click();
	++cli;
	if(cli>3)
		cli=0;

},5000);


//最有边的白色部分
function fiex(){
	$('#max-right').css({
		 'left':$('.ver-cont').width(),
		 'height':$('.ver-cont').height(),
		 'width':($('#main').width()-$('.ver-cont').width())/2		
	});
}
fiex();
   
   $('.down').find('.cat').show();

   $('.main-left').height($('#main').height());
 
    //最右边的浮动效果
    var fix=$('.right-fix');
	 $(document).scroll(function(){

	 	fiex();
        
	 	if($(document).scrollTop()>432){
             fix.css({'position':'fixed','top':0}).width($('.limit-right').width());
	 	}else{
	 		fix.css('position','relative');
	 	}
	 	
	 });

	
    

});