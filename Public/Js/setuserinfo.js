$(function () {

 $.validator.addMethod('tele',function(value,element){
	 var tel=/^1[3|4|5|8][0-9]\d{8}$/;//必须以字母开头，并且长度在5-16之间
	 return this.optional(element)||(tel.test(value));
 
 },'手机格式错误');


$('form[name=register]').validate({
   errorElement:'span',
 
        rules: {
            password: {
                required: true,
                rangelength: [3, 16]

            },
            dbpassword: {
                required: true,
                equalTo: "#password",
            },
            telephone: {
                tele: true,
                required: true,
            },
            verify: {
                required: true,
                remote: {
                    url: checkVerify,
                    type: 'post',
                    data: {
                        'verify': function () {
                            return $('input[name=verify]').val();
                        },
                        'telephone': function () {
                            return $('input[name=telephone]').val();
                        }
                    }
                }
            }

        },
   
   messages:{
	  
	   password:{
	   	required:'密码不能为空',
	   	rangelength:'密码必须在{0}到{1}之间'
	   },
	   dbpassword:{
	   	required:'确认密码不能为空',
	   	equalTo:'两次密码不一致'
	   },
	   telephone:{
	   	  tele:'请输出正确的手机号码',
	   	  required:'手机号码不能为空',

	   },
	   verify:{	
	   	  required:'验证码不能为空',
	   	  remote:'验证码有误'
	   }
   },
   success:function(label){
           label.addClass('succ').html('&nbsp;');
   },
   highlight:function(element,errorClass){
	   $(element).next('span').removeClass('succ');//错误时候移除succ类   
   }


});
	
	/*单击获取短信*/
	$('#information').click(function(){
		var telephone = $('input[name=telephone]');
		//验证手机号码格式
		if ( !telephone.val().match(/^1[3|4|5|8][0-9]\d{8}$/) ) {
			alert('手机格式有误！');
			telephone.focus();
			return false;
		}
		//禁止修改手机
		$('input[name=telephone]').attr('readonly',true);

		//计时器
		var i =60;
		$('#information').attr('disabled','disabled').val("剩余"+i+"秒");
		var time = setInterval(function(){
			if (i>0) {
				i--;
				$('#information').attr('disabled','disabled').val("剩余"+i+"秒");
				
			} else {
				//恢复手机号码可修改
				$('input[name=telephone]').attr('readonly',false);
				$('#information').attr('disabled',false).val('获取短信');
				window.clearInterval(time);
			}
		},1000);

		//发送请求，获取短信
		$.post(sendInformation,{telephone:telephone.val()},function(data){
			if (data.status == 0) {
				alert(data.message);
			}                     
		},'json');

	});

	/*提交表单时*/
	$('input[type=submit]').click(function(){

	});


});
