<?php
Class Task_logsViewModel extends ViewModel{
	public $viewFields = array(
			'task_logs' => array(
					'id','operate','time','uid','remark','status','info','is_show','info','type','ip',
					'_type' => 'LEFT'
				),
			'goods' => array(
					'goods' => 'goods_name',
					'_on'	=> 'task_logs.goods_id = goods.id'
				)
		);


	/**
		@der 处理搜索
	*/
	public function search(){
		$tmp    = $_REQUEST; 
		
		if (!isset($tmp['status'])) {
			$tmp['status'] = -1;
		}

		if (!isset($tmp['type'])) {
			$tmp['type'] = -1;
		}

		$pwhere = array();
		
		//如果有搜索
		if (isset($tmp['search'])) {
			$pwhere['search'] = 1;

			// 类型
			if ( $tmp['type'] != -1 ) {
				$where['type']  = $tmp['type'];				
				$pwhere['type'] = $tmp['type'];
			}

			// 状态
			if ( $tmp['status'] != -1 ) {
				$where['status']  = $tmp['status'];				
				$pwhere['status'] = $tmp['status'];
			}

			// 产品
			if ( !empty($tmp['goods_id']) ) {
				$where['goods_id']  = $tmp['goods_id'];				
				$pwhere['goods_id'] = $tmp['goods_id'];
			}

			//开始时间
			if ( !empty($tmp['min_start_time']) ) {
				$date 				= strtotime($tmp['min_start_time']);	 
				$where['time']  	= array('EGT',$date);
				$pwhere['min_start_time'] = $tmp['min_start_time'];
			}

			//结束时间
			if ( !empty($tmp['max_start_time']) ) {
				$date 				= strtotime($tmp['max_start_time']);	 
				$where['time']  	= array('ELT',$date);
				$pwhere['max_start_time'] = $tmp['max_start_time'];
			}

			//用户IP
			if ( !empty($tmp['ip']) ) {
				$where['ip']  = array('LIKE',"{$tmp['ip']}%");
				$pwhere['ip'] = $tmp['ip'];
			}

		}

		//分页
		$page = X($this,$where,C('PAGE_NUM'),$pwhere);
		$data = $this->where($where)->limit($page['0']->limit)->order('id DESC')->select();

		return array($data,$tmp,$page);
	}
}