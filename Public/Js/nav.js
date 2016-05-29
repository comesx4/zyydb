$(function(){
	//用户信息的显示和隐藏
    $('.showdiv').hover(function(){
      $(this).find('.div').show();

    },function(){
       $(this).find('.div').hide();

    });
});