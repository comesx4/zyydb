<?php
/*
	@der IP网关管理控制器
*/
Class IpGatewayAction extends CommonAction{
	/*
		@der IP网关列表
	*/
	public function index(){
		
		$data = D('Ip_gatewayView')->order('id DESC')->select();

		$this->fpage = $page['1'];
		$this->data  = $data;
		
		$this->display();
	}

	/*
		@der 添加网关视图
	*/
	public function add(){
		//区域下拉列表
		$this->region = show_list('Mu_region','region_id','region_name',0);
		//IP线路下拉列表
		$this->ipLine = show_list('Ip_line','line_id','line_name',0);
		$this->display();
	}

	/*
		@der 添加网关操作
	*/
	public function insert(){
		if (!IS_POST) redirect(__APP__);
		if (post_isnull('region_id','line_id','lan_netmask','lan_gateway','wan_netmask','wan_gateway','status')) {
			$this->error('请填写完整');
		}//if
		
		$data = array('region_id'	=>I('post.region_id',1,'intval'),
					  'line_id'	 	=>I('post.line_id',1,'intval'),
					  'lan_netmask'	=>I('post.lan_netmask',''),
					  'lan_gateway'	=>I('post.lan_gateway',''),
					  'wan_netmask'	=>I('post.wan_netmask',''),
					  'wan_gateway'	=>I('post.wan_gateway',''),
					  'status'		=>I('post.status',1),
					  'remark'		=>I('post.remark',''),
					  'time'		=>time()
					  );
		
		if(M('Ip_gateway')->add($data)) {
			$this->success('添加成功！',U('index'));
		} else {
			$this->error('添加失败！');
		} //if
	}

	/*
		@der 修改网关视图
	*/
	public function update(){
		$id    = I('get.id',0,'intval');
		$field = 'id,region_id,line_id,lan_netmask,lan_gateway,wan_netmask,wan_gateway,status,remark,time';
		$data  = M('Ip_gateway')->field($field)->where(array('id' => $id))->find();;
		
		//区域下拉列表
		$this->region = show_list('Mu_region','region_id','region_name',$data['region_id']);
		//IP线路下拉列表
		$this->ipLine = show_list('Ip_line','line_id','line_name',$data['line_id']);
		$this->data = $data;
		$this->display();
	}

	/*
		@der 修改网关操作
	*/
	public function save(){
		if (!IS_POST) redirect(__APP__);
		if (post_isnull('region_id','line_id','lan_netmask','lan_gateway','wan_netmask','wan_gateway','status')) {
			$this->error('请填写完整');
		}//if

		$data = array('region_id'	=>I('post.region_id',1,'intval'),
					  'line_id'	 	=>I('post.line_id',1,'intval'),
					  'lan_netmask'	=>I('post.lan_netmask',''),
					  'lan_gateway'	=>I('post.lan_gateway',''),
					  'wan_netmask'	=>I('post.wan_netmask',''),
					  'wan_gateway'	=>I('post.wan_gateway',''),
					  'status'		=>I('post.status',1),
					  'remark'		=>I('post.remark',''),
					  'time'		=>time(),
					  'id'			=>I('post.id',0,'intval')
					  );
		
		if(M('Ip_gateway')->save($data)) {
			$this->success('修改成功！',U('index'));
		} else {
			$this->error('修改失败！');
		} 
	}

	/*
		@der 网关删除操作
	*/
	public function delete(){
		$this->db_delete(M('Ip_gateway'));
	}
}