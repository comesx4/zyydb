  
  var mirrType=false;//是否是镜像市场
  function max_siz(){
	     //限制字数
	     $.each($('.length'),function(index,jj){
	     	if($(this).html().length>40){
	     		$(this).html($(this).html().substr(0,48)+'..');
	     	}

	     });
 	}

  /*
    @der 获取异步的data值
  */
  function get_data(checkbox){
    var str  = '';
    $.each(checkbox , function(index,data){     
      str += $(this).val()+',';
    });
    //去掉最右边的 ","
    str = str.substr(0,str.length-1);
    return str;
  }


//异步请求镜像市场数据的方法
 function get2(page){

 	 var region=$('#region').val(); //地域    
	 var goodsId=$('#goodsId').val();//产品的ID

 	 //异步获取的地址
 	 var page=page?page:getMirr+'?cid='+region+'&gid='+goodsId; 	

	 $.ajax({
	 	type:'get',
    	url:page,           
        dataType:'html',
        beforeSend:function(){
        	

        },
    	success:function(data){    		
    	      $('.mirr-right').html(data);
    	      max_siz();
    		
    	},
         

	 });

 }

$(function(){
   
   //购买清单的效果
  $(document).scroll(function(){
  	  var inforight=$('.right-fiexd');

  	  if($(this).scrollTop()>221){
  	  	 inforight.addClass('fixed');

  	  }else{
  	  	inforight.removeClass('fixed');

  	  }
  	
  });
  $('.bk-scope').width($('.info-right').width());



	//判断带宽部分的函数
	function judgeDaikuan(daikuans,bg,handle,mu){
			
			//让绿色背景的宽度等于按钮的left
			bg.width(handle.css('left'));
            	
             
            	//改变带宽表单的值
            	if(mu==2){
	            daikuans.val(Math.floor(parseInt(handle.css('left'))/mu));
	        	}else{
	        	daikuans.val(Math.floor(parseInt(handle.css('left'))*5));
	      
	        	}
                
                if(mu==2){
                	var size=200;
                }else{
                	var size=2000;
                }
                //限制表单值的大小
	            if(daikuans.val()>size){
	            	daikuans.val(size);

	            }
	            if(daikuans.val()<=0){
	            	if(mu==2)
	            	    daikuans.val('1');
	            	else
                    daikuans.val('5');

	            }
	         
           //限制按钮的left和背景的宽度
            if(parseInt(handle.css('left'))>400||parseInt(handle.css('left'))<0){
            	
		        	
		        	if(parseInt(handle.css('left'))>400){
		        		handle.css('left','400px');
		        		bg.css('width','400px');
		        	}      

		        		if(parseInt(handle.css('left'))<0){
		        			handle.css('left','0px');
		        			bg.css('width','0px');
        	            }


            return false;
           } 
          
           return true;


	}
   
   function move(handle,daikuans,bg,mu){ 
	    //带宽按钮拖动时的效果
		handle.mousedown(function(e){
			
			var x=e.clientX;
			var isdown=true;
			var money=$('#money');
	        var left=x-parseInt($(this).css('left'));
	        
	       
	        $(document).mousemove(function(event){
	        	if(!isdown) return '';
	        	
	        	handle.css('left',event.clientX-left);
	           
	               //调用判断函数
	               judgeDaikuan(daikuans,bg,handle,mu);
	            

	            money.html('正在计算价钱');

	        }).mouseup(function(){	
	        	if(isdown) price();
	            if(mu==2)  $('#wangluo').html('带宽'+$('.daikuans').val()+'Mbps（经典网络）');

	        	isdown=false;

	           
	        });


		});
	}


	move($('.handle1'),$('.daikuans'),$('.bg1'),2);
	// move($('.handle2'),$('#hard'),$('.bg2'),10);

	//带宽拖动按钮
	var handle1=$('.handle1');
	//带宽的表单
	var daikuans=$('.daikuans');
	//带宽的背景
	var bg1=$('.bg1');

	//硬盘拖动按钮
	var handle2=$('.handle2');
	//硬盘的表单
	var hard=$('#hard');
	//硬盘的背景
	var bg2=$('.bg2');

	//单击+带宽时
	$('.jia1').click(function(){
       handle1.css('left',Math.ceil(parseInt(handle1.css('left'))+2));
		//调用判断函数
       judgeDaikuan(daikuans,bg1,handle1,2);

		price();
		
	});

	//单击-带宽时
	$('.jian1').click(function(){
		 handle1.css('left',Math.ceil(parseInt(handle1.css('left'))-2));
		//调用判断函数
         judgeDaikuan(daikuans,bg1,handle1,2);
		 price();
	});

	// //单击+硬盘大小时
	// $('.jia2').click(function(){
 //         handle2.css('left',Math.ceil(parseInt(handle2.css('left'))+1));
	// 	//调用判断函数
 //         judgeDaikuan(hard,bg2,handle2,10);
	// 	 price();
		
	// });

	// //单击-硬盘大小时
	// $('.jian2').click(function(){
	// 	   handle2.css('left',Math.ceil(parseInt(handle2.css('left'))-1));
	// 	//调用判断函数
 //         judgeDaikuan(hard,bg2,handle2,10);
	// 	 price();
	// });

	//选择购买区域
	$('.region').find('li').click(function(){
		var index=$(this).index();
		$(this).siblings('li').removeClass('down');
		$(this).addClass('down');
		$('#region').val($(this).attr('value'));

		$('#diyu').html($(this).html()+'（可用区随机分配）');
        
        //如果当前在镜像市场
		if(mirrType){            

            $('.jingxiang').find('li').eq(0).click();


		}

		price();

	});
    
    //选择cpu
	$('.cpu').find('li').click(function(){
		var index=$(this).index();
		$(this).siblings('li').removeClass('down');
		$(this).addClass('down');
		$('#cpu').val($(this).attr('value'));
		var memory=$('.memory').find('li');	

		switch(index){
			//单击1核时
			case 0:
                 memory.hide();
                 memory.eq(1).show();
                 memory.eq(2).show();
                 memory.eq(3).show();
                 memory.eq(4).show();
                 memory.eq(0).show().click();
                
			     break;

			//单击2核时
			case 1:
                 memory.hide();
                 memory.eq(2).show().click();
                 memory.eq(3).show();
                 memory.eq(4).show();
                 memory.eq(5).show();
			     break;

			//单击4核时
			case 2:
                 memory.hide();               
                 memory.eq(3).show().click();
                 memory.eq(4).show();
                 memory.eq(5).show();
                
			     break;
			//单击8核时
			case 3:
			     memory.hide();
                 memory.eq(5).show();
                 memory.eq(6).show();
                 memory.eq(4).show().click();
			     break;

			//单击16核时
			case 4:               
                 memory.hide();
                 memory.last().show().click();
            
			     break;

		}

			$('#peizhi').html('CPU'+$(this).html()+'，内存'+$('#memory').val()+'核');
        
        price();
	});


	//选择内存
	$('.memory').find('li').click(function(){
		var index=$(this).index();
		$(this).siblings('li').removeClass('down');
		$(this).addClass('down');
		$('#memory').val($(this).attr('value'));

		$('#peizhi').html('CPU'+$('#cpu').val()+'核，内存'+$(this).html());

		price();

	});

	// 选择购买时间
	$('.time').find('li').click(function(){
		var index=$(this).index();
		$(this).siblings('li').removeClass('down');
		$(this).addClass('down');

		$('#time').val($(this).attr('value'));
        

	    $('#goumailiang').html($(this).html()+' x '+$('#num').val()+'台');

		price();

	});

	//购买台数离开焦点时候
	$('#num').blur(function(){
		//判断台数是否小于1
		if($(this).val()<1)
			$(this).val(1);
		
		var timedown=$('.time').find('.down').html();
		$('#goumailiang').html(timedown+' x '+$(this).val()+'台');
		price();
	});

	// //输入硬盘离开焦点的时候
	// $('#hard').blur(function(){

	// 	handle2.css('left',Math.ceil($(this).val()/5));
	// 	//调用判断函数
 //         judgeDaikuan(hard,bg2,handle2,10);
	// 	 price();

	// });
	
  //输入带宽离开焦点的时候
	$('.daikuans').blur(function(){
		handle1.css('left',Math.ceil($(this).val()*2));
		 judgeDaikuan(daikuans,bg1,handle1,2);
		price();

	});
    
    
	//计算价钱的方法
    function price(){
    	var money=$('#money'); //显示价钱的信息 
    	money.html('正在计算价钱');
    	setTimeout(function(){
    	var region=$('#region').val(); //地域
    	var cpu=$('#cpu').val();      //CPU
    	var memory=$('#memory').val();  //内存
    	var daikuans=$('.daikuans').val();//带宽
    	var disk = get_data($('.disk_all'));           //硬盘大小
    	var time=$('#time').val();    //购买时长
    	var num=$('#num').val();     //购买数量
    	var goodsId=$('#goodsId').val();//产品的ID

    	var countPrice=$('#countPrice');//总价钱

    	var stmt={'region':region,'goodsId':goodsId,'disk':disk,'cpu':cpu,'memory':memory,'daikuans':daikuans,'time':time,'num':num};//发送过去的JSON数据
        
        $.ajax({
        	type:'post',
        	url:getPrice,
            data:stmt,
            dataType:'json',
            beforeSend:function(){
            	money.html('正在计算价钱');

            },
        	success:function(data){
        		if(data.status==0){
        		money.html(data.mess);
        		countPrice.val(data.mess.replace('￥',''));
        		}else{
        			money.html('<span class="yuanjia">'+data.mess+'</span>'+data.data);
        				countPrice.val(data.data.replace('￥',''));
        		}
        		
        	},
        });
    	},300)
    	
     }

     
     //手动调用计算价钱方法
     price();

     //镜像市场和公共镜像的切换
     $('.jingxiang').find('li').click(function(){
     	var index=$(this).index();
     	
     	//如果切换到了镜像市场
     	if(index==1){
           mirrType=true;
           
           $('.xuanmirr').html('请选择一个镜像');
           $('input[name=xitong]').val(0);
   	       $('.mirroring').find('.showmirr').html('从镜像市场选择');
     	}else{
     	   mirrType=false;
     	   $('input[name=xitong]').val(0);
     	   $('.xz').html('请选择操作系统');
     	}

     	$(this).siblings('li').removeClass('down');
     	$(this).addClass('down');

     	$('.ismirroring').hide().eq(index).show();
     });


     //镜像市场的关闭按钮
     $('.close').click(function(){
         
     	  $(this).parents('#mirroring').hide();
     	  $('.ybc').hide();
     });
    

     //镜像市场对话框的显示
     $('.showmirr').click(function(){  

         $('#cat').find('dd').eq(0).click();
        
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
     	
     	$('#mirroring').show();

     	 
     });     


    //镜像分类选择的单击事件
    $('#cat').find('dd').click(function(){
    	var mirrType=$(this).attr('cid');
    	var region=$('#region').val(); //地域    
	    var goodsId=$('#goodsId').val();//产品的ID

	    var page=getMirr+'?cid='+region+'&gid='+goodsId+'&mirrType='+mirrType;

	    get2(page);

	    $(this).siblings('dd').removeClass('down');
	    $(this).addClass('down'); 	
    	
    });


    //事件委托，镜像的免费试用
    $('.mirr-right').on('click','.send',function(){

    	$('.xuanmirr').html($(this).parent().prev().find('.one').find('a').html());
    	$('.mirroring').find('.showmirr').html('重新选择镜像');

    	 $('input[name=xitong]').val($(this).attr('mid'));
    	 $('.close').click();


    });
   

   //选择系统的单击事件
   $('.xz').click(function(){
      $(this).next().show();

      var span=$(this).next().find('span');

      //判断是否添加了事件
      if(!$.data(span,'events')){
      	     
      	     //给span添加单击事件 
      	     span.click(function(){
      	     	 $('input[name=xitong]').val($(this).attr('value'));

      	     	 $('.xz').html($(this).html());
      	     	 $(this).parent().hide();
      	     });

      }
   });
   
   //判断是否是从镜像市场过来的
   if($('#mirrInfo').length==1){
   	   mirrType=true;
   	   var mirrId=$('#mirrInfo').attr('mid');  //镜像ID
   	   var mirrCity=$('#mirrInfo').attr('cid');//地域ID
   	   var mirrName=$('#mirrInfo').attr('name');//镜像名称

   	   $('.region').find('li[value=4]').click();

   	   $('.jingxiang').find('li').eq(1).click();

   	   $('.xuanmirr').html(mirrName);

   	   $('.mirroring').find('.showmirr').html('重新选择镜像');

   	   $('input[name=xitong]').val(mirrId);

   }


   //单击购买时
   $('.goumai').click(function(){
   	   if($('input[name=xitong]').val()==0){
   	   	    $('#error').html('请选择一个镜像！').show();
   	   	    setTimeout(function(){$('#error').hide()},2000);
   	   	    return false;
   	   }
   });

    
    //控制DT的大小
    $.each($('.config'),function(index){
    	var height=$(this).height();
    	$(this).find('dt').height(height);
    });

   if ( typeof(firstMemory) != 'undefined') {
       $('.cpu').find('li[value='+$('#cpu').val()+']').click();
       $('.memory').find('li[value='+firstMemory+']').click();
       $('.daikuans').blur();
       $('#hard').blur();
   }

   /*
        @der 添加硬盘
   */
   $('.add_disk').click(function(){
      var disk_length = $('.place_disk').length;
      
      if (disk_length >= 3) {     
           $('.disk_text').html('不能再增加');
      } else {
          $('.disk_text').html('增加一块');
      }

      //限制最多4块
      if (disk_length > 3) {         
          return false;
      }
      $('.disk_sum').html(parseInt($('.disk_sum').html()-1));

      $(this).parents('.caos').before("<div class='bk-form-row caos place_disk'><div class='remark'>数据盘:</div><div class='select'><div class='top option new_disk'><span>普通云盘</span><input type='text' name='disk[]' placeholder='5-2000G之间' class='disk_all' value = '5' /><span style=' color: #666;font-size: 13px;left: -65px;position: relative;'>GB</span><span>自动分配挂载点</span><span class='close disk_close'></span></div></div></div>");
       //控制高度
      $('.cunchu').find('.explain').height($('.cunchu').height());

      price();
   });

   /**
      @der 删除硬盘
   */
   $('.cunchu').on('click','.disk_close',function(){
      $('.disk_text').html('增加一块');
      $(this).parents('.place_disk').remove();
      $('.disk_sum').html(Math.ceil(parseInt($('.disk_sum').html())+1));
      //控制高度
      $('.cunchu').find('.explain').height(parseInt($('.cunchu').height())-62);

      price();
   });


    /*
        @der 硬盘离开焦点的事件
    */
    $('.cunchu').on('blur','.disk_all',function(){
       var disk_val = $(this).val();
       if (disk_val <= 0 || !disk_val.match(/^\d+$/)) {
          $(this).val(5);
       }
       //如果不是5的倍数
       if (disk_val%5 != 0) {
          $(this).val(Math.ceil(disk_val/5) * 5);
       }

        if (disk_val >2000) {
          $(this).val(2000);
        }

        price();
    });
});

  