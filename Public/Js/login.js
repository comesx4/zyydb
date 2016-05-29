$(function () {
 $.validator.addMethod('lname',function(value,element){
	 var tel=/^[a-z][0-9a-z]{2,15}$/;//必须以字母开头，并且长度在5-16之间
	 return this.optional(element)||(tel.test(value));
 
 },'账号必须以字母开头，并且长度在3-16之间');



$('form[name=dologin]').validate({
   errorElement:'span',
 
   rules:{
         username:{
		   required:true,
		   email:true,		  
        },
           password:{
		   required:true,		  
        },
	    code:{
		   required:true,
		  
		   remote:{
		        url:checkcode,
		        type:'post'		   
		    }
	    },
	agreement:{
             required:true,
        }

   },
   
   messages:{
	   username:{
                required:'账号不能为空',    
                email:'请输入正确的电子邮箱格式'  

	   },
             password:{
                required:'必需输入密码',    

	   },
	   code:{
                  required:'验证码不能为空',
		  remote:'验证码错误'

	   },
            agreement:{
                    required:'必须同意服务协议',
	   }
        
   
   },
   success:function(label){
        label.addClass('succ').html('&nbsp;');
   },
   highlight:function(element,errorClass){
	   $(element).next('span').removeClass('succ');//错误时候移除succ类   
   }
});
});
