<?php
/*
	@der 存储池管理
*/
Class CloudPoolAction extends CommonAction{
	/*
		@der 列表页
	*/
	public function index(){
		$data = M('Cloud_pool')->select();

		$this->data = $data;
		$this->display();
	}

	/*
		@der 添加页面
	*/
	public function add(){
		$this->display();
	}

	/*
		@der 添加数据操作
	*/
	public function insert(){
		if ( !IS_POST ) redirect(__APP__);
		if (post_isnull('pool_name','code','status')) {
			$this->error('请填写完整!');
		}

		$add = array(
			'pool_name' => I('post.pool_name',''),
			'code'	 	=> I('post.code',''),
			'status'	=> I('post.status',1,'intval'),
			'remark'	=> I('post.remark')
			);
		
		if (M('Cloud_pool')->add($add)) {
			$this->success('添加成功',U('index'));
		} else {
			$this->error('添加失败');
		}
	}

	/*
		@der 修改页面
	*/
	public function update(){
		
		$id   = I('get.id',0,'intval');
		
		$this->data = M('Cloud_pool')->where(array('id' => $id))->find();

		$this->display();
	}

	/*
		@der 修改数据操作
	*/
	public function save(){
		if ( !IS_POST ) redirect(__APP__);
		if (post_isnull('pool_name','code','status','id')) {
			$this->error('请填写完整!');
		}
		
		$add = array(
			'pool_name' => I('post.pool_name',''),
			'code'	 	=> I('post.code',''),
			'status'	=> I('post.status',1,'intval'),
			'remark'	=> I('post.remark'),
			'id'		=> I('post.id',0,'intval')
		);
		
		if ( M('Cloud_pool')->save($add) ) {
			$this->success('修改成功',U('index'));
		} else {
			$this->error('修改失败');
		}
	}

	/*
		@der 删除数据操作
	*/
	public function delete(){
		$this->db_delete(M('Cloud_pool'));
	}
}