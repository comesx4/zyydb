$(function(){

	$('.se-lb').toggle(function(){
        $(this).nextAll('dd').show();
         var _this=$(this);

         //判断下面是否有问题
         if(_this.hasClass('get-sc')){
         	 var sid=$(this).attr('sid');
            
            //如果没有异步过才去异步
         	if(_this.next('dd').length==0){
	         	 //异步获取问题标题等信息
	             $.post(getScence,{'sid':sid,'type':1,'has':1},function(data){
	             	   _this.after(data);
	             	   _this.nextAll('dd').show();
	             });
            }

         }

         
         //如果没有加入事件才去加入事件
         if(!$.data($(this).nextAll('dd')[0],'events')){
		         
		         //第二层问题下面还有问题
		         $(this).nextAll('.y-chid').click(function(){
		         	 var sid=$(this).attr('sid');
                     
                     //异步获取问题标题等信息
                     $.post(getScence,{'sid':sid,'type':1},function(data){
                     	   $('.right-list').find('li').remove();
                     	   $('.select-right').find('*').show();
                     	   $('#uc-recommend').hide();
                     	   $('#uc-resolve-form').hide();
                           $('.right-list').append(data);
                     });


		         	$(this).siblings('dd').removeClass('down');
		         	$(this).addClass('down');
		         });
		 }



	},function(){
         $(this).nextAll('dd').hide();
	});

	
	//右边问题的单击事件
	$('.right-list').on('click','li',function(){
        
		//异步获取问题的答案
        var sid=$(this).attr('sid');       
         showForm(sid);

		$(this).siblings('li').removeClass('down');
		$(this).addClass('down');

	});

    
    //单击评价时
	$('#uc-recommend').on('click','.history',function(){
		var type=$(this).hasClass('yes')?'good':'poor';
		var sid=$(this).attr('sid');

		$.post(orInc,{'sid':sid,'type':type},function(data){
			$('.history').css({'background':'#ccc','color':'#fff','border':'none'}).removeClass('history');
			
		});
        
        //如果单击的是没用，提交工单
		if(type=='poor'){
			$('#uc-resolve-form').show();
	
		}
	

	});

	$('a.no').click(function(){
		$('#uc-resolve-form').show();
	});

	
	//左半边的问题单击时
	$('.select-left').on('click','.showForm',function(){
		var sid=$(this).attr('sid');
		$(this).siblings('dd').removeClass('down');
		$(this).addClass('down');

		$('.select-right').find('*').hide();
		showForm(sid);

	});


    
    //用来显示问题的答案
	function showForm(sid){
		$.post(getScence,{'sid':sid,'type':0},function(data){
         	    $('#uc-resolve-form').hide();
         	   $('#uc-recommend').show().find('h3').html(data.title);
         	   $('#anwer').html(data.anwer+"<div class='bot-btns'>该条建议是否对你有用：<span sid="+data.id+" class='yes history'>有用</span><span sid="+data.id+" class='no history'>没用，提交工单</span></div>");
         	  
         },'json');
	}



   

	




});