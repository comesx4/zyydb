$(function () {
	//单击锁定时
	$('.table').on('click','.suo',function(){
		_this=$(this);
		if(_this.attr('aid')==null){
			var uid=_this.attr('uid');
			var data={'uid':uid,'type':1};

		}else{
		var aid=_this.attr('aid');
		var data={'aid':aid,'type':1};
	    }		
		//异步锁定
		$.post(userLock,data,function(data){
                         if(data==1){
			_this.parent().parent('tr').find('.static').html('锁定');
			_this.removeClass('suo').addClass('jie').html('[解锁]');
                          }
                    else{
                        alert("操作失败！");
                    }
		});

    //单击解锁时
    }).on('click','.jie',function(){
    	_this=$(this);
		if(_this.attr('aid')==null){
			var uid=_this.attr('uid');
			var data={'uid':uid,'type':0};

		}else{
		var aid=_this.attr('aid');
		var data={'aid':aid,'type':0};
	    }
		
		//异步锁定
		$.post(userLock,data,function(data){
                    if(data==1){
			_this.parent().parent('tr').find('.static').html('正常');
			_this.removeClass('jie').addClass('suo').html('[锁定]');
                    }
                    else{
                        alert("操作失败！");
                    }
		});
    });    
});