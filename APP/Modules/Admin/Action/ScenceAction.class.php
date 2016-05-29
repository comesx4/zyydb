<?php
/*
  工单控制器
*/
Class ScenceAction extends CommonAction{
	/*
      工单列表
	*/
    public function index(){     
      
    	$this->data=D('ScenceView')->select();    	
    	
    	$this->display('Help:index');

    }

    /*
     添加工单视图
    */
    public function add(){
      $this->content=ueditor1();     
      
      //提取出分类下拉列表
      $cat=get_catOne(0,false,'Scencecat');
      // $pid[]=4;
      // $i=4;
      // $db=M('Scencecat');
      
      // while($pid[]=$db->where(array('pid'=>$i))->getField('id')){

      //     $i=$pid[count($pid)-1];

      // }
      

      // array_pop($pid);
     
      // foreach($pid as $v){
      //     $info=get_catOne($v,false,'Scencecat');
       

      //     if(!empty($info))
      //          $cat .=$info;
      // }
    	
      $this->cat=$cat;
    	$this->display('Help:add');

    }

    /*	
     添加工单操作
    */
    public function insert(){
    	if(!$this->ispost()) redirect(__APP__);
     

      if($_POST['cid']==0)  $this->error('请选择一个分类');
      
    	$data=array(
             'cid'=>$this->_post('cid','intval'),
             'title'=>$this->_post('title'),             
             'time'=>time()
    	);

    
      $db=M('Scence');
      
      //先将标题等添加到数据库中
    	if($id=$db->add($data)){
        
          $this->reimg($db,$id);

      		 $this->success('添加成功',U('add'));

    	}else{
           $db->getLastSql();die;
    		   $this->error('添加失败，请重试');

    	}


    }


    /*
      修改视图
    */
     public function update(){
        
      $id=$this->_get('id','intval');        

     	$data=M('Scence')->where(array('id'=>$id))->find();
      $this->data=$data;
      
      //查找出该分类的所有顶级分类
      $parent=get_parent($data['cid'],'Scencecat');
      $parent=array_reverse($parent);
      $cat='';
      foreach($parent as  $v){
          $cat .=get_catOne($v['pid'],$v['id'],'Scencecat');
      }

      $this->cat=$cat;
       

     	$this->content=ueditor1();

     	$this->display('Help:update');

     }


     /*
      修改操作
     */
      public function save(){
      	
      	if(!$this->ispost()) redirect(__APP__);
      	$where=array('id'=>$this->_post('id','intval'));
        
        $db=M('Scence');

      if($db->where($where)->setField($this->_post())){
        
         $this->reimg($db,$this->_post('id','intval'));

    		 $this->success('修改成功',U('index'));

    	}else{
    		 $this->error('修改失败，请重试');

    	}

      }


      /*
        处理上传操作
      */
      private function reimg($db,$id){
      		
      		//将POST中的图片路径修改
          $anwer=str_replace('Tmp/',"Scence/{$id}/",$this->_post('anwer'));

         if(empty($_SESSION['img'])){
         	$db->where(array('id'=>$id))->setField(array('anwer'=>$anwer));
            return false;
        }
          
          //临时路径
          $dbpath='./Uploads/Tmp/';

          //更改为的路径
          $path="./Uploads/Scence/{$id}/";

          //如果目录不存在就创建
          if(!file_exists($path))  mkdir($path);               
        

          //将内容修改到数据库中
          if($db->where(array('id'=>$id))->setField(array('anwer'=>$anwer))){

              //将图片剪切到目标目录下面
             foreach($_SESSION['img'] as $v){
             
                    rename($dbpath.$v,$path.$v);
             }

             //清空SESSION
             $_SESSION['img']=array();


          }else{
            
              $this->error('答案添加失败');
          }
      }


      /*
       删除操作
      */
      public function del(){
        $id=$this->_get('id','intval');  

        if(M('Scence')->where(array('id'=>$id))->delete()){

          //删除目录及该工单的图片
          undir($id,"./Uploads/Scence/");

    		   $this->success('删除成功',U('index'));

	      }else{
	    		 $this->error('删除失败，请重试');

	      }



      }


    /*
     异步获取分类信息的方法
    */
    public function get_info(){
      if(!$this->isAjax()) redirect(__APP__);
      
      $info=get_catOne($this->_post('pid','intval'),false,'Scencecat');

      if(!empty($info))
          echo $info;
    } 



}