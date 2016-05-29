<?php
 //操作系统控制器
 Class OsAction extends CommonAction{

 	//操作系统列表
 	public function index(){
 		$this->os=M('Os')->select();
          $this->display();
 	}

 	//添加操作系统视图
 	public function add(){
 		$this->display();
 	}

 	//添加操作系统操作
 	public function insert(){
 		if(!$this->ispost()) $this->redirect('Index/index');

 		if(empty($_POST['name']))
			 $this->error('名称不能为空');

		if(M('Os')->data($this->_post())->add())
			$this->success('添加成功',U('index'));
		else
			$this->error('添加失败请重试');

 	}


 	//删除操作系统操作
	public function del(){
		$where=array('id'=>$this->_get('id','intval'));

		if(M('Os')->where($where)->delete())
			$this->success('删除成功',U('index'));
		else
			$this->error('删除失败请重试');
	}

	//系统修改视图
	public function update(){
        $where=array('id'=>$this->_get('id','intval'));
		//查找出该系统的信息
		$this->os=M('Os')->where($where)->find();

		$this->display();
 
	}

	//系统修改操作
	public function save(){
		if(!$this->ispost()) $this->redirect('Index/index');

		if(M('Os')->save($this->_post()))
			$this->success('修改成功',U('index'));
		else
			$this->error('修改失败请重试');

	}
 }