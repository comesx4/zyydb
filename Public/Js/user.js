$(function(){

	var arr=new Array();
    var type;
    //单击修改按钮时创建出表单
    $('tbody').on('click','.save',function(){
    	//获取这一行的内容区域
    	var info=$(this).parent().parent('tr').find('.info');
    
    	//循环遍历每个内容
    	$.each(info,function(index,data){
    		  type=getType(index);
    		  arr[index]=info.eq(index).html();
          
           //判断是不是职位
           if(index==3){
           	info.eq(3).html('<select name="position">\n\
                <option value="">请选择职位</option>\n\
                <option value="技术负责人" selected="selected">技术负责人</option>\n\
                <option value="运维负责人">运维负责人</option>\n\
                <option value="项目负责人">项目负责人</option>\n\
                <option value="财务负责人">财务负责人</option>\n\
                <option value="CEO">CEO</option>\n\
                <option value="其它">其它</option></select>');
           	return false;
           }

    		//修改TD的内容（创建出input编辑区域）
    		info.eq(index).html('<input type="text" name="'+type+'" value="'+arr[index]+'"/>');
    		
    	});

    	$(this).html('<button class="add">保存</button><button class="no">取消</button>').removeClass('save');
            

            //这些变量必须在外面声明，因为这些变量是给取消按钮使用的
          var _this=$(this).parent().parent('tr').find('.info');
        	var __this=$(this);
    	    var name=_this.eq(0).find('input').val();
    	    var email=_this.eq(1).find('input').val();
    	    var phone=_this.eq(2).find('input').val();
    	    var position=_this.eq(3).find('select').val();

        //给保存按钮添加事件
        $('.add').click(function(){
            //这些变量必须在里面声明，因为这些内容是要修改的
        	var name=_this.eq(0).find('input').val();
    	    var email=_this.eq(1).find('input').val();
    	    var phone=_this.eq(2).find('input').val();
    	    var position=_this.eq(3).find('select').val();
    	   
    	    var cid=$(this).parent().attr('cid');
            
            //异步保存联系人信息
    	    $.post(setContacter,{'name':name,'email':email,'phone':phone,'position':position,'cid':cid},function(data){
                         
                          _this.html('');
                          _this.eq(0).html(name);
                          _this.eq(1).html(email);
                          _this.eq(2).html(phone);
                          _this.eq(3).html(position);
                          __this.html('修改').addClass('save');  
                       //如果修改不成功
                       if(data!=1){
                              alert('修改失败，您并没有修改内容');
                       	       	 
                       }

                       	 
                       

                       
                      
    	    });
        });
         
            //给取消添加事件
    	    $('.no').click(function(){
    	    	              _this.html('');
                       	  _this.eq(0).html(name);
                       	  _this.eq(1).html(email);
                       	  _this.eq(2).html(phone);
                       	  _this.eq(3).html(position);
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

    //异步添加联系人
    $('.add-contacter').click(function(){
    	    var _this=$(this).parent().parent('tr').find('.info');
    	    
    	    //如果内容为空就不让提交
    	    if(_this.find('input').val()==''||_this.find('select').val()=='')
                   return false;
        	
    	    var name=_this.eq(0).find('input').val();
    	    var email=_this.eq(1).find('input').val();
    	    var phone=_this.eq(2).find('input').val();
    	    var position=_this.eq(3).find('select').val();

    	    //异步添加联系人信息
    	    $.post(setContacter,{'name':name,'email':email,'phone':phone,'position':position},function(data){
                      if(data>0){
                      	$('.tbspacer').before('<tr><td class="info">'+name+'</td><td class="info">'+email+'</td><td class="info">'+phone+'</td><td class="info">'+position+'</td><td><a style="cursor:pointer;margin-right:10px;" class="save"  cid="'+data+'">修改</a><a style="cursor:pointer;" class="delete-contacter" cid="'+data+'" >删除</a></td></tr>');
                         _this.find('input').val('');
                      }else{
                      	alert('添加失败');
                      }

                      
    	    });

    });

    


});