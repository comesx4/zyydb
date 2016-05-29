<?php
/*
	@der 云服务器列表页
*/
Class LivingViewModel extends ViewModel{
	public $viewFields = array(
			'living' => array(
					'id','tid','gid','status','end',
					'_type' => 'LEFT'
				),
			'yunji_server' => array(
					'lan_ip' ,'wan_ip','name' ,'server_alias','band',
					'_on'=> 'living.tid = yunji_server.id'
				),
		);

	/**
	 * 	@der 处理搜索
	 * 	@return array(result,tmp,page)
	 */
	public function search(){
		$tmp    = $_REQUEST;
		$where  = array('uid' => $_SESSION['id'] , 'gid' => 1 , 'status' => array('NEQ',0) ,'isdelete' => 0);
		$pwhere = array();
		//如果有搜索
		if (isset($tmp['submit'])) {
			$pwhere['submit'] = 1;
			// 状态
			if ($tmp['status'] != '-1') {
				$where['status']  = $tmp['status'];
				$pwhere['status'] = $tmp['status'];
			}

			//主机名
			if (!empty($tmp['name'])) {
				$where['name']  = array('LIKE',"{$tmp['name']}%");
				$pwhere['name'] = $tmp['name'];
			}

			//别名
			if (!empty($tmp['server_alias'])) {
				$where['server_alias']  = array('LIKE',"{$tmp['server_alias']}%");
				$pwhere['server_alias'] = $tmp['server_alias'];
			}

			//外网IP
			if (!empty($tmp['wan_ip'])) {
				$where['wan_ip']  = array('LIKE',"{$tmp['wan_ip']}%");
				$pwhere['wan_ip'] = $tmp['wan_ip'];
			}

		}

		//分页
		$page = X($this,$where,C('page_num'),$pwhere);
		$data = $this->where($where)->limit($page['0']->limit)->select();

		return array($data,$tmp,$page);
	}
}