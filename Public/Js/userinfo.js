$(function(){
	//弹出对话框
	createdialog($('.showface'),$('#setface'),$('#setface-top'));

	//对话框中的内容切换
	$('.list-face').find('li').click(function(){
		var index=$(this).index();
		$(this).siblings('li').css('background','none');
		$(this).css('background','#fff');
		$('.faceshow').hide().eq(index).show();



	});

     //选中图片的预览效果
	$('.setface-pic').find('img').click(function(){
		
		$(this).siblings('img').removeClass('border');
		$(this).addClass('border');
		$('.liulang').attr('src',$(this).attr('src'));


	});

	//异步修改头像
    $('.save-face').click(function(){
    	var face=$('.border').attr('sr');

    	//异步修改头像
    	$.post(setFace,{'face':face},function(data){
    		   if(data==1)
    		   	  window.location='';
    		   	else
    		   		alert('图像修改失败，请重试');


    	});
    });

});