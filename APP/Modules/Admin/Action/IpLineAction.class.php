<?php
/*
	@der IP线程管理控制器
*/
Class IpLineAction extends CommonAction{

	/*
		@der IP线程列表
	*/
	public function index(){
		$db    = M('Ip_line');
		$page  = X($db);
		$field = array('id,region_id,line_name,line_code,line_type,status,remark,time');
		$data  = $db->field($field)->limit($page['0']->limit)->select();
		
		$this->fpage = $page['1'];
		$this->data  = $data;
		
		$this->display();
	}

	/*
		@der 添加线程视图
	*/
	public function add(){
		//区域下拉列表
		$this->region = show_list('Mu_region','region_id','region_name',0);
		//城市下拉列表
		$this->city = show_list('City','city_id','city',0);
		$this->display();
	}

	/*
		@der 添加线程操作
	*/
	public function insert(){
		if (!IS_POST) redirect(__APP__);
		if (post_isnull('line_type','line_name','line_code','status')) {
			$this->error('请填写完整');
		}
		$data = array('line_type'	=>I('post.line_type',1),
					  'line_name'	=>I('post.line_name',''),
					  'line_code'	=>I('post.line_code',''),
					  'status'		=>I('post.status',''),
					  'remark'		=>I('post.remark',''),
					  'region_id' 	=>I('post.region_id',0,'intval'),
					  'time'		=>time()
					  );
		
		if(M('Ip_line')->add($data)) {
			$this->success('添加成功！',U('index'));
		} else {
			$this->error('添加失败！');
		} 
	}

	/*
		@der 修改线程视图
	*/
	public function update(){
		$id    = I('get.id',0,'intval');
		$field = 'id,line_type,line_name,line_code,status,remark,region_id';
		$data  = M('Ip_line')->field($field)->where(array('id' => $id))->find();
		$this->data = $data;
		//区域下拉列表
		$this->region = show_list('Mu_region','region_id','region_name',$data['region_id']);
		$this->display();
	}

	/*
		@der 修改线程操作
	*/
	public function save(){
		if (!IS_POST) redirect(__APP__);
		if (post_isnull('id','line_type','line_name','line_code','status')) {
			$this->error('请填写完整');
		}
		$data = array('line_type'	=>I('post.line_type',1),
					  'line_name'	=>I('post.line_name',''),
					  'line_code'	=>I('post.line_code',''),
					  'status'		=>I('post.status',''),
					  'remark'		=>I('post.remark',''),	
					  'region_id' 	=>I('post.region_id',0,'intval'),			
					  'id'			=>I('post.id',0,'intval')
					  );
		
		if(M('Ip_line')->save($data)) {
			$this->success('修改成功！',U('index'));
		} else {
			$this->error('修改失败！');
		} 

	}

	/*
		@der 线程删除操作
	*/
	public function delete(){
		$this->db_delete(M('Ip_line'));
	}
}