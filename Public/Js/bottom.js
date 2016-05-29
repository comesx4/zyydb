$(function(){

	function createdialogs(even,eventobj,moveevent){
even.click(function(){
	 moveevent.css('cursor','move');
	
	


eventobj.show();


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

});
}
   //打开提问对话框
   createdialogs($('.issue'),$('#cloud-helper-box'),$('.clude-header'));

    //提问对话框问题的答案显示
	$('.clude-list').find('li').find('a').toggle(function(){
		$(this).next('div').slideDown(200);

	},function(){
		$(this).next('div').slideUp(200);

	});



	//小云问答的切换
    $('.clude-redirect2').click(function(){
    	$('.cloud-top2').hide();
    	$('.cloud-top1').show();

    });
    $('.clude-redirect1').click(function(){
    	$('.cloud-top1').hide();
    	$('.cloud-top2').show();
    });

    //小云的异步提问
    $('.tiwen').click(function(){

    	_this=$(this);
    	var input=$(this).prev('input');
    	var values=input.val();
    	
    	//如果内容为空就不让提交问题
    	if(values==''){
    		input.focus();
    		return false;
    	}

    	input.val('');
    	
    		$.post(xiaoyun,{'title':values},function(data){
    			var content=$('.xiaoyuncontent');
    			content.append(data);
    			//如果是第一个页面就让他跳转到第二个页面
    			if(_this.attr('class')=='tiwen'){
    				$('.clude-redirect1').click();

    			}
    			//让滚动条始终在最下面
    			content.get(0).scrollTop =content.get(0).scrollHeight; 
    			input.focus();

    		});
    	
    });


    //删除消息记录
    $('.unlink').click(function(){
    	bool=confirm('确定清除记录?');
    	if(bool){
    	$('.xiaoyuncontent').find('dl').remove();
        }
    });
    var autotop=$('.autotop');
    

    //移动到顶部
    autotop.click(function(){
    	$(document).scrollTop(0);

    });

    //滚动事件
    $(document).scroll(function(){
    	 if($(document).scrollTop()>150){
    	      autotop.show();

         }else{
    	      autotop.hide();
         }

    });
});