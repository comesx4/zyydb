<?php
/**
	@der 快照管理
*/
Class SnapshotAction extends CommonAction{

	/*
		@der 列表页
	*/
	public function index(){
		$db   = M('Cloud_snap');
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
		@der 添加数据操作
	*/
	public function insert(){
		if ( !IS_POST ) redirect(__APP__);
		
		$disk_code	= I('post.disk_code','');
		$remark		= I('post.remark','');

		//添加快照
		import('Class.Server',APP_PATH);
		$send_data=array(
						  'disk_code'	=>$disk_code,
						  'remark'		=>$remark
						  );
		$result = json_decode(Server::createSnapshot($send_data) , true);
		
		if ($result['code'] == 0) {
			$this->success('创建成功!',U('index'));
		} else {
			$this->error($result['error']);
		}
	}

	/*
		@der 修改页面
	*/
	public function update(){
		
		$this->display();
	}

	/*
		@der 修改数据操作
	*/
	public function save(){
		if ( !IS_POST ) redirect(__APP__);
		
	}

	/**
	 * @der 克隆硬盘
	*/
	public function cloneDisk(){
		$where = array('id' => I('get.id',0,'intval') );
		$this->data = M('Cloud_snap')->field('snap_code,id')->where($where)->find();

		$this->display();
	}

	/**
		@der 克隆硬盘操作
	*/
	public function cloneDiskHandle(){
		IS_POST || redirect(__APP__);
		$where = array();
		$snap  = M('Cloud_snap')->field('snap_code')->where($where)->find();

		$data = array(
			'name' 	    => I('post.name',''),
			'snap_code' => $snap['snap_code'],
			'disk_flag'	=> 'private'
			);
		
		import('Class.Server',APP_PATH);
		$result = json_decode( Server::cloneDisk($data) , true);

		if ($result['code'] == 0) {
			$this->success('克隆成功!');
		} else {
			$this->error($result['error']);
		}
	}

	/**
		@der 保护快照
	*/
	public function protectionSnap(){
		$this->interfaceRequest('protectionSnap');
	}

	/**
	 * 	@der 取消快照保护	
	 */
	public function cancelSnapProtection(){
		$this->interfaceRequest('cancelSnapProtection');
	}

	/*
		@der 删除快照
	*/
	public function delete(){
		$this->interfaceRequest('deleteSnap');
	}

	/**
	 *	@der 处理接口请求
	 *	@param string $function 方法名
	 */
	private function interfaceRequest($function = 'cancelSnapProtection'){
		$snap_id = I('get.id',0,'intval');
		import('Class.Server',APP_PATH);
		$result  = json_decode( Server::$function($snap_id) , true);

		if ($result['code'] == 0) {
			$this->success('操作成功',U('index'));
		} else {
			$this->error($result['error']);
		}
	}

	/*
		@der 处理搜索
	*/
	private function _search($db){
		$tmp    = $_REQUEST;
		$where= '';
		$pwhere = array();
		//如果有搜索
		if (isset($tmp['search'])) {
			$pwhere['submit'] = 1;
			// 状态
			if (!empty($tmp['status'])) {
				$tmp['status'] != -1 ? $status = $tmp['status'] : $status = 0;
				$where['status']  = $status;				
				$pwhere['status'] = $tmp['status'];
			}

			// 快照类型
			if (!empty($tmp['snap_type'])) {
				$where['snap_type']  = $tmp['snap_type'];			
				$pwhere['snap_type'] = $tmp['snap_type'];
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

			//快照标识
			if (!empty($tmp['snap_code'])) {
				$where['snap_code']  = array('LIKE',"{$tmp['snap_code']}%");
				$pwhere['snap_code'] = $tmp['snap_code'];
			}
		}
		
		//分页
		$page = X($db,$where,C('page_num'),$pwhere);
		$data = $db->where($where)->limit($page['0']->limit)->order('id DESC')->select();

		return array($data,$tmp,$page);
	}
}