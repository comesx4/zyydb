$(function(){


	$('.banben').click(function(){
		$(this).next('div').show();
		
        //如果span还没有添加事件
        if(!$._data($(this).next('div').find('span')[0], "events")){
        	
        	//给span添加单击事件
			$(this).next('div').find('span').click(function(){
				
				$('.banben').eq(1).removeClass('red');
				
				$('input[name=type]').val($(this).attr('value'));

				$(this).parent('div').hide().prev('span').html($(this).html());

				get_price();

			});


        }
       
	});
   
	$(document).scroll(function(){

		if($(this).scrollTop()>162){
            $('#right-fixed').css({'position':'fixed','width':$('#main-right').width()-52});
		}else{
			$('#right-fixed').css('position','relative');

		}
	});

	//地域的单击事件
	$('.city').find('button').click(function(){
		$(this).siblings('button').removeClass('down');
		$(this).addClass('down');
		$('input[name=city]').val($(this).attr('value'));
        
		get_price();
	});


	//购买时长的单击事件
	$('.button2').find('button').click(function(){
		$(this).siblings('button').removeClass('down');
		$(this).addClass('down');
		$('input[name=time]').val($(this).attr('value'));

		get_price();
		
	});

	//产品的单击事件
	$('.goodsType').click(function(){
        var index=$(this).index();
        $(this).siblings('img').removeClass('down');
        $(this).addClass('down');
        $('input[name=goods]').val($(this).attr('value'));
        $('.isCity').hide().eq(index).show().find('button').eq(0).click();
	});

	//异步提交计算价钱的方法
	function get_price(){
		//如果值不为空就去异步
		if($('input[name=city]').val()!=''&&$('input[name=type]').val()!=''&&$('input[name=goods]').val()!=''){		
	        $('#price').html('计算中..');
	        setTimeout(function(){
	        	var goods=$('input[name=goods]').val(); //产品ID
				var city=$('input[name=city]').val();   //地域ID
				var type=$('input[name=type]').val();   //套餐
				var time=$('input[name=time]').val();   //购买时长
				//var version=$('input[name=version]').val();  //版本
				var mess={'gid':goods,'city':city,'type':type,'time':time};
	        	
	        	$.ajax({
	        		type:'post',
		        	url:getPrice,
		            data:mess,
		            dataType:'json',
		            beforeSend:function(){
		            	

		            },
	        		success:function(data){

	                  //判断是否有打折
	                  if(data.status==1){
	                        $('#price').html(data.data);
	                        $('#countPrice').val(data.data.replace('￥',''));
	                        $('.price').html(data.data.replace('￥','')+'元')
	                     
	                  }else{
	               		   	$('#price').html(data.mess);
	               		   	 $('#countPrice').val(data.mess.replace('￥',''));
	               		   	 $('.price').html(data.mess.replace('￥','')+'元');

	                  }
				    }
				});
	        

	        },300);
        }
			

	}


	$('.goodsType').eq(0).click(); //产品自动单击

    
    //表单的提交
	$('input[type=submit]').click(function(){
		if($('input[name=type]').val()==''){
		       $('.banben').eq(1).addClass('red');

		       return false;
		}

	});

    
    //自选配置的跳转
	$('.free').click(function(){
		var goods=$('input[name=goods]').val(); //产品ID
		var city=$('input[name=city]').val();   //地域ID

		mirrHref+='&id='+goods+'&city='+city;
		  
        
        window.location=mirrHref;

	});





	
	
});