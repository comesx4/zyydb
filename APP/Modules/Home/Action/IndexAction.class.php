<?php
   Class IndexAction extends Action{

   	public function index(){
     
     
      /*公告和动态*/
      $field = 'id,title,create_time';
      $this->notice  = M('Notice')->field($field)->order('sort DESC')->limit(3)->select();
      $this->dynamic = M('Dynamic')->field('id,title,create_time,cid')->order('sort DESC')->limit(5)->select();      
    
   		$this->display('newIndex');
   	}
  


    //小云的问答
        public function xiaoyun(){
          if(!$this->isAjax()) halt('页面不存在');
            $title=$_POST['title'];
            $titles=$this->_post('title');
            $where=array('title'=>array('LIKE',''.$title.'%'));
            $str='';
            $name=!empty(I('session.' . C('SAFE.NAMEHAND')))?I('session.' . C('SAFE.NAMEHAND')):'访客';
       
           
            //正则匹配JS代码
            if(preg_match('/\<script\>.*?\<\/script\>/is',$title)!=0){
               $str .="<dl class='my'>";
                  $str .="<dt>{$name}：<span>(".date('Y-m-d H:i:s',time()).")</span></dt>";
                  $str .="<dd>{$titles}</dd>";
                  $str .="</dl>";
                  $str .="<dl class='xiaoyun'>";
                  $str .="<dt>小云解答：<span>(".date('Y-m-d H:i:s',time()).")</span></dt>";
                  $str .="<dd>你太坏了，居然嵌入JS代码,还好小云有所防范！</dd>";
                  $str .="</dl>";
                   echo $str;
                  die;


            }
            
            //去数据库中查找问题合适的答案
            if($answer=M('Xiaoyun')->where($where)->getField('answer')){

                  $str .="<dl class='my'>";
                  $str .="<dt>{$name}：<span>(".date('Y-m-d H:i:s',time()).")</span></dt>";
                  $str .="<dd>{$title}</dd>";
                  $str .="</dl>";
                  $str .="<dl class='xiaoyun'>";
                  $str .="<dt>小云解答：<span>(".date('Y-m-d H:i:s',time()).")</span></dt>";
                  $str .="<dd>{$answer}</dd>";
                  $str .="</dl>";
                  
            }else{
              $str .="<dl class='my'>";
                  $str .="<dt>{$name}：<span>(".date('Y-m-d H:i:s',time()).")</span></dt>";
                  $str .="<dd>{$title}</dd>";
                  $str .="</dl>";
                  $str .="<dl class='xiaoyun'>";
                  $str .="<dt>小云解答：<span>(".date('Y-m-d H:i:s',time()).")</span></dt>";
                  $str .="<dd>您的问题太深奥了，小云暂时无法回答您哦！</dd>";
                  $str .="</dl>";

            }
            echo $str;

        }
 }

	  