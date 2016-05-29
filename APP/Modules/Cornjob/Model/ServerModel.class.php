<?php
/**
	@der 云集服务器Model
*/
Class ServerModel extends Model{

		/*
		@der 获取监控数据
		@return json || string
	*/
	public function get_chart_data($dimensions_value = ''){

		$dimensions_value = I('get.dimensions_value',$dimensions_value);
		$requestType   	  = I('get.requestType','1','intval');
		$type             = $requestType;
		$startTime		  = explode(' ' , date('Y-m-d H:i:s') );
		$date			  = explode('-', $startTime['0']);
		$date['3']		  = explode(':', $startTime['1'])['0'];
		//缓存名称
		$cacheName		  = "monitor_{$dimensions_value}_{$requestType}_{$date['0']}_{$date['1']}_{$date['2']}_{$date['3']}";
	
		//获取服务器名称
		$name = M('Yunji_server')->where(array('id' => $dimensions_value))->getField('name');

		switch ($timeType) {
			case '2'://7天				
				$startTime     = date('Y-m-d H:i:s' , strtotime('today') - (86400 * 6));
				break;
			case '3'://15天			
				$startTime     = date('Y-m-d H:i:s' , strtotime('today') - (86400 * 14));
				break;
			case '4'://30天				
				$startTime     = date('Y-m-d H:i:s' , strtotime('today') - (86400 * 29));
				break;
			default://实时
				$period 	   = 60;
				$pointInterval = 60 * 1000;
				break;
		}

		$requestType == 3 && $type = 2;

		/*获取监控指标列表*/
		import('Class.Server',APP_PATH);	
		$data = array(
			'name'  => $name , 
			'year'  => $date['0'],
			'month' => $date['1'],
			'day'   => $date['2'],
			'hour'  => $date['3']-1,
			);

		$result = json_decode(Server::getMonitorUsage($data , $type),true);
		

		if ($result['code'] != 0 ) {
			return json_encode(array('status' => 0 , 'message' => '获取失败！'));
		} 

		switch ($requestType) {
			case 1://CPU
				$data = $result['result']['usage'];
				break;
			case 2:
				$data = $result['result']['wan']['tx_rate'];
				break;
			case 3:
				$data = $result['result']['lan']['tx_rate'];
				break;
		}

		$json 	= array(
			'data' 			=> '['.implode(',' , explode(' ' , $data ) ).']',
			'pointInterval' => $pointInterval
			);
		
		$json = json_encode($json);

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
			$result[$key] 		= json_decode(Server::getStatus($v['name']),true);
			$result[$key]['id'] = $v['id'];
			$arr[ $v['id'] ] 	= $v['id'];
		}
		
		$i 	  = 0;
		$data = array(); 
		foreach ($result as $v) {
			
			//请求成功时
			if ($v['code'] == 0) {
				//状态改变成功的
				if ($this->status_convert($v['result']['status']) == $status) {
					
					//修改实例状态
					$db->where(array('tid' => $arr[$v['id']] ))->setField(array('status' => $status));
					$data[] = $arr[$v['id']];
					$i++;
				}	
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
			'rollback'	 => 99,  //正在回滚，不清楚。。
			'failed'	 => 1,	//创建失败
			'deleted'	 => 23, //已删除
			);

		return $status[$key];
	}

}