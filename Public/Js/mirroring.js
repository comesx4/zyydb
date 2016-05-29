$(function(){


function fiex(){
	$('#max-right').css({
		 'left':$('.ver-cont').width(),
		 'height':$('.ver-cont').height(),
		 'width':($('#main').width()-$('.ver-cont').width())/2		
	});
}
fiex();
   
   $('.down').find('.cat').show();
   $('.down').parent('.cat').show();

   $('.main-left').height($('#main').height());
 
    //最右边的浮动效果
    var fix=$('.right-fix');
	 $(document).scroll(function(){

	 	fiex();
        
	 	if($(document).scrollTop()>139){
             fix.css({'position':'fixed','top':0}).width($('.main-right').width());
	 	}else{
	 		fix.css('position','relative');
	 	}
	 	
	 });
    

});