<?php
/*
	@der  server控制器的普通模型
*/
Class TenxunModel extends Model{

	/*
		@der 获取监控数据
		@isReturn boolean 是否返回数据
		@return json || string
	*/
	public function get_chart_data($isReturn = false , $dimensions_value = ''){
		$dimensions_value = I('get.dimensions_value',$dimensions_value);
		$requestType   	  = I('get.requestType','1','intval');
		$timeType 	      = I('get.timeType','2','intval');
		$pointInterval    = 86400*1000;
		$period 	      = 86400;
		$endTime 	      = date('Y-m-d H:i:s',strtotime('today'));
		switch ($requestType) {
			case '2'://内存使用率
				$metricName = 'mem_used';
				break;
			case '3'://外网出包量
				$metricName = 'outpkg';
				break;
			case '4'://磁盘读流量
				$metricName = 'disk_read_traffic';
				break;
			default://cpu使用率
				$metricName = 'cpu_usage';
				break;
		}

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
				$period 	   = 300;
				$pointInterval = 300 * 1000;
				$startTime     = date('Y-m-d H:i:s' , strtotime('today'));
				$endTime 	   = date('Y-m-d H:i:s' , time());
				break;
		}

		/*获取监控指标列表*/
		import('Class.Tenxunyun',APP_PATH);	
		$arr = array(
			'namespace'			 => 'qce/cvm',
			'dimensions.1.name'  => 'instanceId',
			'dimensions.1.value' => $dimensions_value,
			'startTime'			 => $startTime,
			'endTime'			 => $endTime,	
			'metricName'		 => $metricName,
			'period'			 => $period,	
			);
		$result = Tenxunyun::sendRequest($arr,'GetMetricStatistics','monitor.api.qcloud.com');
		
		/*如果不是实时，需要翻转数组*/
		if ( $timeType != 1) {
			$result['dataPoints'] = array_reverse($result['dataPoints'] , true);
		}
		/*将内容为空的数组替换成0*/
		foreach ($result['dataPoints'] as $key => $v) {
			if (empty($v)) {
				$result['dataPoints'][ $key ] = 0;
			} 
		}

		$json 	= array(
			'data' 			=> '['.implode(',' , $result['dataPoints']).']',
			'pointInterval' => $pointInterval
			);
		
		if ($isReturn) {
			return $result['dataPoints'];
		} else {
			echo json_encode($json);
		}
	}

	/*
		@der 处理服务器重启操作
	*/
	public function server_restart(){
		$db = M('Living');
		/*验证数据*/
		list($instance_id , $count ,$where) = $this->check_request($db);

		$request = array();
		foreach ($instance_id as $key => $v) {
			$request["instanceIds.{$key}"] = $v['instance_id'];
		}
		
		/*发送重启请求*/
		import('Class.Tenxunyun',APP_PATH);	
		//设置地域
		Tenxunyun::set('Region' , I('post.region' , 'gz'));		 
		$result = Tenxunyun::sendRequest($request,'RestartInstances','cvm.api.qcloud.com');
		
		return $this->return_result($db,$where,$result,7);
	}

	/*
		@der 处理改变实例状态额异步请求
		@param int $status [要修改的状态]
	*/
	public function change_status($status = 2){
		$db = M('Living');
		/*验证数据*/
		list($instance_id , $count) = $this->check_request($db,'id,instance_id');

		/*请求腾讯云，判断重启是否成功*/
		$result = array();
		$arr 	= array();
		import('Class.Tenxunyun',APP_PATH);	
		foreach ($instance_id as $key => $v) {
			$result["instanceIds.{$key}"] = $v['instance_id'];
			$arr[ $v['instance_id'] ] 	  = $v['id'];
		}
		//设置地域
		Tenxunyun::set('Region' , I('post.region' , 'gz'));		  
		$result = Tenxunyun::sendRequest($result,'DescribeInstances','cvm.api.qcloud.com');
		
		$i 	  = 0;
		$data = array(); 
		foreach ($result['instanceSet'] as $v) {
			
			//请求成功时
			if ($v['code'] == 0) {
				//状态改变成功的
				if ($v['status'] == $status) {
					
					//修改实例状态
					$db->where(array('id' => $arr[$v['instanceId']] ))->setField(array('status' => $status));
					$data[] = $arr[$v['instanceId']];
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

	/*
		@der 验证请求数据
		@param object $db 数据库连接对象
		@param string $field 要查询的字段
		@return array || null
	*/
	private function check_request($db,$field){
		$where = array('id' => array('IN' , I('post.id')) , 'uid' => $_SESSION['id'] ); 
		
		//根据ID查找出云服务器ID
		$instance_id = $db->field($field)->where($where)->select();
		$count = count( explode(',',I('post.id')) );

		/*验证数据*/
		if (empty($instance_id) || count($instance_id) != $count) {
			redirect(__APP__);
		}
		return array($instance_id,$count,$where);
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
			$db->where($where)->save(array('status' => $status));
			return json_encode( array('status' => 1 , 'message' => $message) );
		} else {
			return json_encode( array('status' => 0 , 'message' => $result['message']) );
		}
	}
}