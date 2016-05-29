<?php
Class Cloud_snapViewModel extends ViewModel{
	public $viewFields = array(
		'cloud_snap' => array(
			'id','snap_name','snap_code','snap_type','status','time',
			'_type' => 'LEFT'
			),
		'cloud_disk' => array(
			'disk_name','disk_code',
			'_on' => 'cloud_snap.cloud_disk_id = cloud_disk.id',
			'_type' => 'LEFT'
			),
		'yunji_server' => array(
			'name' => 'server_name','server_alias',
			'_on' => 'cloud_disk.cloud_server_id = yunji_server.id'
			),
		);

	 /**
	 * 	@der 处理搜索
	 * 	@return array(result,tmp)
	 */
	public function search(){
		$tmp    = $_REQUEST;
		$where= array('cloud_snap.uid' => $_SESSION['id'],'type' => array('NEQ',3));
		$pwhere = array();
		//如果有搜索
		if (isset($tmp['submit'])) {
			$pwhere['submit'] = 1;
			// 状态
			if (!empty($tmp['status'])) {
				$tmp['status'] != -1 ? $status = $tmp['status'] : $status = 0;
				$where['cloud_snap.status']  = $status;				
				$pwhere['status'] 			 = $tmp['status'];
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

			//快照标识
			if (!empty($tmp['snap_code'])) {
				$where['snap_code']  = array('LIKE',"{$tmp['snap_code']}%");
				$pwhere['snap_code'] = $tmp['snap_code'];
			}

			//快照别名
			if (!empty($tmp['snap_name'])) {
				$where['snap_name']  = array('LIKE',"{$tmp['snap_name']}%");
				$pwhere['snap_name'] = $tmp['snap_name'];
			}

		}

		//分页
		$page = X($this,$where,C('page_num'),$pwhere);
		$data = $this->where($where)->limit($page['0']->limit)->order('id DESC')->select();

		return array($data,$tmp,$page);
	}
}