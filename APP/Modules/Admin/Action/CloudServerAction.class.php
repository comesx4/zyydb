<?php
/*
	@der 中电云集云服务器管理
*/
Class CloudServerAction extends CommonAction{

	/*
		@der 列表页
	*/
	public function index(){
		$db = D('Yunji_serverView');
		
		list ($data,$tmp,$page) = $this->_search($db);
		
		$this->data  = $data;
		$this->fpage = $page['1'];
		$this->tmp   = $tmp;
		$this->display();
	}

	/*
		@der 服务器回收站
	*/
	public function recycle(){
		$db = D('Yunji_serverView');
		$where = array('isdelete' => 1);
		list ($data,$tmp,$page) = $this->_search($db,$where);
		
		$this->data  = $data;
		$this->fpage = $page['1'];
		$this->tmp   = $tmp;
		$this->display();
	}

	/*
		@der 添加页面
	*/
	public function add(){
		//母服务器下拉列表
		$where = array('status' => 1);
		$this->mu_server = show_list('Mu_server','mu_server_id','server_ip',0,true,$where);
		//IP线路下拉列表
		$this->ipLine = show_list('Ip_line','line_id','line_name',0);
		//查找出操作系统
        $os = M('Mirroring')->field('id,oid')->where(array('type'=>0))->select();

        foreach($os as $key=>$v){
        	$os[$key]['name']=M('Os')->where(array('id'=>$v['oid']))->getField('name');	
        }
        
        $this->os=$os; 
		$this->display();
	}

	/*
		@der 创建服务器
	*/
	public function insert(){
		if ( !IS_POST ) redirect(__APP__);
		
		$data = array(
				'mu_server_id' 		     => I('post.mu_server_id',0,'intval'),
				'line_id'	   		     => I('post.line_id',0,'intval'),
				'image_id'	   		     => I('post.image_id',0,'intval'),
				'disk'					 => explode(',', I('post.disk')),
				'cpu'					 => I('post.cpu',''),
				'memory'				 => I('post.memory',''),
				'uid'					 => I('post.uid',0,'intval'),
				'gid'				     => 1,
				'cid'			  	 	 => 1,
				'end'					 => I('post.month',1,'intval'),
				'ipos'					 => I('post.ipos',''),
				'io'					 => I('post.io',''),
				'lan_upload_bandwidth'   => I('post.lan_upload_bandwidth',''),
				'lan_download_bandwidth' => I('post.lan_download_bandwidth',''),
				'wan_upload_bandwidth'   => I('post.wan_upload_bandwidth',''),
				'wan_download_bandwidth' => I('post.wan_download_bandwidth',''),
			);

		import('Class.Server',APP_PATH);
		$result = json_decode(Server::createServer($data) , true);
		
		if ($result['code'] == 0) {
			$this->success('添加成功');
		} else {

			$this->error($result['error']);
		}
	}

	/*
		@der 管理页面
	*/
	public function update(){
		$where = array('yunji_server.id' => I('get.id',0,'intval'));
		$data = D('Yunji_serverView')->where($where)->find();
		
		if ( in_array($data['status'] , array(3,14,8,9,10,7,11) ) ) {
			$this->getStatus($data);
		}

		$this->data = $data;
		$this->display();
	}

	/*
		@der 修改数据操作
	*/
	public function save(){
		if ( !IS_POST ) redirect(__APP__);
		
		$save = array(
				'id' 	 => I('post.living_id',0,'intval'),
				'status' => I('post.status','','intval'),
				'remark' => I('post.remark',''),
			);

		if (M('Living')->save($save)) {
			$this->success('修改成功',U('index'));
		} else {			
			$this->error('修改失败');
		}
	}

	/**
		@der 重置密码页面 
	*/
	public function resetPassword(){

		$this->display();
	}

	/**
		@der 重置密码操作
	*/
	public function resetPasswordHandle(){
		if ( !IS_POST ) redirect(__APP__);

		$type = I('get.type',3,'intval');
		$data = array(
				'name'     => I('post.name',''),
				'password' => I('post.password',''),
			);

		import('Class.Server',APP_PATH);
		/*发送请求*/
		$result = json_decode(Server::changePassword($data),true);
		
		if ($result['code'] == 0) {
			$this->success('操作成功',$_SERVER['HTTP_REFERER']);
		} else {
			$this->error($result['error']);
		}
	}

	/**
		@der 更改带宽、CPU、内存
			get type 1 带宽
					 2 CPU
					 3 内存					
	*/
	public function updateAttribute(){
		$type = I('get.type',0,'intval');
		$select = '';
		switch ($type) {
			case '1':
				$name = '带宽';
				break;
			case '2':
				$name = 'CPU';
				break;
			case '3':
				$name = '内存';
				break;
			default:
				redirect(__APP__);
				break;
		}

		$this->name = $name;
		$this->select = $select;
		$this->type = $type;
		$this->display();
	}

	public function saveAttribute(){
		if ( !IS_POST ) redirect(__APP__);

		$type = I('post.type',0,'intval');
		$id   = I('post.id',0,'intval');

		switch ($type) {
			case '1':
				$fun = 'setBandWidth';
				break;
			case '2':
				$fun = 'setCpu';
				break;
			case '3':
				$fun = 'setMemory';
				break;
			default:
				redirect(__APP__);
				break;
		}

		$data = array(
				'cloud_server_id' => $id,
				'value' 		  => I('post.value','')
			);

		import('Class.Server',APP_PATH);
		/*发送请求*/
		$result = json_decode(Server::$fun($data),true);
		
		if ($result['code'] == 0) {
			$this->success('操作成功',$_SERVER['HTTP_REFERER']);
		} else {
			$this->error($result['error']);
		}
		
	}

	/*
		@der 删除数据操作
	*/
	public function delete(){
		$id = I('get.id',0,'intval');
		$where = array('id' =>$id);
		$name = M('Yunji_server')->where($where)->getField('name');

		import('Class.Server',APP_PATH);
		$result = Server::serverDelete($name,$id);
		if ($result['code'] == 0) {
			$this->success('删除成功');
		} else {
			$this->error($result['error']);
		}
	}

	/*
		@der 再次创建(仅限创建失败的服务器)
	*/
	public function create(){
		$db = M('Living');
		//查找出实例信息
		$id 	= I('get.id',0,'intval');
		$field  = 'cpu,city,osid,disk,band,name';
		$server = M('Yunji_server')->field()->where(array('id' => $id))->find();

		//验证是否是创建失败的
		if ($db->where(array('tid' => $id))->getField('status') != 1 ) {
			redirect(__APP__);
		}

		$where     = array('city_id' => $arr['city'] , 'status' => 1);
		$region_id = M('Mu_region')->field('id')->where($where)->select();
		$arr['region_id'] 			 = peatarr($region_id , 'id');
		$arr['image_id']  		     = $server['osid'];
		$arr['wan_upload_bandwidth'] = $server['band'];
   		$arr['cpu']	  = $server['cpu'];
		$arr['server_id'] = $id;
        // $arr['name']      = $server['name'];
       	
       	if (!empty($server['disk'])) {
       		$arr['disk'] = explode(',', $server['disk']);
       	}

       	import('Class.Server',APP_PATH);
		$result = json_decode(Server::createServer($arr),true);
       
		if ($result['code'] == 0) {  
			$this->success('创建成功！');
        //记录错误信息
		} else {
            $save = array(
                'remark' => $result['error'],
                'status' => 1,//创建失败
                );
            $db->where(array('tid' => $id))->save($save);

            $this->error($result['error']);
        }
        		
	}		

	/**
		@der 云服务器的 开机 关机 重启 删除
		@param int    $type 操作类型
							1、开机
							2、关机
							3、重启
							
		@return json
	*/
	public function serverOperation(){
		$type = I('get.type',3,'intval');
		$name = I('get.name','');

		import('Class.Server',APP_PATH);
		/*发送请求*/
		$result = json_decode(Server::serverOperation($name , $type),true);
		
		if ($result['code'] == 0) {
			$this->success('操作成功',$_SERVER['HTTP_REFERER']);
		} else {
			$this->error($result['error']);
		}
	}

	/**
		@der 处理接口请求
	*/
	// public function sendRequest(){
	// 	$type = I('get.type',3,'intval');
	// 	$name = I('get.name','');

	// 	import('Class.Server',APP_PATH);
	// 	/*发送请求*/
	// 	$result = json_decode(Server::serverOperation($name , $type),true);
		
	// 	if ($result['code'] == 0) {
	// 		$this->success('操作成功',$_SERVER['HTTP_REFERER']);
	// 	} else {
	// 		$this->error($result['error']);
	// 	}
	// }

	/**
		@der 获取状态
	*/
	private function getStatus($data , $status = ''){
		import('Class.Server',APP_PATH);
		$result = json_decode(Server::getStatus($data['name'] , $status),true);
		
		if (!$status) {
			if ($result['status'] != $data['status']) {
				$setField = array('status' => $result['status']);
				M('Living')->where(array('id' => $data['living_id']))->setField($setField);

				return true;
			}
		}

		return false;
	}

	/*
		@der 处理搜索
	*/
	private function _search($db,$where = ''){
		$tmp    = $_REQUEST; 
		
		if (!isset($tmp['status'])) {
			$tmp['status'] = -1;
		}

		$where || $where = array('isdelete' => 0);
		$pwhere = array();
		
		//如果有搜索
		if (isset($tmp['search'])) {
			$pwhere['search'] = 1;
			
			// 状态
			if ($tmp['status'] != -1) {

				//$tmp['status'] == 0 ? $status = $tmp['status'] : $status = 0;
				$where['status']  = $tmp['status'];				
				$pwhere['status'] = $tmp['status'];
			}

			//主机名
			if (!empty($tmp['name'])) {
				$where['name']  = array('LIKE',"{$tmp['name']}%");
				$pwhere['name'] = $tmp['name'];
			}

			//外网IP
			if (!empty($tmp['wan_ip'])) {
				$where['wan_ip']  = array('LIKE',"{$tmp['wan_ip']}%");
				$pwhere['wan_ip'] = $tmp['wan_ip'];
			}

			//开始时间
			if (!empty($tmp['min_start_time'])) {
				$date 				= strtotime($tmp['min_start_time']);	 
				$where['start']  	= array('EGT',$date);
				$pwhere['min_start_time'] = $tmp['min_start_time'];
			}

			//结束时间
			if (!empty($tmp['max_start_time'])) {
				$date 				= strtotime($tmp['max_start_time']);	 
				$where['start']  	= array('ELT',$date);
				$pwhere['max_start_time'] = $tmp['max_start_time'];
			}

		}

		//分页
		$page = X($db,$where,C('PAGE_NUM'),$pwhere);
		$data = $db->where($where)->limit($page['0']->limit)->select();

		return array($data,$tmp,$page);
	}

}