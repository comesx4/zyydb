<?php
/*
	@der 获取监控数据
*/
Class MonitorAction extends CommonAction{

	/*
		@der 获取监控数据并插入数据库
	*/
	public function index(){
		//判断天表示否存在，不存在则创建
		$table_name = date('Y_m_d');
		$sql = "CREATE TABLE IF NOT EXISTS kz_{$table_name}(`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`id`), `server_name` VARCHAR(12) NOT NULL COMMENT '服务器名称' , `cpu` SMALLINT(3) UNSIGNED NOT NULL COMMENT 'CPU使用率' , `lan_rx_rate` INT NOT NULL COMMENT '内网接收速度，字节/秒' , `lan_tx_rate` INT NOT NULL COMMENT '内网发送速度，字节/秒' , `wan_rx_rate` INT NOT NULL COMMENT '外网接收速度，字节/秒' , `wan_tx_rate` INT NOT NULL COMMENT '外网发送速度，字节/秒' , `time` INT(10) NOT NULL COMMENT '生成时间' , INDEX `server_name_time` (`server_name`, `time`)) ENGINE = MyISAM;";
		M()->execute($sql);
	
		$add  = array();
		$time = $_SERVER['REQUEST_TIME'];
		import('Class.Server',APP_PATH);
		/*查找出所有开启状态的母服务器ID*/
		$mu_server_id = M('Mu_server','kz_','DB_DSN2')->field('id')->where(array('status' => 1))->select();
		/*循环发送请求获取实时监控*/
		foreach ($mu_server_id as $v) {

			$result = Server::getMonitor($v['id']);
			
			if ($result['code'] != 0) {
				echo "{$result['error']}\n";
				continue;
			} 
			/*组合监控数据*/
			unset($result['result']['result']);
			foreach ($result['result'] as $key => $value) {
				
				$add[] = array(
					'server_name' => $key,
					'cpu'  		  => $value['cpu'],
					'lan_rx_rate' => $value['lan']['rx_rate'],
					'lan_tx_rate' => $value['lan']['tx_rate'],
					'wan_rx_rate' => $value['wan']['rx_rate'],
					'wan_tx_rate' => $value['wan']['tx_rate'],
					'time' 		  => $time
					);				
			}
		}
		if (empty($add)) {
			die("{$time}:data is empty");
		}
		
		if (M($table_name,'kz_','DB_DSN')->addAll($add)) {
			echo "{$time}:insert ok";
		} else {
			echo "{$time}:insert fail";
		}
	}

	/*
		@der 每月统计一次（生成月表）
	*/
	public function createMonth(){
		
		if (date('d') == '01') {
			//获取上个月的月份
			$prevMonth  = sprintf('%02d' , date('m')-1);
			//上个月的天数总和
	    	$countDay  = date( 't',strtotime(date('Y-').$prevMonth) );
			$table_name = date('Y_').$prevMonth;
			$year = date('Y');$table = "kz_{$year}_{$prevMonth}_".sprintf('%02d' , $i);
			
			/*删除上个月的天表*/
			for($i = 1; $i <= $countDay ; $i++) {
				$table = "kz_{$year}_{$prevMonth}_}".sprintf('%02d' , $i);
				M()->execute("DROP TABLE {$table}");
			}	

		} else {
			$countDay   = sprintf('%02d' , date('d')-1);
			$table_name = date('Y_m');
		}
		
		$sql = "CREATE TABLE IF NOT EXISTS kz_{$table_name}(`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`id`), `server_name` VARCHAR(12) NOT NULL COMMENT '服务器名称' , `cpu` SMALLINT(3) UNSIGNED NOT NULL COMMENT 'CPU使用率' , `lan_rx_rate` VARCHAR(12) NOT NULL COMMENT '内网接收速度，字节/秒' , `lan_tx_rate` VARCHAR(12) NOT NULL COMMENT '内网发送速度，字节/秒' , `wan_rx_rate` VARCHAR(12) NOT NULL COMMENT '外网接收速度，字节/秒' , `wan_tx_rate` VARCHAR(12) NOT NULL COMMENT '外网发送速度，字节/秒' , `time` INT(10) NOT NULL COMMENT '生成时间' , INDEX `server_name_time` (`server_name`, `time`)) ENGINE = MyISAM;";
		M()->execute($sql);
		
		$add = array();
		//循环统计
		$db    = M("{$table_name}_{$countDay}");

		$field = "server_name,AVG(cpu) AS cpu,AVG(lan_rx_rate) AS lan_rx_rate,AVG(lan_tx_rate) AS lan_tx_rate,AVG(wan_rx_rate) AS wan_rx_rate,AVG(wan_tx_rate) AS wan_tx_rate";
		$data  = $db->field($field)->group('server_name')->select();
		if (!$data){
			return false;
		} 
		$time = strtotime('today');
		foreach ($data as $key=>$v) {
			$data[$key]['time'] = $time;
		}
		$add = array_merge($add,$data);
		
		M($table_name)->addAll($add);
	}

	/*
		@der 每月统计一次（生成月表）
	*/
	// public function createMonth(){
	// 	if (date('d') == '01') {
	// 		echo 111;die;
	// 	}
	// 	//获取上个月的月份
	// 	$prevMonth = sprintf('%02d' , date('m')-1);
	// 	//创建数据表
	// 	$table_name = date('Y_').$prevMonth;
	// 	$sql = "CREATE TABLE IF NOT EXISTS kz_{$table_name}(`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`id`), `server_name` VARCHAR(12) NOT NULL COMMENT '服务器名称' , `cpu` SMALLINT(3) UNSIGNED NOT NULL COMMENT 'CPU使用率' , `lan_rx_rate` VARCHAR(12) NOT NULL COMMENT '内网接收速度，字节/秒' , `lan_tx_rate` VARCHAR(12) NOT NULL COMMENT '内网发送速度，字节/秒' , `wan_rx_rate` VARCHAR(12) NOT NULL COMMENT '外网接收速度，字节/秒' , `wan_tx_rate` VARCHAR(12) NOT NULL COMMENT '外网发送速度，字节/秒' ,`day` SMALLINT(2) UNSIGNED NOT NULL COMMENT '生成时间（天）' , INDEX `server_name_time` (`server_name`, `day`)) ENGINE = MyISAM;";
	// 	M()->execute($sql);
		
	// 	//上个月的天数总和
	// 	$countDay = date( 't',strtotime(date('Y-').$prevMonth) );
	// 	$add = array();
	// 	//循环统计
	// 	for ($i = 31; $i <= $countDay; $i++) {
	// 		$db    = M("{$table_name}_{$i}");

	// 		$field = "server_name,AVG(cpu) AS cpu,AVG(lan_rx_rate) AS lan_rx_rate,AVG(lan_tx_rate) AS lan_tx_rate,AVG(wan_rx_rate) AS wan_rx_rate,AVG(wan_tx_rate) AS wan_tx_rate";
	// 		$data  = $db->field($field)->group('server_name')->select();
	// 		if (!$data){
	// 			continue;
	// 		} 
	// 		foreach ($data as $key=>$v) {
	// 			$data[$key]['day'] = $i;
	// 		}
	// 		$add = array_merge($add,$data);
	// 	}
		
	// 	M($table_name)->addAll($add);
	// }
}