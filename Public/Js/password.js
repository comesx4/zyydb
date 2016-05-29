$(function () {

 $.validator.addMethod('tele',function(value,element){
	 var tel=/^1[3|4|5|8][0-9]\d{8}$/;//必须以字母开头，并且长度在5-16之间
	 return this.optional(element)||(tel.test(value));
 
 },'手机格式错误');


$('form[name=setinfo]').validate({
   errorElement:'span', 
   rules:{
       oldPassword:{
	       required:true,
	          minlength:5
	     },
	   password:{           
	   	  required:true,
                  minlength:5
	   },
	   rePassword:{	   	  
	   	  required:true,  
                   minlength:5,
                  equalTo: "#password"
	   }
   },
   
   messages:{
	  
	  oldPassword:{
	       required:'请输入原密码',
                minlength:jQuery.format("原密码不能小于{0}个字 符")
	     },
	
	    password:{           
	   	  required:'请输入密码',
                  minlength:jQuery.format("密码不能小于{0}个字 符")
	   },
            rePassword: {
                required: "请输入确认密码",
                minlength: jQuery.format("确认密码不能小于{0}个字 符"),
                equalTo: "两次输入密码不一致不一致"
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
