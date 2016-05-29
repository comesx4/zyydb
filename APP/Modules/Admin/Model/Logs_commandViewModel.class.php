<?php
/*
	@der 命令日志
*/
Class Logs_commandViewModel extends ViewModel{
	public $viewFields = array(
		'logs_command' => array(
			'id','command_id','user_id','client_ip','server_ip','secret_key','content','result','status','process_time','time','operate',
			'_type' => 'LEFT'
			),
		'command' => array(
			'name','title','rule','remark','status' => 'command_status',
			'_on' => 'logs_command.command_id = command.id'
			),
		);

	
	/**
		@der 处理搜索
	*/
	public function search(){
		$tmp    = $_REQUEST; 
		
		if (!isset($tmp['status'])) {
			$tmp['status'] = -1;
		}

		$pwhere = array();
		
		//如果有搜索
		if (isset($tmp['search'])) {
			$pwhere['search'] = 1;
			
			//命令名
			if ( !empty($tmp['title']) ) {
				$where['title']  = $tmp['title'];
				$pwhere['title'] = $tmp['title'];
			}

			//用户ID
			if ( !empty($tmp['user_id']) ) {
				$where['user_id']  = $tmp['user_id'];
				$pwhere['user_id'] = $tmp['user_id'];
			}

			//用户IP
			if ( !empty($tmp['client_ip']) ) {
				$where['client_ip']  = array('LIKE',"{$tmp['client_ip']}%");
				$pwhere['client_ip'] = $tmp['client_ip'];
			}

			//服务器IP
			if ( !empty($tmp['server_ip']) ) {
				$where['server_ip']  = array('LIKE',"{$tmp['server_ip']}%");
				$pwhere['server_ip'] = $tmp['server_ip'];
			}

			// 状态
			if ( $tmp['status'] != -1 ) {
				$where['status']  = $tmp['status'];				
				$pwhere['status'] = $tmp['status'];
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

		}

		//分页
		$page = X($this,$where,C('PAGE_NUM'),$pwhere);
		$data = $this->where($where)->limit($page['0']->limit)->order('id DESC')->select();

		return array($data,$tmp,$page);
	}
}