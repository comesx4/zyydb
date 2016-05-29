<?php
//镜像信息控制器
Class MirroringInfoAction extends CommonAction{
	//镜像信息列表
 	public function index(){
 		$this->mirr=M('MirroringInfo')->query("SELECT i.id AS `id`,m.name AS `name` FROM kz_mirroringinfo i LEFT JOIN kz_mirroring m ON i.mid=m.id");
       
        $this->display();
 	}

 	//添加镜像信息视图
 	public function add(){
 		//取出镜像下拉列表
		$this->mirr=show_list('Mirroring','mid','name');

 		$this->display();
 	}

 	//添加镜像信息操作
 	public function insert(){
 		if(!$this->ispost()) $this->redirect('Index/index');

 		if(empty($_POST['content']))
			 $this->error('信息不能为空');

		if(M('Mirroringinfo')->data($this->_post())->add())
			$this->success('添加成功',U('index'));
		else
			$this->error('添加失败请重试');

 	}


 	//删除镜像信息操作
	public function del(){
		$where=array('id'=>$this->_get('id','intval'));

		if(M('Mirroringinfo')->where($where)->delete())
			$this->success('删除成功',U('index'));
		else
			$this->error('删除失败请重试');
	}

	//镜像信息修改视图
	public function update(){
		$where=array('id'=>$this->_get('id','intval'));		

		//取出镜像下拉列表
		$this->jx=show_list('Mirroring','mid','name',$this->_get('id','intval'));

		$this->mirr=M('Mirroringinfo')->where($where)->find();


		$this->display();

	}

	//镜像信息修改操作
	public function save(){
		if(!$this->ispost()) $this->redirect('Index/index');

		if(M('Mirroringinfo')->save($_POST)){
           $this->success('修改成功',U('index'));
		}else{
        	$this->error('修改失败请重试');
		}

	}
}