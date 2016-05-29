
 function addBookmark(title,url) {
   if (window.sidebar) {
   window.sidebar.addPanel(title, url,"");
  } else if( document.all ) {
   window.external.AddFavorite( url, title);
  } else if( window.opera && window.print ) {
    return true;
  }
  }
  
$(function(){
    
  //导航区域的显示和隐藏
    $('.list').find('li').hover(function(){
      $(this).find('.nav').show();

    },function(){
      $(this).find('.nav').hide();


    });

    //用户信息的显示和隐藏
    $('.showdiv').hover(function(){
      $(this).find('.div').show();

    },function(){
       $(this).find('.div').hide();

    });

}); 
  
  //鼠标移动到菜单上自动弹出下拉菜单
$(function () {
    $('li.dropdown').mouseover(function () {
        $(this).addClass('open');
    }).mouseout(function () {
        $(this).removeClass('open');
    });
}); 