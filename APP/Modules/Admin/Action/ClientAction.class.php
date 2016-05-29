<?php
Class ClientAction extends CommonAction{

	 //客户列表页
	 public function index(){
	 	$this->client=D('ClientView')->select();
	 	$this->display();
	 }


      //客户分类列表
      public function cat(){
        //如果单击了排序
        if(isset($_POST['submit'])){
        	unset($_POST['submit']);
        	$db=M('Clientcat');

        	//循环修改排序
        	foreach($_POST as $key=>$v){
        		$where=array('id'=>$key);
        		$db->where($where)->setField(array('sort'=>$v));


        	}


        }

      	$this->cat=M('Clientcat')->order('sort asc')->select();
      	$this->display();

      }

      //添加客户分类
      public function addCat(){

      	$this->display();

      }

      //插入分类到数据库
      public function insertCat(){
      	if(!$this->ispost()) $this->redirect('Index/index');
      	//如果为空就不插入
      	if(empty($_POST['cat']))
      		$this->error('分类名称不能为空');
     
      	if(M('Clientcat')->add($this->_post()))
      		$this->success('分类添加成功',U('addCat'));
      	else
      		$this->error('添加失败，请重试！');

      }

      //添加客户页面
      public function add(){
      	$this->cat=Clientcat('cid');
      	$this->display();

      }

      //修改客户界面 
      public function update(){
        $id=$this->_get('id','intval');
        $stmt=M('Client')->where(array('id'=>$id))->find();
        $this->stmt=$stmt;
        $this->cat=Clientcat('cid',$stmt['cid']);
       
        $this->display();
      }

      //修改客户操作
      public function updateClient(){
        if(!$this->ispost()) $this->redirect('Index/index');
        $db=M('Client');
        $where=array('id'=>$this->_post('id','intval'));

        //判断用户是否上传了文件
        if($_FILES['img']['error']==0){
            //取出用户之前的图片路径
            $path='./Public/Images/';
            $path .=$db->where($where)->getField('img');

            //调用文件上传函数
            $info=uploadimg('./Public/Images/');
            
            //如果返回的值不是数组就说明上传出错了
            if(!is_array($info)){
                $this->error($info);
            }
            //删除原来的图片
            unlink($path);
        }
        //判断有没有上传文件
        if(isset($info))
          $_POST['img']=$info['0']['savename'];

        //将数据修改到数据库中
        if($db->where($where)->save($_POST))
            $this->success('修改成功',U('index'));
        else
            $this->error('修改失败');


      }

      //插入客户到数据库
      public function insert(){
      	if(!$this->ispost()) $this->redirect('Index/index');
      	
      	  $info=uploadimg('./Public/Images/');

      	  //判断上传有没有出错
      	  if(!is_array($info))
      	  	$this->error($info);
            
            $_POST['img']=$info['0']['savename'];
          
            if(M('Client')->add($_POST)){
            	$this->success('添加成功',U('add'));
            }else{
            	$this->error('添加失败，请重试');
            }

      }

      //删除分类或客户
      public function delCat(){
        if(!$this->ispost()) $this->redirect('Index/index');

        
        //如果存在cid就说明是删除分类
        if(!empty($_POST['cid'])){
            $where=array('id'=>$this->_post('cid'));

             if(M('Clientcat')->where($where)->delete())
                echo 1;
              else
                echo 0;

         //反之就是删除客户
        }else{
            $where=array('id'=>$this->_post('pid'));
            $db=M('Client');

            //先查找出客户的图标
            $img=$db->where($where)->getField('img');
            //如果删除成功
            if($db->where($where)->delete()){
                 //删除客户之前的图标
                  unlink('./Public/Images/'.$img);
                  echo 1;

            }else{
                  echo 0;
            }

        }


      }


}