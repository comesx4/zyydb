$(function(){
   $('.table').on('change','select[name=cid]',function(){

         var val=$(this).val();
         

         if(val==0) return false;
         
         _this=$(this);

         $.post(get_info,{'pid':val},function(data){

         	//先请空自己后面的下拉列表
         	_this.nextAll('select').remove();

         	  $(data).appendTo('.addCat');

         });
   });
   

   
   
});