<?php
//产品性能控制器
Class PerformanceAction extends CommonAction{
	
	//性能列表
	public function index() {
        //如果单击了排序
        $gid = I('get.gid', 1);
        $cid = I('get.cid', 2);
        $this->goods = goodsList('gid', $gid);
        $this->goodslista = show_lista('goods', 'gid', 'goods', 'btn',array('gid'=>$gid));
        cat_sort('Performance'); //排序处理        
        $where['cid'] = $gid;
        $name=I('get.name');
        if (!empty($name))
            $where['name'] = array('like', "{$name}%");
        $data = D('PerformanceView')->where($where)->order('sort ASC')->select();
        $this->data = $data;
        $this->display();
    }

    //添加性能视图
	public function add(){
		//产品分类
		$this->cat = show_list('goods','cid','goods');
		$this->display();

	}

	//添加性能操作
	public function insert(){
		if(!$this->ispost()) $this->redirect('Index/index');
		$db=M('Goods');

		
		//判断用户有没有勾选添加字段
		if(isset($_POST['is'])){
			$type=$this->_post('type');
			$cid=$this->_post('cid','intval');

			//根据分类ID去查找出表名字
            $table=$db->where(array('id'=>$cid))->getField('tablename');
            
            //给表添加字段
			$db->query("alter table {$table} add {$this->_post('title')} {$type} not null;");
		}	

		if(empty($_POST['name'])||empty($_POST['remark']))
			 $this->error('名称或描述不能为空');

		if(M('Performance')->data($this->_post())->add())
			$this->success('添加成功',U('index'));
		else
			$this->error('添加失败请重试');

	}

	// //删除性能操作
	// public function del(){
	// 	$where=array('id'=>$this->_get('id','intval'));

	// 	if(M('Performance')->where($where)->delete())
	// 		$this->success('删除成功',U('index'));
	// 	else
	// 		$this->error('删除失败请重试');
	// }

	//性能修改视图
	public function update(){
		$where=array('id'=>$this->_get('id','intval'));

		$data=M('Performance')->where($where)->find();

		$this->cat = show_list('goods','cid','goods',$data['cid']);

		$this->per=$data;

		$this->display();
	}

	//性能修改操作
	public function save(){
		if(!$this->ispost()) $this->redirect('Index/index');

		//判断用户有没有勾选添加字段
		if(isset($_POST['is'])){
			$db=M('Goodscat');
			$type=$this->_post('type');
			$cid=$this->_post('cid','intval');

			//根据分类ID去查找出表名字
            $table=$db->where(array('id'=>$cid))->getField('tablename');

            //查找出改字段之前的名称
            $title=M('Performance')->where(array('id'=>$this->_post('id','intval')))->getField('title');

            //修改该字段
            $db->query("alter table {$table} change {$title} {$this->_post('title')} {$type} not null;");
           // echo $db->getLastSql();die;
            
   //          //给表添加字段,如果不成功就存在该字段，
			// if(!$db->query("alter table {$table} add {$this->_post('title')} {$type} not null;")){
                
   //              //查找出改字段之前的名称
   //              $title=M('Performance')->where(array('id'=>$this->_post('id','intval')))->getField('title');

   //              //修改该字段
   //              $db->query("alter table {$table} change {$title} {$this->_post('title')} {$type} not null;");
			// }
		}			
        
		if(M('performance')->save($this->_post()))
			$this->success('修改成功',U('index'));
		else
			$this->error('修改失败请重试');
	}

	/**
		@der 删除性能
	*/
	public function del(){
		$this->db_delete(M('performance'));
	}


}