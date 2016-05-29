<?php
//服务商控制器
Class SourceAction extends CommonAction{
	//服务商系统列表
 	public function index(){
 		//排序
 		cat_sort('Source');

 		$this->source=M('Source')->order('sort DESC')->select();
 	
          $this->display();
 	}

 	//添加服务商视图
 	public function add(){
 		$this->display();
 	}

 	//添加服务商操作
 	public function insert(){
 		if(!$this->ispost()) $this->redirect('Index/index');

 		if(empty($_POST['source']))
			 $this->error('名称不能为空');

		if(M('Source')->data($this->_post())->add())
			$this->success('添加成功',U('index'));
		else
			$this->error('添加失败请重试');

 	}


 	//删除服务商操作
	public function del(){
		$where=array('id'=>$this->_get('id','intval'));

		if(M('Source')->where($where)->delete())
			$this->success('删除成功',U('index'));
		else
			$this->error('删除失败请重试');
	}

	//服务商修改视图
	public function update(){
		$this->display();
	}

	//服务商修改操作
	public function save(){
		if(!$this->ispost()) $this->redirect('Index/index');
		
	}

}