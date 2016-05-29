$(function(){
    $('.del').click(function(){
        var is=confirm('确定废除？');

        if(!is)
        	return false;

		var tid=$(this).attr('tid');
		var _this=$(this);
		//异步废除订单
        
        $.post(orderDel,{'id':tid},function(data){
        	if(data==1){ 
                _this.parent().parent().remove();

        	}else{
        		alert(data);
        		alert('废除失败，请重试');
        	}

        });
    });


    //控制产品名称的字数
    $.each($('.goods'),function(index){
           
           if($(this).html().length>7){
                $(this).html($(this).html().substr(0,7)+'...');
           }
    });


});