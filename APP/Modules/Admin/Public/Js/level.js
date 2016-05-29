$(function(){
	//单击应用时候
	$('body').on('click','.level1',function(event){
		var _this=$(this);
		if(_this.prop('checked')== true){			
		    _this.prop('checked',true).parents('h4').next('div').find('.level').prop('checked',true);
	    
	    }else
            _this.prop('checked',false).parents('h4').next('div').find('.level').prop('checked',false);
		

	}).on('click','.level2',function(){
		
		 var _this=$(this);
		 if(_this.prop('checked')== true)
		 	_this.prop('checked',true).parents('dl').find('.level3').prop('checked',true);
		 else
		    _this.prop('checked',false).parents('dl').find('.level3').prop('checked',false);

	});
});