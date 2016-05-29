$(function(){
	 $('#receiptYes').click(function(){
	 
	 	$('.brick-form').show();
	 });

	 $('#receiptNo').click(function(){
        $('.brick-form').hide();

	 });

	 $('#remark').click(function(){
	 		
           if($(this).attr('checked')=='checked'){
          
           	    $('.remark').show();
           }else{
           	    $('.remark').hide();
           }
	 });
});