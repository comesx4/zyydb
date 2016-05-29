$(function(){

	var arr=new Array();
    var type;
    //单击修改按钮时创建出表单
    $('tbody').on('click','.save',function(){        
 
    	//获取这一行的内容区域
    	var info=$(this).parent().parent('tr').find('.xinfo');
    
    	//循环遍历每个内容
        var status=$(this).attr('status');
    	$.each(info,function(index,data){
    		  type=getType(index);
    		  arr[index]=info.eq(index).html();
          
           //判断是不是职位
           if(index==0){            
            $.post(Getlistbyarr,{'key':'status','arr':'UserDindanType','val':status,'mode':0},function(data){		
                if (data.status == 0) {
				info.eq(0).html(data.message);
                            }  else{
                              alert(data.message);
                        } 
		},'json');
           	return false;
           }

    		//修改TD的内容（创建出input编辑区域）
    		info.eq(index).html('<input type="text" name="'+type+'" value="'+arr[index]+'"/>');
    		
    	});

    	$(this).html('<button class="xadd">保存</button><button class="no">取消</button>').removeClass('save');
            

            //这些变量必须在外面声明，因为这些变量是给取消按钮使用的
          var _this=$(this).parent().parent('tr').find('.xinfo');
          var __this=$(this);
    	  var position=arr[0];
            

        //给保存按钮添加事件
        $('.xadd').click(function(){
            //这些变量必须在里面声明，因为这些内容是要修改的
//        	var name=_this.eq(0).find('input').val();
//    	    var email=_this.eq(1).find('input').val();
//    	    var phone=_this.eq(2).find('input').val();
    	    var status=_this.eq(0).find('select').val();
            var  position=_this.eq(0).find('select').find("option:selected").text();
    	    var cid=$(this).parent().attr('cid');
            var salt=$(this).parent().attr('salt');
            
            //异步修改订单状态
    	    $.post(setContacter,{'status':status,'salt':salt,'cid':cid},function(data){
                         
                          _this.html('');
//                          _this.eq(0).html(name);
//                          _this.eq(1).html(email);
//                          _this.eq(2).html(phone);
                          _this.eq(0).html(position);                         
                          if (data.status == 0) {
                              __this.attr('status',status);
                              __this.html('修改').addClass('save'); 
                                
			}  else{
                              alert(data.message);
                        } 
		},'json');
    	    });    
         
            //给取消添加事件
    	    $('.no').click(function(){
                // checkText=$("#select_id").find("option:selected").text();
//              position=_this.eq(0).find('select').find("option:selected").text();
    	    	              _this.html('');
//                       	  _this.eq(0).html(name);
//                       	  _this.eq(1).html(email);
//                       	  _this.eq(2).html(phone);
                       	  _this.eq(0).html(position);
                       	  __this.html('修改').addClass('save');        	 


    	    });

    	


       //时间委托删除联系人
    }).on('click','.delete-contacter',function(){
    	var cid=$(this).attr('cid');
    	var tr=$(this).parent().parent('tr');
    	var bool=confirm('确认删除该联系人?');

      if(bool){
    	  $.post(deleteContacter,{'cid':cid},function(data){

    		     //如果删除成功
    		     if(data==1){
    		     	  tr.remove();
    		     }else{
    		     	  alert('删除失败请重试');

    		     }

    	  });
       }



    	
    })

    //获取type
    function getType(str){
    	var name;
    	switch(str){
    		case 0:
    		   name='name';
    		   break;
    		case 1:
    		   name='email';
    		   break;   
    	    case 2:
    		   name='phone';
    		   break;
    		case 3:
    		   name='position';
    		   break;
    	}
    	return name;

    }  


});