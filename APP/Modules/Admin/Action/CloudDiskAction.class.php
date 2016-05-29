<?php
/**
	@der 云磁盘管理控制器
*/
Class CloudDiskAction extends CommonAction{

	/*
		@der 列表页
	*/
	public function index(){
		$db   = M('Cloud_disk');
		list ($data,$tmp,$page) = $this->_search($db);
		
		$this->data  = $data;
		$this->fpage = $page['1'];
		$this->tmp   = $tmp;
		$this->display();
	}

	/*
		@der 添加页面
	*/
	public function add(){
		
		$this->display();
	}

	/*
		@der 添加云磁盘操作
	*/
	public function insert(){
		if ( !IS_POST ) redirect(__APP__);
		//查找出云服务器ID
		$cloud_server_id = M('Yunji_server')->where(array('name' => I('post.name')))->getField('id');
		$data = array(
			'cloud_server_id'   => $cloud_server_id,
			'size'   			=> I('post.size',5,'intval'),
			'remark' 		    => I('post.remark',''),
			'uid'				=> I('post.uid','')
			);

		import('Class.Server',APP_PATH);
		$result = json_decode(Server::createCloudDisk($data),true);
		if ($result['code'] == 0) {
			$this->success('创建成功',U('index'));
		} else {
			$this->error($result['error']);
		}
	}

	/**
		@der 卸载云磁盘
	*/
	public function unCloudDisk(){
		import('Class.Server',APP_PATH);
		$result = json_decode( Server::unCloudDisk(I('get.id',0,'intval')) ,true);
		
		if ($result['code'] == 0) {
			$this->success('卸载成功',U('index'));
		} else {
			$this->error($result['error']);
		}
	}

	/**
		@der 挂载页面
	*/
	public function attach(){
		$this->display();
	}

	/**
		@der 挂载云磁盘
	*/
	public function mountCloudDisk(){
		IS_POST || redirect(__APP__);
		$data = array(
				'server_name' => I('post.name',''),
				'disk_id'	  => I('post.id',0,'intval'),
				'uid'		  => I('post.uid','')
			);
		
		import('Class.Server',APP_PATH);
		$result = json_decode(Server::mountCloudDisk($data),true);

		if ($result['code'] == 0) {
			$this->success('挂载成功',U('index'));
		} else {
			$this->error($result['error']);
		}
	}
	/*
		@der 删除云磁盘操作
	*/
	public function delete(){
		import('Class.Server',APP_PATH);
		$result = json_decode(Server::deleteCloudDisk(I('get.id',0,'intval')),true);

		if ($result['code'] == 0) {
			$this->success('删除成功',U('index'));
		} else {
			$this->error($result['error']);
		}
	}

	/*
		@der 处理搜索
	*/
	private function _search($db){
		$tmp    = $_REQUEST; 
		
		if (!isset($tmp['status'])) {
			$tmp['status'] = -1;
		}

		$where  = array();
		$pwhere = array();
		
		//如果有搜索
		if (isset($tmp['search'])) {
			$pwhere['search'] = 1;
			
			// 挂载状态
			if ($tmp['status'] != -1) {
				$where['status']  = $tmp['status'];				
				$pwhere['status'] = $tmp['status'];
			}

			// 状态
			if ($tmp['type'] != -1) {
				$where['type']  = $tmp['type'];				
				$pwhere['type'] = $tmp['type'];
			}

			// 磁盘类型
			if ($tmp['target'] != -1) {
				if ( $tmp['target'] == 1 ) {
					$where['target']  = 'vda';
				} else {
					$where['target']  = array('NEQ','vda');
				}
				
				$pwhere['target'] = $tmp['target'];
			}

			//云服务器ID
			if (!empty($tmp['cloud_server_id'])) {
				$where['cloud_server_id']  = $tmp['cloud_server_id'];
				$pwhere['cloud_server_id'] = $tmp['cloud_server_id'];
			}

			//硬盘标识
			if (!empty($tmp['disk_code'])) {
				$where['disk_code']  = array('LIKE',"{$tmp['disk_code']}%");
				$pwhere['disk_code'] = $tmp['disk_code'];
			}
			
			//开始时间
			if (!empty($tmp['min_start_time'])) {
				$date 				= strtotime($tmp['min_start_time']);	 
				$where['time']  	= array('EGT',$date);
				$pwhere['min_start_time'] = $tmp['min_start_time'];
			}

			//结束时间
			if (!empty($tmp['max_start_time'])) {
				$date 				= strtotime($tmp['max_start_time']);	 
				$where['time']  	= array('ELT',$date);
				$pwhere['max_start_time'] = $tmp['max_start_time'];
			}

		}

		//分页
		$page = X($db,$where,C('PAGE_NUM'),$pwhere);
		
		$field = array('id,cloud_server_id,disk_code,target,flag,status,type,iops,io,size,remark,time');
		$data  = $db->where($where)->field($field)->limit($page['0']->limit)->select();

		return array($data,$tmp,$page);
	}
}
