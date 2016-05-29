$(function(){
	$('select[name=gid]').change(function(){
		
		var val=$(this).val();
		//异步获取性能信息
		$.post(getPer,{'gid':val},function(data){

			$('.demo').remove();
			$('.city').after(data);

			if(!data)
				 alert('该产品还未添加性能！');

		});
	});


    $('select[name=gid]').change();

});