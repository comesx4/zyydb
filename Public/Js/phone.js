$(function () {

 $.validator.addMethod('tele',function(value,element){
	 var tel=/^1[3|4|5|8][0-9]\d{8}$/;//必须以字母开头，并且长度在5-16之间
	 return this.optional(element)||(tel.test(value));
 
 },'手机格式错误');


$('form[name=setinfo]').validate({
   errorElement:'span', 
        rules: {
            oldphone: {
                required: true,
                tele: true
            },
            phone: {
                required: true,
                tele: true
            },
        },
   
   messages:{
	  
	  oldphone:{
	   	  tele:'请输出正确的手机号码',
	   	  required:'原手机号码不能为空'
	   },	
	  phone:{
	   	  tele:'请输出正确的手机号码',
	   	  required:'新手机号码不能为空'
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
