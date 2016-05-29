<?php
/*
	@der 机房服务器管理
*/
Class RoomServerAction extends CommonAction{

	/*
		@der 列表
	*/
	public function index(){
		$data = D('Room_serverView')->select();

		$this->data = $data;
		$this->display();
	}

	/*
		@der 添加视图
	*/
	public function add(){
		//区域下拉列表
		$this->region = show_list('Mu_region','region_id','region_name',0);

		$this->display();
	}

	/*
		@der 插入操作
	*/
	public function insert(){
		IS_POST || redirect(__APP__);
		post_isnull('status','region_id','server_name','server_code','position') && $this->error('请填写完整');
		
		$add = array(
			'status' 	  => I('post.status',0,'intval'),
			'region_id'	  => I('post.region_id',5,'intval'),
			'server_name' => I('post.server_name'),
			'server_code' => I('post.server_code'),
			'cpu'		  => I('post.cpu',1,'intval'),
			'memory'	  => I('memory',1,'intval'),
			'disk'		  => I('post.disk',100,'intval'),
			'network'	  => I('post.network'),
			'other'		  => I('post.other'),
			'size'		  => I('post.size'),
			'express_code'=> I('post.express_code'),
			'owner'		  => I('post.owner'),
			'position'	  => I('post.position'),
			'remark'	  => I('post.remark')
			);

		if (M('Room_server')->add($add)) {
			$this->success('添加成功',U('index'));
		} else {
			$this->error('添加失败');
		}
	}

	/*
		@der 修改视图
	*/
	public function update(){
		$where = array('id' => I('get.id',0,'intval'));
		$data  = M('Room_server')->where($where)->find();

		//区域下拉列表
		$this->data   = $data;
		$this->region = show_list('Mu_region','region_id','region_name',0);
		$this->display();
	}

	/*
		@der 更新操作
	*/
	public function save(){
		IS_POST || redirect(__APP__);
		post_isnull('status','region_id','server_name','server_code','position') && $this->error('请填写完整');

		$save = array(
			'status' 	  => I('post.status',0,'intval'),
			'region_id'	  => I('post.region_id',5,'intval'),
			'server_name' => I('post.server_name'),
			'server_code' => I('post.server_code'),
			'cpu'		  => I('post.cpu',1,'intval'),
			'memory'	  => I('memory',1,'intval'),
			'disk'		  => I('post.disk',100,'intval'),
			'network'	  => I('post.network'),
			'other'		  => I('post.other'),
			'size'		  => I('post.size'),
			'express_code'=> I('post.express_code'),
			'owner'		  => I('post.owner'),
			'position'	  => I('post.position'),
			'remark'	  => I('post.remark'),
			'id'		  => I('post.id',0,'intval')
			);

		if (M('Room_server')->save($save)) {
			$this->success('编辑成功',U('index'));
		} else {
			$this->error('编辑失败');
		}
	}

	/*
		@der 删除操作
	*/
	public function delete(){
		$this->db_delete(M('Room_server'));
	}
}