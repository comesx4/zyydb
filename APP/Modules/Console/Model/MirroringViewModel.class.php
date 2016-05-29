<?php
Class MirroringViewModel extends ViewModel{
	public $viewFields = array(
		'mirroring' => array(
			'id','default_size','name' => 'image_name','status','time',
			'_type' => 'LEFT'
			),
		'os' => array(
			'name',
			'_on' => 'mirroring.oid = os.id',
			
			),
		
		);

	 /**
	 * 	@der 处理搜索
	 * 	@return array(result,tmp)
	 */
	public function search(){
		$tmp    = $_REQUEST; 
		$where = array('mirroring.uid' => $_SESSION['id']);
		$pwhere = array();
		//如果有搜索
		if (isset($tmp['submit'])) {
			$pwhere['submit'] = 1;
			// 状态
			if (!empty($tmp['status'])) {
				if ($tmp['status'] == -1) {
					$where['status']  = 0;		
				} elseif ($tmp['status'] == 1) {
					$where['status']  = 1;
				} else {
					$where['status'] = array('NOT IN','1,0');
				}
				
				$pwhere['status'] 			 = $tmp['status'];
			}

			//镜像名称
			if (!empty($tmp['mirroring_name'])) {
				$where['mirroring.name']  = array('LIKE',"{$tmp['mirroring_name']}%");
				$pwhere['mirroring_name'] = $tmp['mirroring_name'];
			}

			//操作系统
			if (!empty($tmp['os_name'])) {
				$where['os.name']  = array('LIKE',"{$tmp['os_name']}%");
				$pwhere['os_name'] = $tmp['os_name'];
			}

		}

		//分页
		$page = X($this,$where,C('page_num'),$pwhere);
		$data = $this->where($where)->limit($page['0']->limit)->order('id DESC')->select();

		return array($data,$tmp,$page);
	}
}