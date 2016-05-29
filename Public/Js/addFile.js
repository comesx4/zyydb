$(function(){
        /*
      增加上传按钮的事件
    */
    var i=1;
    $('.file').on('click','.addFile',function(){
        //限制只能添加2个
        if(i>2){
            return false;
        }

        $(this).after('<p><input type="file" name="img[]"><span class="delFile">删除</span></p>');
        i++;
    }).on('click','.delFile',function(){

        $(this).parent().remove();
        i--;
    });


      /*
        描述的失去焦点验证
      */
    $('textarea').blur(function(){
        var textarea=$('textarea').eq(0);
          
        if(!textarea.val().match(/^\S+$/)){
            if(textarea.next('span').length<1)
                    textarea.after("<span class='error'>请填写描述</span>");
            
            bool=false;

        }else{
            textarea.next('span').remove();
        }
    });


    /*
      验证值是否为空
    */
    $('.submit').click(function(){
        bool=true; 
       
        $('textarea').blur();
       
         
         $.each($('.require'),function(index,data){
            var name=$(this).text().replace('*','').replace('：','');
            var dd=$(this).next();
            var input=dd.find('input').eq(0);
            
              //判断是否是复选框或者单选框
              if(dd.find('input').attr('type')=='checkbox'){
                    
                    if(dd.find('input:checked').length<1){
                            bool=false;
                            alert('请选择一个:'+name);
                    }
              
              }else{

                  if(!input.val().match(/^\S+$/)){
                        
                        bool=false;
                        
                        if(dd.find('span').length<1){
                             dd.append("<span class='error'>请填写"+name+"</span>");
                             input.focus();
                        }
                  }

                  if($.data(input,'events')==null){
                          
                          //添加离开焦点的事件
                          input.blur(function(){
                               //如果填写了值，并且没有空
                               if($(this).val().match(/^\S+$/)){
                                     $(this).next('span').hide();
                               }else{
                                    $(this).next('span').show();
                               }
                          });
                  }
                  

              }


         });


           return bool;
         

    });
});