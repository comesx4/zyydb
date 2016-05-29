$(function () {

 $.validator.addMethod('tele',function(value,element){
	 var tel=/^1[3|4|5|8][0-9]\d{8}$/;//必须以字母开头，并且长度在5-16之间
	 return this.optional(element)||(tel.test(value));
 
 },'手机格式错误');


$('form[name=setinfo]').validate({
   errorElement:'span', 
   rules:{
       trueName:{
	       required:true,
	       rangelength:[1,16]	 
	  
	     },
	 //  phone:{
	 //  	 tele:true,
	  // 	  required:true,
	 //  },
	   biz:{	   	  
	   	  required:true,   	
	   }

   },
   
   messages:{
	  
	   trueName:{
	   	required:'真实姓名不能为空',
	   	rangelength:'真实姓名必须在{0}到{1}之间'
	   },
	
	   phone:{
	   	  tele:'请输出正确的手机号码',
	   	  required:'手机号码不能为空',

	   },
	   biz:{	
	   	  required:'主营业务不能为空',	   
	   }
   },
   success:function(label){
           label.addClass('succ').html('&nbsp;');
   },
   highlight:function(element,errorClass){
	   $(element).next('span').removeClass('succ');//错误时候移除succ类   
   }


});
	

	/*提交表单时*/
    $('input[type=button]').click(function(){
        
        var r = $('#setinfo').valid();
        if(r){     
            var params = $("#setinfo").serialize();
            $.post(setuserinfo,params,function(data){
		//alert(setuserinfo);	
                if (data.status == 0) {
				alert(data.message);
			}  else{
                              alert(data.message);
                        } 
		},'json');    
            return false;
            
              }
        else{
            alert("未通过验证");
        }
        
	});


});
