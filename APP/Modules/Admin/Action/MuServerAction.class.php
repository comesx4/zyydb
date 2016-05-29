<?php
/*
	@der 母服务器管理控制器
*/
Class MuServerAction extends CommonAction{

	/*
		@der 列表页
	*/
	public function index(){
		$this->data = D('Mu_serverView')->order('id DESC')->select();
		
		$this->display();
	}

	/*
		@der 添加页面
	*/
	public function add(){

		//区域下拉列表
		$this->region = show_list('Mu_region','region_id','region_name',0);
		$this->display();
	}

	/*
		@der 添加数据操作
	*/
	public function insert(){
		if ( !IS_POST ) redirect(__APP__);
		if ( post_isnull('region_id','server_ip','server_port','secret_key','server_can_sum','status', 'version') ) {
			$this->error('请填写完整!');
		}
		$data = array('region_id'		=> I('post.region_id',''),
					  'server_ip'		=> I('post.server_ip',''),
					  'server_port'		=> I('post.server_port',''),
					  'secret_key'		=> I('post.secret_key',''),
					  'server_can_sum'	=> I('post.server_can_sum',1),
					  'server_sum'	    => I('post.server_sum',0,'intval'),
					  'status'			=> I('post.status',1,'intval'),
					  'version'			=> I('post.version',''),
					  'cpu'				=> I('post.cpu'),
					  'memory'	        => I('post.memory'),
					  'remark'			=> I('remark',''),
					  'time'			=> time()
					  );
		
		if(M('Mu_server')->add($data)) {
			$this->success('添加成功！',U('index'));
		} else {
			$this->error('添加失败！');
		} //if
	}

	/*
		@der 修改页面
	*/
	public function update(){
		$where = array('id'=>I('get.id',0,'intval'));
		$data = D('Mu_serverView')->where($where)->order('id DESC')->find();

		//区域下拉列表
		$this->region = show_list('Mu_region','region_id','region_name',$data['region_id']);
		$this->data = $data;
		$this->display();
	}

	/*
		@der 修改数据操作
	*/
	public function save(){
		if ( !IS_POST ) redirect(__APP__);
		if ( post_isnull('region_id','server_ip','server_port','secret_key','server_can_sum','server_sum','status', 'version') ) {
			$this->error('请填写完整!');
		}
		$data = array('region_id'		=> I('post.region_id',''),
					  'server_ip'		=> I('post.server_ip',''),
					  'server_port'		=> I('post.server_port',''),
					  'secret_key'		=> I('post.secret_key',''),
					  'server_can_sum'	=> I('post.server_can_sum',1),
					  'server_sum'	    => I('post.server_sum',''),
					  'status'			=> I('post.status',1,'intval'),
					  'version'			=> I('post.version',''),
					  'cpu'				=> I('post.cpu'),
					  'memory'	        => I('post.memory'),
					  'remark'			=> I('remark',''),
					  'time'			=> time(),
					  'id'	   			=> I('post.id',0,'intval')
					  );
		
		if(M('Mu_server')->save($data)) {
			$this->success('修改成功！',U('index'));
		} else {
			$this->error('修改失败！');
		} //if
	}

	/*
		@der 删除数据操作
	*/
	public function delete(){
		$this->db_delete(M('Mu_server'));
	}
}