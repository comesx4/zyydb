<?php
/**
	@der 后台记录视图模型
*/
Class Admin_detailViewModel extends ViewModel{
	public $viewFields = array(
		'admin_detail' => array(
			'id','name','remark','time',
			'_type' => 'LEFT'
			),
		'admin' => array(
			'username',
			'_on' => 'admin_detail.uid = admin.id'
			),
		);

	/**
		@der 处理搜索
	*/
	public function search($where){
		$pwhere = array();
		$tmp	= $_REQUEST;

		if (isset($tmp['search'])) {
			
			$pwhere['search'] = 1;
			//地址
			if (!empty($tmp['name'])) {
				$where['name']  = array('LIKE',"{$tmp['name']}%");
				$pwhere['name'] = $tmp['name'];
			}
			//用户名
			if (!empty($tmp['username'])) {
				$where['username']  = array('LIKE',"{$tmp['username']}%");
				$pwhere['username'] = $tmp['username'];
			}

			//最小日期
			if (!empty($tmp['min-date'])) {
				$time = strtotime($tmp['min-date']);
				$where['time']  	= array('egt',"{$time}%");
				$pwhere['min-date'] = $tmp['min-date'];
			}
			//最大日期
			if (!empty($tmp['max-date'])) {
				$time = strtotime($tmp['max-date']);
				$where['time']  	= array('elt',"{$time}%");
				$pwhere['max-date'] = $tmp['max-date'];
			}
		}
		
		return array($where,http_build_query($pwhere),$tmp);
	}
}