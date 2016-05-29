$(function(){
	$('input[type=text]').focus(function(){
		$(this).val('');
	});

    var li=$('.tab-xnav').find('li');
    
	//图片轮播
	li.click(function(){
		var index=$(this).index();
      i = index;
		li.removeClass('liu');
		$(this).addClass('liu');
		var div=$('.tab-lbpannel');
		div.css('display','none');
		div.eq(index).show();
	});

	//图片自动轮播
	var i=1;
	setInterval(function(){
		if(i==5){
			i=0;
		}
		var sum=li.length;
		
		li.eq(i).click();
         i++;

	},3000);

   // //购买物品的经过和离开效果
   // $('.hover').hover(function(){
   // 	    $(this).find('dd').hide();
   // 	    $(this).find('.hidden').show();
   // 	    $(this).css('background','#1CA8DD');



   // },function(){
   // 	    $(this).css('background','#fff');
   // 	    $(this).find('dd').show();
   // 	    $(this).find('.one').hide();
   // });

   //购买物品的经过和离开效果
   $('.hovers').hover(function(){
   	    $(this).find('dd').hide();
   	    $(this).find('.hidden').show();
   	    $(this).css('background','#1CA8DD');



   },function(){
   	    $(this).css('background','#F8F8F8');
   	    $(this).find('dd').show();
   	    $(this).find('.one').hide();
   });



   //购买物品的切换
   $('.onleft').toggle(function(){
   	
   	    var content=$('.left4');
   	    content.hide();

   	    content.eq(1).show();
   	    
   },function(){
   	    var content=$('.left4');
   	    content.hide();

   	    content.eq(0).show();

   });

  
   $('.change').find('li').click(function(){
      var index = $(this).index();
      $(this).addClass('on').siblings('li').removeClass('on');

      $('.client_image').find('ul').hide().eq(index).show();
   });











});