<?php
/*
  @der 服务管理控制器
*/
Class ServiceAction extends CommonAction{
     
    /*
       @der 服务列表
    */
    public function index(){
    	//查找出所有服务
    	$db=D('ServiceView');
  	    
       //导入分页类
       $page=X(M('Service'),'',10);
       //查找出该分类下面的所有服务
       $service=$db->limit($page['0']->limit)->select();
       $this->data=$service;
       $this->display();
    }


    /*
      @der 添加服务视图
    */
    public function add(){

    	//产品列表
    	$this->goods=show_list('Goods','gid','goods');
        //服务商列表
        $this->service=show_list('Source','sid','source');
        //分类下拉列表
        $this->cat=$cat=getCat_one('服务定制与开发');

        $this->scence=$cat=getCat_one('经常使用的云产品与服务',1,'Scencecat',0,'wid');
    	$this->display();
    
    }


    /*
     @der 添加服务操作
    */
    public function insert(){
        if(!$this->ispost()) redirect(__APP__);
       
        	$info=uploadimg('./Uploads/Service/',true);

        	if(is_array($info)){ 
                 
                 $_POST['logo']=$info['0']['savename'];
        	}else{
        		$this->error($info);
        	}

        
        if($id=M('Service')->where()->add($this->_post())){

        	$add=array(
                   'sid'=>$id,//服务表的ID
                   'price'=>$this->_post('price'), //价钱
                   'spec'=>$this->_post('specs') //规格
        		); 
        	 //将数据插入到服务规格表中
        	 M('Service_spec')->add($add);
           
             $this->success('添加成功',U('add'));
        }else{
        	 $this->error('添加失败，请重试');
        }

    }


    /*
      @der 服务修改视图
    */
    public function update(){
    	$id=$this->_get('id','intval');
        
    	//服务的信息
    	$data=M('Service')->where(array('id'=>$id))->find();

    	//产品列表
    	$this->goods=show_list('Goods','gid','goods',$data['gid']);
        //服务商列表
        $this->service=show_list('Source','sid','source',$data['sid']);
        //分类下拉列表
        $this->cat=$cat=getCat_one('服务定制与开发',1,'mirroringcat',$data['cid']);

        $this->scence=$cat=getCat_one('经常使用的云产品与服务',1,'Scencecat',$data['wid'],'wid');

    	$this->data=$data;
    	$this->display();
    }


    /*
       @der 服务的修改操作
    */
     public function save(){
     	if(!$this->ispost()) redirect(__APP__);
     	$where=array('id'=>$this->_post('id','intval'));
        
        $db=M('Service');

     	//判断是否上传了图片
     	if($_FILES['img']['error']==0){
     		 //查找出该服务的LOGO
     		$logo=$db->where($where)->getField('logo');
           
            //处理图片上传
     		if(is_array($info)){ 
                 
                $_POST['logo']=$info['0']['savename'];
        	}else{
        		$this->error($info);
        	}
     	}

     	if($db->where($where)->save($this->_post())){

     		if(isset($logo))  unlink("./Uploads/Service/{$logo}");
     		$this->success('修改成功',U('index'));

     	}else{
     		$this->error('修改失败');
     	}
     }

}