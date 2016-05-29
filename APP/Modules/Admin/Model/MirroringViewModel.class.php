<?php
Class MirroringViewModel extends ViewModel{
	public $viewFields = array(
				'mirroring' => array(
						'id','name','time','type','image_code','image_snap_code','uid','status','image_action','default_port',
						'_type' => 'LEFT'
					),
				'os' => array(
						'os_code','name'=>'os',
						'_on'=> 'mirroring.oid = os.id'
					),
			);

	/**
	 *	@der 处理搜索
	 */
	public function search(){
		$tmp    = $_REQUEST; 

		if (!isset($tmp['type'])) {
			$tmp['type'] = -1;
		}
		if (!isset($tmp['status'])) {
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

			//端口
			if ( !empty($tmp['default_port']) ) {
				$where['default_port']  = $tmp['default_port'];
				$pwhere['default_port'] = $tmp['default_port'];
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

			//标识
			if ( !empty($tmp['image_code']) ) {
				$where['image_code']  = array('LIKE',"{$tmp['image_code']}%");
				$pwhere['image_code'] = $tmp['image_code'];
			}

			//快照标识
			if ( !empty($tmp['image_snap_code']) ) {
				$where['image_snap_code']  = array('LIKE',"{$tmp['image_snap_code']}%");
				$pwhere['image_snap_code'] = $tmp['image_snap_code'];
			}

			//系统类型
			if ( !empty($tmp['os_code']) ) {
				$where['os_code']  = array('LIKE',"{$tmp['os_code']}%");
				$pwhere['os_code'] = $tmp['os_code'];
			}

		}

		//分页
		$page = X($this,$where,C('PAGE_NUM'),$pwhere);
		$data = $this->where($where)->limit($page['0']->limit)->order('id DESC')->select();

		return array($data,$tmp,$page);
	}
}