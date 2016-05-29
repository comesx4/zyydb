<?php
/*
	@der 母服务器IP地址管理
*/
Class MuIpAction extends CommonAction{

	/*
		@der 列表页
	*/
	public function index(){

		$db = D('Mu_ipView');
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
		
		//网关下拉列表
		$this->gateway = $this->get_gateway();

		$this->display();
	}

	/*
		@der 添加数据操作
	*/
	public function insert(){
		if ( !IS_POST ) redirect(__APP__);
		
		$ipduan_from	=I('post.ipduan_from','1');
		$ipduan_to		=I('post.ipduan_to','1');
		$ip_gateway_id	=I('post.ip_gateway_id','1');

		$field = array('region_id');
		$where = array('id'=>$ip_gateway_id);
		$item  = M('ip_gateway')->field($field)->where($where)->find();

		$ips = array();
		for($i=$ipduan_from;$i<=$ipduan_to;$i++){

			$ips[] = array('region_id'	=>$item['region_id'],
						   'ip_gateway_id'=>$ip_gateway_id,
						   'ip'			=>I('post.ipduan').'.'.$i,
						   'mac'		=>creatMac(),
						   'type'		=>I('post.type',''),
						   'status'		=>I('post.status','1'),
						   'orderby'	=>$i,
						   'remark'		=>I('post.remark',''),
						   'time'		=>time()
						   );

		}//for

		if(M('Mu_ip')->addAll($ips)) {
			$this->success('添加成功！',U('index'));
		} else {
			$this->error('添加失败！');
		} //if
	}

	/*
		@der 修改页面
	*/
	public function update(){
		$where = array('id' => I('get.id',0,'intval'));
		$field = array('id,region_id,ip_gateway_id,ip,mac,type,status,remark,time');
		$data = M('Mu_ip')->field($field)->where($where)->find();

		//网关下拉列表
		$this->gateway = $this->get_gateway('ip_gateway_id' , $data['ip_gateway_id']);
		$this->data    = $data;
		$this->display();
	}

	/*
		@der 修改数据操作
	*/
	public function save(){
		if ( !IS_POST ) redirect(__APP__);
		// if ( post_isnull('region_id','server_ip','server_port','secret_key','server_can_sum','server_sum','status', 'version') ) {
		// 	$this->error('请填写完整!');
		// }
		$id = I('id',0,'intval');

		$ip_gateway_id	=I('ip_gateway_id','1');

		$field = array('region_id');
		$where = array('ip_gateway_id'=>$ip_gateway_id);
		$item  = M('Ip_gateway')->field($field)->where($where)->find();

		$data = array('id'			=>$id,
					  'region_id'	=>$item['region_id'],
					  'ip_gateway_id'=>$ip_gateway_id,
					  'ip'			=>I('post.ip',''),
					  'mac'			=>I('post.mac',''),
					  'type'		=>I('post.type',''),
					  'status'		=>I('post.status',1),
					  'remark'		=>I('post.remark',''),
					  'time'		=>time()
					  );
		
		if(M('Mu_ip')->save($data)) {
			$this->success('编辑成功！',U('index'));
		}else{
			$this->error('编辑失败！');
		}//if
	}

	/*
		@der 删除数据操作
	*/
	public function delete(){
		$this->db_delete(M('Mu_ip'));
	}

	/**
		@der 组合IP网关下拉列表
		@param string $name 下拉列表的name值
		@param int $pid 最上面显示的内容
		@return select标签
	*/
	private function get_gateway($name = 'ip_gateway_id' , $pid = 0){
		$field = 'id,lan_gateway,wan_gateway';
		$data  = M('Ip_gateway')->field($field)->order('id DESC')->select();
		
		$str    = "<select name='{$name}'>";
		$selected = '';
		foreach ($data as $v) {
			if ( $pid ) {
				if ($v['id'] == $pid) {
					$selected = 'selected';
				} else {
					$selected = '';
				}
			}
			$str .= "<option {$selected} value='{$v['id']}'>{$v['wan_gateway']}(外网网关)&nbsp;{$v['lan_gateway']}内网网关</option>";
		}
		$str .= '</select>';

		return $str;
	}

	/*
		@der 处理搜索
	*/
	private function _search($db,$where = ''){
		$tmp    = $_REQUEST; 
		
		if (!isset($tmp['status'])) {
			$tmp['status'] = -1;
		}

		$pwhere = array();
		
		//如果有搜索
		if (isset($tmp['search'])) {
			$pwhere['search'] = 1;
			
			// 状态
			if ( $tmp['status'] != -1 ) {
				$where['status']  = $tmp['status'];				
				$pwhere['status'] = $tmp['status'];
			}

			// IP类型
			if ( $tmp['type'] != -1 ) {
				$where['type']  = $tmp['type'];				
				$pwhere['type'] = $tmp['type'];
			}

			//IP
			if ( !empty($tmp['ip']) ) {
				$where['ip']  = array('LIKE',"{$tmp['ip']}%");
				$pwhere['ip'] = $tmp['ip'];
			}

			//mac地址
			if ( !empty($tmp['mac']) ) {
				$where['mac']  = array('LIKE',"{$tmp['mac']}%");
				$pwhere['mac'] = $tmp['mac'];
			}

			//外网IP
			if ( !empty($tmp['wan_ip']) ) {
				$where['wan_ip']  = array('LIKE',"{$tmp['wan_ip']}%");
				$pwhere['wan_ip'] = $tmp['wan_ip'];
			}
			
			//网关IP
			if ( !empty($tmp['wan_gateway']) ) {
				$where['wan_gateway']  = array('LIKE',"{$tmp['wan_gateway']}%");
				$pwhere['wan_gateway'] = $tmp['wan_gateway'];
			}

			//开始时间
			if ( !empty($tmp['min_start_time']) ) {
				$date 				= strtotime($tmp['min_start_time']);	 
				$where['start']  	= array('EGT',$date);
				$pwhere['min_start_time'] = $tmp['min_start_time'];
			}

			//结束时间
			if ( !empty($tmp['max_start_time']) ) {
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