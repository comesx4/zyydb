$(function(){
	$('.adds').click(function(){		
		var group=$(this).parents('.group'); 
		var le=$('input[type=submit]').parent().parent(); 
		//克隆节点
		group.clone().insertBefore(le);
	});
});

$(function (){
    $('.dels').click(function(){
       
		if(confirm("确认删除")){ 
                    $(this).parent().parent().remove(); 
                 //alert("删除");
        }
	});
	
});
/*
function addrole(){		
		var group=$(this).parents('.group'); 
		var le=$('input[type=submit]').parent().parent(); 
		//克隆节点
		group.clone().insertBefore(le);
	}
        
function delerole(){       
		if(confirm("确认删除")){ 
                    $(this).parent().parent().remove();  }
	}*/