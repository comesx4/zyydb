<?php
/**
	@der 云集服务器Model
*/
Class ServerModel extends Model{

		/*
		@der 获取监控数据
		@return json 
	*/
	public function get_chart_data($dimensions_value = '',$server_name = ''){
		$server_name 	  = I('get.server_name',$server_name);
		$dimensions_value = I('get.dimensions_value',$dimensions_value);
		$requestType   	  = I('get.requestType','1','intval');
		$timeType 	      = I('get.timeType','1','intval');
		//是否是实时
		$isRealTime		  = I('get.isRealTime',''); 
		$pointInterval 	  = 60 * 1000;
		$year		  	  = date('Y');
		$month			  = date('m');
		$day			  = date('d');
		$hour			  = 0;
		$minute			  = 0;

		//缓存名称
		$cacheName		  = "monitor_{$dimensions_value}_{$requestType}_{$date['0']}_{$date['1']}_{$date['2']}_{$date['3']}";
	
		//获取服务器名称
		$name = M('Yunji_server')->where(array('id' => $dimensions_value))->getField('name');

		$where = array('server_name' => $server_name);
		$field  = 'server_name,cpu,lan_rx_rate,lan_tx_rate,wan_rx_rate,wan_tx_rate';
		switch ($timeType) {
			case '2'://昨天		
				$table_name    = date('Y_m_d',strtotime('-1 day'));		
				$startTime     = strtotime('today') - (86400);
				$endTime	   = strtotime('today');
				$where['time'] = array( array('egt',$startTime) , array('elt',$endTime) );

				$day = sprintf('%02d',$day-1);
				break;

			case '3'://今天			
				$table_name    = date('Y_m_d');		
				$startTime     = strtotime('today');
				$where['time'] = array('egt',$startTime);
				break;

			case '4'://30天		
				$table_name    = date('Y_').sprintf('%02d',date('m')-1);	
				$table_name2   = date('Y_m');				
				$startTime     = strtotime('-30 day');		
				$endTime	   = strtotime('today');
				$where['time'] = array( array('egt',$startTime) , array('elt',$endTime) );
				
				$result  = M($table_name,'kz_','DB_DSN2')->field($field)->where($where)->select();
				!$result && $result = array();
				$result2 = M($table_name2,'kz_','DB_DSN2')->field($field)->where($where)->select();
				!$result2 && $result2 = array();
				$result  = array_merge($result,$result2);	

				$period 	   = 86400;
				$pointInterval = 86400 * 1000;

				$length = strtotime('-'.count($result).' day');
				$year   = date('Y',$length);
				$month  = date('m',$length);
				$day	= date('d',$length);
				break;

			default://实时
				$table_name    = date('Y_m_d');		
				$startTime     = $_SERVER['REQUEST_TIME']-7200;
				$hour		   = date('H',$startTime);
				$minute		   = date('i',$startTime);
				$where['time'] = array('egt',$startTime);
				break;
		}
	
		/*获取监控指标列表*/
		switch ($requestType) {
			case 1://CPU
				$keys = 'cpu';
				break;
			case 2:
				$keys = 'lan_rx_rate';
				break;
			case 3:
				$keys = 'lan_tx_rate';
				break;
			case 4:
				$keys = 'wan_rx_rate';
				break;
			case 5:
				$keys = 'wan_tx_rate';
				break;
		}

		//不为30天时
		if ($timeType != 4) {
			if ($isRealTime) {
				$data   = M($table_name,'kz_','DB_DSN2')->where($where)->order('id DESC')->limit('1')->getField($keys);
				
			} else {
				$result = M($table_name,'kz_','DB_DSN2')->field($field)->where($where)->select();
			}
			
		}

		//为昨天时
		if ($timeType == 2) {
			$length = count($result) - 1440;

			$hour	= round($length / -60);			
		}
		
		if ($result && !$isRealTime) {
			$data = '[';
			foreach ($result as $key => $value) {
				$data .= "$value[$keys],";
			}
			$data = rtrim($data,',').']';
		}
		
		/*处理和JS UTC 时区的差，JS比这里会多一个月*/
		if ($month != 1) {
			$month--;
		} else {
			$year--;
			$month = 12;
		}

		$json 	= array(
			'data' 			=> $data,
			'pointInterval' => $pointInterval,
			'year'			=> $year,
			'month'		    => $month,
			'day'			=> $day,
			'hour'		    => $hour,
			'minute'		=> $minute
			);
		//p($json);die;
		$json = json_encode($json);

		return $json;
	}

	/**
		@der 获取最后一条监控
		@param array $living  数据
		@return array
	*/
	public function getLastMonitor(array $living){
		$where = array('name' => $living['name']);
		$last_monitor  = M(date('Y_m_d') ,'kz_','DB_DSN2')->field('id,server_name,time',true)->where($where)->order('id DESC')->find();
		$new_monitor = array();
		foreach ($last_monitor as $key => $v) {
			switch ($key) {
				case 'lan_rx_rate':
					$value = $living['lan_download_bandwidth'];
					break;
				case 'lan_tx_rate':
					$value = $living['lan_upload_bandwidth'];					
					break;
				case 'wan_rx_rate':
					$value = $living['lan_download_bandwidth'];
					break;
				case 'wan_tx_rate':
					$value = $living['band'];
					break;
				case 'cpu':
					$value = $living['cpu'];
					break;
			}

			$avg = ceil(($v/1024)/$value);
			$avg == 0 && $avg = 1;
			
			switch ($avg) {
				case $avg < 30:
					$class = 'progress-bar-success';
					break;
				case $avg < 60:
					$class = 'progress-bar-info';
					break;
				case $avg < 85:
					$class = 'progress-bar-warning';
					break;
				default:
					$class = 'progress-bar-danger';
					break;
			}

			$new_monitor[$key]['prev']  = $v;
			$new_monitor[$key]['avg']   = $avg;
			$new_monitor[$key]['class'] = $class;
		}

		return $new_monitor;
	}

	/**
		@der 云服务器的 开机 关机 重启 删除
		@param int    $type 操作类型
							1、开机
							2、关机
							3、重启
							4、删除
		@return json
	*/
	public function serverOperation($type = 3){
		//获取服务器名称
		list($server_name,$length) = $this->check_request( M('Yunji_server') , 'name');
		import('Class.Server',APP_PATH);
		/*循环发送请求*/
		$result = array();
		foreach ($server_name as $v) {
			$result = json_decode(Server::serverOperation($v['name'] , $type),true);
		} 
		
		$pwhere['tid'] = $where['id'];

		return $this->return_result(M('Living'),$pwhere,$result);
	}
	

	/*
		@der 处理改变实例状态额异步请求
		@param int $status [要修改的状态]
	*/
	public function changeStatus($status = 2){
		$db = M('Living');
		/*验证数据*/
		list($server , $count) = $this->check_request(M('Yunji_server'),'id,name');

		/*请求接口，判断重启是否成功*/
		$result = array();
		$arr 	= array();
		import('Class.Server',APP_PATH);
		foreach ($server as $key => $v) {
			$result[$key] 		= json_decode(Server::getStatus($v['name'] , $status),true);
			$result[$key]['id'] = $v['id'];
			$arr[ $v['id'] ] 	= $v['id'];
		}
		
		$i 	  = 0;
		$data = array(); 
		foreach ($result as $v) {
			
			//请求成功时
			if ($v['code'] == 0) {
				//状态改变成功的
				//if ($this->status_convert($v['result']['status']) == $status) {
					
					//修改实例状态
					//$db->where(array('tid' => $arr[$v['id']] ))->setField(array('status' => $status));
					$data[] = $arr[$v['id']];
					$i++;
				//}	
			}
		}

		if ($i != 0) {
			if ($i == $count) {
				//全部修改成功
				return json_encode(array('status' => 2 , 'id' => $data));
			} else {
				//部分修改成功
				return json_encode(array('status' => 1 , 'id' => $data));
			}
			
		} else {
			//无修改成功
			return json_encode(array('status' => 0 , 'message' => '哎呀，出错啦！'));
		}
	}

	/**
		@der 修改服务器密码
	*/
	public function changePassword(){
		//获取服务器名称
		list($server,$length) = $this->check_request( M('Yunji_server') , 'name');
		import('Class.Server',APP_PATH);
		$data = array(
			'name' 	   => $server['0']['name'],
			'password' => I('post.password',''),
			);
		$result = Server::changePassword($data , 1);
		
		return $this->return_result('','',$result);
	}

	/**
		@der 重装系统
	*/
	public function reOs(){
		//获取服务器名称
		list($server,$length) = $this->check_request( M('Yunji_server') , 'name');

		import('Class.Server',APP_PATH);
		$data = array(
			'name' 	   => $server['0']['name'],
			'password' => I('post.newPassword',''),
			'image_id' => I('post.osid')
			);
		
		$result = Server::reInstallOs($data);
		
		return $this->return_result('','',$result);
	}

	/*
		@der 验证请求数据
		@param object $db 数据库连接对象
		@param string $field 要查询的字段
		@return array || null
	*/
	private function check_request($db,$field){
		$id    = I('post.id');
		$where = array('id' => array('IN' , $id) , 'uid' => $_SESSION['id'] ); 
		
		//根据ID查找出云服务器信息
		$server_id = $db->field($field)->where($where)->select();
		$count = count( explode(',',$id) );
		
		/*验证数据*/
		if (empty($server_id)) {
			redirect(__APP__);
		}

		return array($server_id,$count,$where);
	}

	/*
		@der 对修改实例状态的判断，并返回数据
		
		@param object $db 数据库连接对象
		@param where  $where where条件
		@param result 腾讯云返回的数据
		@param int  $status 修改的状态
		@param string $message 成功之后的消息
		@return json
	*/
	private function return_result($db,$where,$result,$status,$message = '成功！'){

		if ($result['code'] == 0) {
			
			/*改变服务器状态*/
			//$db->where($where)->save(array('status' => $status));
			return json_encode( array('status' => 1 , 'message' => $message) );
		} else {

			return json_encode( array('status' => 0 , 'message' => '哎呀，出错啦！') );
		}
	}

	/**
	 *	@der 状态转换
     *	@param string $key 状态
     *	@return int status
	 */
	private function status_convert($key){

		$status = array(
			'installing' => 3,  //正在安装
			'reinstall'  => 14, //正在重装
			'booting'    => 8,  //正在启动
			'running' 	 => 2,  //运行中
			'shutdown'	 => 9,  //关机中
			'poweroff'	 => 4,	//已关机
			'changepw'	 => 10, //密码修改中,
			'reboot'	 => 7,  //重启中
			'rollback'	 => 11,  //正在回滚
			'failed'	 => 1,	//创建失败
			'deleted'	 => 23, //已删除
			);

		return $status[$key];
	}

}