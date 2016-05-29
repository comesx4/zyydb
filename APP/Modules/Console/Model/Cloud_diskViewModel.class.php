<?php
/*
	@der 云磁盘视图模型
*/
Class Cloud_diskViewModel extends ViewModel{
	public $viewFields = array(
			'cloud_disk' => array(	
					'id','disk_name','disk_code','cloud_server_id','target','size','iops','io','type','status','time'
				),
			'yunji_server' => array(
					'name','server_alias',
					'_on' => 'cloud_disk.cloud_server_id = yunji_server.id'
				)
		);


    /**
	 * 	@der 处理搜索
	 * 	@return array(result,tmp)
	 */
	public function search(){
		$tmp    = $_REQUEST;
		$where= array('uid' => $_SESSION['id'],'type' => array('NEQ',3));
		$pwhere = array();
		//如果有搜索
		if (isset($tmp['submit'])) {
			$pwhere['submit'] = 1;
			// 类型
			if (!empty($tmp['target'])) {
				if ( $tmp['target'] == 1 ) {
					$where['target']  = 'vda';
				} else {
					$where['target']  = array('NEQ','vda');
				}
				
				$pwhere['target'] = $tmp['target'];
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

			//硬盘标识
			if (!empty($tmp['disk_code'])) {
				$where['disk_code']  = array('LIKE',"{$tmp['disk_code']}%");
				$pwhere['disk_code'] = $tmp['disk_code'];
			}

			//硬盘别名
			if (!empty($tmp['disk_name'])) {
				$where['disk_name']  = array('LIKE',"{$tmp['disk_name']}%");
				$pwhere['disk_name'] = $tmp['disk_name'];
			}

		}

		//分页
		$page = X($this,$where,C('page_num'),$pwhere);
		$data = $this->where($where)->limit($page['0']->limit)->order('id DESC')->select();

		return array($data,$tmp,$page);
	}
}