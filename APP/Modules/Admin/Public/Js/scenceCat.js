$(function(){
	$('.ad').click(function(){
      		
  		$('.nname').after("	<tr><td>分类名称</td><td><input type='text' name='cat[]' /></td></tr>");
    });

    $('select[name=pid]').change(function(){
    	var index=this.selectedIndex;
        var istwo=$(this).find('option').eq(index).attr('istwo'); //当前所单击的option标签的istwo属性
        
        //判断是否为二级分类
        if(istwo==1){
             $('.two').show();
        }else{
             $('.two').hide();
        }
        
        
    });
});