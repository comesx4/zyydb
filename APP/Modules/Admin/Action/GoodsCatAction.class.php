<?php
/*
   产品分类控制器
*/
Class GoodsCatAction extends CommonAction{
	
	//产品分类列表
	public function index(){
		
        //调用排序函数
		cat_sort('Goodscat');

      	$this->cat=M('Goodscat')->order('sort asc')->select();	
		$this->display();
	}

	//添加产品分类视图
	public function add(){
		$this->display();

	}

	//分类插入到数据库
	public function insert(){
		if(!$this->ispost()) $this->redirect('Index/index');

		if(post_isnull('cat')) $this->error('有值不能为空');

		if($id=M('Goodscat')->data($_POST)->add()){
            //创建数据库表
            // M('Goodscat')->query("create table {$this->_post('tablename')}(id int not null auto_increment,city varchar(30) not null default '',primary key(id)) ENGINE = myisam;");

            $this->_addPer($id);

			$this->success('添加成功',U('index'));
		}else{
			$this->error('添加失败，请重试');
		}

	}

	//删除分类操作
	public function del(){
		$id=$this->_get('id','intval');
		//先判断该分类下面有没有产品，如果有产品就不让删除
		if(M('Goods')->where(array('cid'=>$id))->getField('id')>0){
			$this->error('该分类下面还有产品，不能删除');
		}
		//查找出该分类的表名称
		$table=M('Goodscat')->where(array('id'=>$id))->getField('tablename');

		$where=array('id'=>$this->_get('id','intval'));
		if (M('Goodscat')->where($where)->delete()) {
            //删除对应的参数、表
            M()->query("DROP TABLE {$table}");
            M('Goodsrenew_performace')->where(array('cid'=>$id))->delete();

			$this->success('删除成功',U('index'));
		} else {
			$this->error('删除失败，请重试');
		}


	}

	//分类修改视图
	public function update(){
		$id=$this->_get('id','intval');
		$where=array('id'=>$id);
		//分类信息
        $this->cat=M('Goodscat')->where($where)->find(); 
        //分类拥有的续费参数
        $this->data=peatarr( M('Goodsrenew_performace')->field('name')->where(array('cid'=>$id))->select(),'name' );
        
		$this->display();

	}

	//分类修改操作
	public function save(){
		if(!$this->ispost()) $this->redirect('Index/index');
        
        if(post_isnull('name','cat')) $this->error('有值不能为空');
		$db=M('Goodscat');
        $id=$this->_post('id','intval');
	    //查找出该分类的表名
		$table=$db->where(array('id'=>$id))->getField('tablename');

		$db->save($this->_post());
		
		//判断该分类之前有没有创建数据表
		if(!empty($table)){
			//判断用户有没有修改表名称
			if($table!=$this->_post('tablename')){					
				//修改表名称
               $db->query("alter table {$table} rename {$this->_post('tablename')}");  
            }
		
		//反之如果tablename字段不为空的话
	    }elseif(!empty($this->_post('tablename'))){
	    	//创建数据表
	    	$db->query("create table {$this->_post('tablename')}(id int not null auto_increment,primary key(id));");
	    }
        
        //分类续费参数的修改
        M('Goodsrenew_performace')->where(array('cid'=>$id))->delete();
        $this->_addPer($id);

		$this->success('修改成功',U('index'));
	
		
	}

	private function _addPer($id){
		
		//添加续费参数
        $add=array();
        foreach($this->_post('name') as $v){
			$add[]=array('cid'=>$id,'name'=>$v);    
		}
		M('Goodsrenew_performace')->addAll($add);
	}
}