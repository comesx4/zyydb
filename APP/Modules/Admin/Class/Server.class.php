<?php
/*导入xml类*/
import('@.Class.Xml');
/*socket处理类*/
import('@.Class.Socket');
//导入工具类
import('@.Class.Tool');
Class Server{
	//数据库查询编码
	const CHARSET	= 'utf8';
	//云服务器的表名称
	const DB 		= 'Yunji_server';
	//系统盘
	const XVDA		= 'vda';
	//系统盘大小M
	const XVDASIZE	= '20480';
	//系统盘大小M
	const WINXVDASIZE= '40960';
	//存储盘
	const XVDD		= 'vdb';
	//存储池
	const POOL		= 'cloud';
	//G转换为M的单位
	const GTOM		= '1024';
	//linux镜像默认端口
	const LINUXPORT = '22';
	//windows镜像默认端口
	const WINPORT   = '3389';

	/*
		@der 创建云服务器
		@param array $data
		@return mixed
	*/
	public static function createServer($data){
		
		//实例表 
		$db = M('Living');
		/*如果不存在服务器名称则随机生成*/
		if (!$data['name']) {
			$data['name'] = Tool::randStr(5,10);
		}
		if(strlen($data['name'])>10){
			return Tool::output('服务器名称超过10位，请控制在10位以内！',false,442);
		}

		if(strpos($data['name'],"_") !== false){
			return Tool::output('服务器名称不能含有下划线_！',false,442);
		}

		//验证云服务器名称是否存在
		$where = array('gid' => C('yunecs'), 'name' => $data['name']);
		$namedb = $db->where($where)->count();
		if($namedb > 0){
			return Tool::output('服务器名称已经存在，请换一个！',false,442);
		}

		/*如果用户没有输入密码则随机生成密码*/
		if(!$data['password']){
			$data['password'] = Tool::randStr(8);
		} elseif(preg_match('/^[a-z,1-9,A-Z]{1}[a-z,0-9,A-Z]{1,7}$/is',$data['password']) ){
			return Tool::output('密码最长8个字符，仅限于字母和数字！',false,400);
		}

		if(!$data['vncpassword']){
			$data['vncpassword'] = Tool::randStr(8);
		} elseif(preg_match('/^[a-z,1-9,A-Z]{1}[a-z,0-9,A-Z]{1,7}$/is',$data['vncpassword']) ){
			return Tool::output('vnc密码最长8个字符，仅限于字母和数字！',false,400);
		}

		if(!$data['wan_upload_bandwidth']){
			return Tool::output('云服务器外网上行带宽不能为空！',false,484);
		}

		if(!$data['lan_download_bandwidth']){
			$data['lan_download_bandwidth']=100;
		}

		if(!$data['lan_upload_bandwidth']){
			$data['lan_upload_bandwidth']=100;
		}

		if(!$data['wan_download_bandwidth']){
			if($data['wan_upload_bandwidth']<5){
				$data['wan_download_bandwidth']=5;
			}else{
				$data['wan_download_bandwidth']=$data['wan_upload_bandwidth'];
			}
		}

		if(!$data['iops']){
			$data['iops']='1000';
		}

		if(!$data['io']){
			$data['io']='100';
		}

		//获取母服务器信息		
		$addsql=$sql='';
		if($data['line_code']){
				//根据IP线路标识且类型为云服务器或公用的找出开启状态线路下的所有开启状态的网关 id 和 机房区域ID
				$gatewaydb	= M()->query("SELECT g.`id`,g.`region_id`
								 FROM `ali_ip_line` l LEFT JOIN `ali_ip_gateway` g on l.`id`=g.`line_id` where g.`status`=1 and l.`status`=1 and l.`line_type` in('2','3') and l.`line_code`='{$data['line_code']}'");

				if(!$gatewaydb){
					return Tool::output('此线路下没有可用的IP了,请稍后再试！',false,478);
				}

				foreach($gatewaydb as $key=>$val){
					$in_ip_gateway_id[]='"'.$val['id'].'"';
					$in_region_id[]='"'.$val['region_id'].'"';
				}
				//网关ID
				$in_ip_gateway_id && $addsql=' AND i.`ip_gateway_id` IN('.implode(',',$in_ip_gateway_id).')';
				//区域ID
				$in_region_id && $sql=' AND r.`id` IN('.implode(',',$in_region_id).')';				
			

		}elseif($data['region_code']){	
			$sql=' and r.`region_code`="'.$data['region_code'].'"';
		}

		/*===============母服务器ID================*/
		if($data['mu_server_id']){
			$sql=' and m.`id`="'.$data['mu_server_id'].'"';
		}

		$mu_server = M()->query('SELECT m.`id`,m.`server_ip`,m.`server_port`,m.`secret_key`,m.`region_id`,r.`lan_netmask`,r.`lan_gateway`,r.`wan_netmask`,r.`wan_gateway`
							   FROM `ali_mu_server` m LEFT JOIN `ali_mu_region` r ON m.`region_id`=r.`id`
							   where m.`server_can_sum`-m.`server_sum`>0 and m.`status`="1" AND r.`status`="1" '.$sql.' ORDER BY m.`server_time` asc')['0'];

		if(!$mu_server) {
			return Tool::output('获取母服务器信息失败！',false,443);
		}
		/*========网关ID=============*/
		if($data['ip_gateway_id']){
			$addsql =' AND i.`ip_gateway_id`="'.$data['ip_gateway_id'].'"';
		}
		
		//获取镜像信息 disk_image_code磁盘对应镜像标识  快照标识   镜像标识 都从这里取出来（原始）
		$image_id= M()->query('SELECT i.`id`,i.`default_port`,i.`default_size`,i.`type`,o.name AS `os`,i.`image_code`,i.`image_snap_code`,i.`image_pool`,i.image_pool AS `pool`,i.image_snap_code AS `snap_code`
							   FROM `ali_mirroring` i LEFT JOIN `ali_os` o ON i.oid = o.id 
							   where i.`status`=1 and i.`id`="'.$data['image_id'].'"')['0'];
	
		if(!$image_id) {
			return Tool::output('获取镜像id失败！'.$data['image_id'],false,446);
		}
		$image_id['disk_image_code'] = $image_id['image_code'];

		//锁表
		M()->execute("LOCK TABLES `ali_mu_ip` READ,`ali_ip_gateway` READ,`ali_mu_ip` WRITE,`ali_mu_server` WRITE");

		//获取内网ip
		$lan_ip = M()->query('SELECT id,ip,mac
							   from `ali_mu_ip`
							   where status="0" AND type="lan" AND `region_id`='.$mu_server['region_id'])['0'];

		if(!$lan_ip){
			return Tool::output('内网ip不足！',false,444);
		}
		
		//获取外网ip
		$wan_ip = M()->query('SELECT i.`id`,i.`ip`,i.`mac`,i.`ip_gateway_id`,g.lan_netmask,g.lan_gateway,g.wan_netmask,g.wan_gateway
							   from `ali_mu_ip` i left join `ali_ip_gateway` g on i.`ip_gateway_id`=g.`id`
							   where i.status="0" AND i.type="wan" '.$addsql.' AND g.`region_id`='.$mu_server['region_id'].'  limit 1')['0'];
		
		
		if(!$wan_ip) {
			return Tool::output('外网ip不足！',false,445);
		}
		
		// if($addsql2){
		// 	//获取外网ip
		// 	$wan_ip2 = M()->query('select i.`id`,i.`ip`,i.`mac`,i.`ip_gateway_id`,g.lan_netmask,g.lan_gateway,g.wan_netmask,g.wan_gateway
		// 						   from `ali_mu_ip` i left join `ali_ip_gateway` g on i.`ip_gateway_id`=g.`id`
		// 						   where i.status="0" AND i.type="wan" '.$addsql2.' AND g.`region_id`='.$mu_server['region_id'].' AND i.`ip`!="'.$wan_ip['ip'].'" order by i.`orderby` limit 1');
			
		// 	if(!$wan_ip2) {
		// 		return Tool::output('外网ip不足！',false,445);
		// 	}
		// 	//更新外网IP信息
		// 	M()->execute("update `ali_mu_ip` set `status`=1,`remark`='".$data['name']."' where `id`='".$wan_ip2['id']."'");
		// }

		//更新内网IP信息
		M()->execute("UPDATE `ali_mu_ip` set `status`=1,`remark`='".$data['name']."' where `id`='".$lan_ip['id']."'");

		//更新外网IP信息
		M()->execute("UPDATE `ali_mu_ip` set `status`=1,`remark`='".$data['name']."' where `id`='".$wan_ip['id']."'");

		M()->execute("UPDATE `ali_mu_server` set `server_sum`=`server_sum`+1,`server_time`='".time()."' where `id`='".$mu_server['id']."'");
		//解锁表
		M()->execute("UNLOCK TABLES");

		if($image_id['default_size']){
			$svdasize = $image_id['default_size'];
		}else{
			$svdasize = self::XVDASIZE;
		}

		$disk_code_xvda	=self::XVDA.$data['name'].((int)$mu_server['id']+10).Tool::randStr(1,1);
		
		$disk_image_code_xvda=$disk_code_xvda.'_image';
		
		$send_str = array('root'=>'CREATEDOMAIN',
		 				  'CREATEDOMAIN'=>array('name'		=>$data['name'],
									  'vcpu'		=>$data['vcpu'],
									  'memory'		=>$data['memory'],
									  'password'	=>$data['password'],
									  'vncpassword'	=>$data['vncpassword'],
									  'interface_lan'=>array('mac'=>$lan_ip['mac'],
															 'download_bandwidth'=>$data['lan_download_bandwidth'],
															 'upload_bandwidth'	 =>$data['lan_upload_bandwidth'],
															 'ip' =>array('ip'=>$lan_ip['ip'],
																		  'netmask'=>$wan_ip['lan_netmask'],
																		  'gateway'=>$wan_ip['lan_gateway'])),
									  'interface_wan'=>array('mac'=>$wan_ip['mac'],
															 'download_bandwidth'=>$data['wan_download_bandwidth'],
															 'upload_bandwidth'=>$data['wan_upload_bandwidth'],
															 'ip' =>array('0'=>array('ip'=>$wan_ip['ip'],
																		  'netmask'=>$wan_ip['wan_netmask'],
																		  'gateway'=>$wan_ip['wan_gateway']))),
									  'disk'=>array('pool'=>self::POOL,'target'=>self::XVDA,'size'=>$svdasize),
									  'os_type'		=>$image_id['os'],
									  'os_rbd_pool'	=>self::POOL,
									  'os_rbd_image'=>$image_id['disk_image_code'],
									  'os_rbd_snap'	=>$image_id['snap_code']));

		// if($wan_ip2){
		// 	$send_str['CREATEDOMAIN']['interface_wan']['ip']['1']=array('ip'	 =>$wan_ip2['ip'],
		// 												'netmask'=>$wan_ip2['wan_netmask'],
		// 												'gateway'=>$wan_ip2['wan_gateway']);
		// 	if($wan_ip2['ip']){
		// 		$wan_ip['ip']=$wan_ip['ip'].",".$wan_ip2['ip'];
		// 		$wan_ip['ip_gateway_id']=$wan_ip['ip_gateway_id'].",".$wan_ip2['ip_gateway_id'];
		// 	}
		// }

		// if($data['disk']){
		// 	$data['disk']=unserialize($data['disk']);

		// 	foreach($data['disk'] as $key=>$val){
		// 		$data['disk'][$key]['disk_code']		= $val['target'].Tool::generateRandomString(8);
		// 		$data['disk'][$key]['disk_image_code']	= $data['disk'][$key]['disk_code']."_image";
		// 		$data['disk'][$key]['size']				= $val['size']*self::GTOM;
		// 		if($val['target']){
		// 			$send_str['CREATEDOMAIN']['disk'][$key+1]		= array('pool'	=>self::POOL,
		// 															'image'	=>$data['disk'][$key]['disk_image_code'],
		// 															'target'=>$val['target'],
		// 															'size'	=>$data['disk'][$key]['size']);
		// 			if($data['iops']){
		// 				$send_str['CREATEDOMAIN']['disk'][$key+1]['iops']=$data['iops'];
		// 			}
		// 			if($data['io']){
		// 				$send_str['CREATEDOMAIN']['disk'][$key+1]['io']=$data['io'];
		// 			}
		// 		}
		// 	}
		// }elseif($data['size']){
		// 	$data['size']=$data['size']*self::GTOM;
		// 	$disk_code_xvdd	=self::XVDD.$data['name'].((int)$mu_server['id']+10).Tool::generateRandomString(1,1);
		// 	$disk_image_code_xvdd=$disk_code_xvdd.'_image';
		// 	$send_str['CREATEDOMAIN']['disk']['1']=array('pool'=>self::POOL,'image'=>$disk_image_code_xvdd,'target'=>self::XVDD,'size'=>$data['size']);
		// 	if($data['iops']){
		// 		$send_str['CREATEDOMAIN']['disk']['1']['iops']=$data['iops'];
		// 	}
		// 	if($data['io']){
		// 		$send_str['CREATEDOMAIN']['disk']['1']['io']=$data['io'];
		// 	}
		// }

		if($image_id['type']==3){//云虚拟主机
			$send_str['CREATEDOMAIN']['flag']=1;//不限制IP
		}
		/*发送请求到服务器*/
		//$result = self::sendCommand($arr ,'43.229.108.2' ,'35627' ,'kJ#ieNP38kzyh#Iof');
		if($result['result']!=0) {
			//更新内网IP信息
			if($lan_ip['id']){
				$mysqlCS->execute("UPDATE `ali_mu_ip` set `status`=0,`remark`='".$data['name']." fail' where `id`='".$lan_ip['id']."'");
			}
			//更新外网IP信息
			if($wan_ip['id']){
				$mysqlCS->execute("UPDATE `ali_mu_ip` set `status`=0,`remark`='".$data['name']." fail' where `id`='".$wan_ip['id']."'");
			}
			//更新外网IP信息
			if($wan_ip2['id']){
				$mysqlCS->execute("UPDATE `ali_mu_ip` set `status`=0,`remark`='".$data['name']." fail' where `id`='".$wan_ip2['id']."'");
			}
			return Tool::output('创建云服务器失败',false,240);
		}
		
		$data['password']	 = Tool::encryptPassword($data['password']);
		$data['vncpassword'] = Tool::encryptPassword($data['vncpassword']);
		
		/*直接创建的情况*/
		if (empty($data['server_id'])) {
			$city = M('Mu_region')->where(array('id' => $mu_server['region_id']))->getField('city_id');
			$add = array(
				'city' 					 => $city,
				'mu_server_id'			 => $mu_server['id'],
				'ip_gateway_id' 		 => $wan_ip['ip_gateway_id'],
				'osid'					 => $image_id['id'],
				'name'					 => $data['name'],
				'cpu'					 => $data['vcpu'],
				'memory'				 => $data['memory'],
				'port'					 => $image_id['default_port'],
				'password'				 => $data['password'],
				'vncpassword'			 => $data['vncpassword'],
				'lan_ip'				 => $lan_ip['ip'],
				'wan_ip'				 => $wan_ip['ip'],
				'lan_upload_bandwidth' 	 => $data['lan_upload_bandwidth'],
				'lan_download_bandwidth' => $data['lan_download_bandwidth'],
				'band'					 => $data['wan_upload_bandwidth'], //外网带宽
				'wan_download_bandwidth' => $data['wan_download_bandwidth'],
				'status'				 => 2, //运行中
				'update_time'			 => $_SERVER['REQUEST_TIME'],
				'time'					 => $_SERVER['REQUEST_TIME'], 
				);
		//添加云服务器
		$cloud_server_id = M(DB)->add($add);

		/*修改的情况*/
		} else {
			$save = array(
				'mu_server_id'			 => $mu_server['id'],
				'ip_gateway_id' 		 => $wan_ip['ip_gateway_id'],
				'name'					 => $data['name'],
				'port'					 => $image_id['default_port'],
				'password'				 => $data['password'],
				'vncpassword'			 => $data['vncpassword'],
				'lan_ip'				 => $lan_ip['ip'],
				'wan_ip'				 => $wan_ip['ip'],
				'lan_upload_bandwidth' 	 => $data['lan_upload_bandwidth'],
				'lan_download_bandwidth' => $data['lan_download_bandwidth'],
				'wan_download_bandwidth' => $data['wan_download_bandwidth'],
				'status'				 => 2, //运行中
				'update_time'			 => $_SERVER['REQUEST_TIME'],
				'time'					 => $_SERVER['REQUEST_TIME'],
				'id'					 => $data['server_id']
				);
			//修改云服务器
			$cloud_server_id = M(DB)->save($save);
			/*修改实例表的到期时间*/
		}

		
			
		if(!$cloud_server_id){
			return Tool::output('插入云服务器信息失败',false,447);
		}

		//更新内网IP信息
		$mysqlCS->execute("update `ali_mu_ip` set `cloud_server_id`='".$cloud_server_id."',`status`=1,`remark`='".$data['name']."' where `id`='".$lan_ip['id']."'");

		//更新外网IP信息
		$mysqlCS->execute("update `ali_mu_ip` set `cloud_server_id`='".$cloud_server_id."',`status`=1,`remark`='".$data['name']."' where `id`='".$wan_ip['id']."'");

		//更新外网IP信息
		if($wan_ip2['id']){
			$mysqlCS->execute("update `ali_mu_ip` set `cloud_server_id`='".$cloud_server_id."',`status`=1,`remark`='".$data['name']."' where `id`='".$wan_ip2['id']."'");
		}
		
		//插入硬盘信息
		if($data['disk']){
			foreach($data['disk'] as $key2=>$val2){
				$sqldisk[]='("'.$mu_server['id'].'","'.$cloud_server_id.'","'.$val2['disk_code'].'","'.$val2['disk_image_code'].'","'.self::POOL.'","'.$val2['target'].'","'.$val2['size'].'","private","'.$data['iops'].'","'.$data['io'].'","attach","'.time().'")';
			}
			$sqlxvdd=','.implode(',',$sqldisk);
		}elseif($data['size']){
			$sqlxvdd=',("'.$mu_server['id'].'","'.$cloud_server_id.'","'.$disk_code_xvdd.'","'.$disk_image_code_xvdd.'","'.self::POOL.'","'.self::XVDD.'","'.$data['size'].'","private","'.$data['iops'].'","'.$data['io'].'","attach","'.time().'")';
		}

		M()->execute('insert into `ali_cloud_disk`(`mu_server_id`,`cloud_server_id`,`disk_code`,`disk_image_code`,`pool`,`target`,`size`,`flag`,`iops`,`io`,`status`,`time`) 
				 values("'.$mu_server['id'].'","'.$cloud_server_id.'","'.$disk_code_xvda.'","'.$disk_image_code_xvda.'","'.self::POOL.'","'.self::XVDA.'","'.$svdasize.'","private","'.$data['iops'].'","'.$data['io'].'","attach","'.time().'")'.$sqlxvdd);
		
		$iplist		= $mysqlCS->getArray('select i.`ip`,l.`line_name`
										from `ali_mu_ip` i left join `ali_ip_gateway` g on i.`ip_gateway_id`=g.`id` left join `ali_ip_line` l on g.`line_id`=l.`id` where i.`cloud_server_id`="'.$cloud_server_id.'" and i.type="wan" and i.status=1',self::CHARSET);
		$wan_ip['ip']=$iplist;

		$list=array('name'	=>$data['name'],
					'lan_ip'=>$lan_ip['ip'],
					'wan_ip'=>$wan_ip['ip'],
					'status'=>'installing');

		return array('message'	=>'创建云服务器成功！',
					 'success'	=>true,
					 'data'		=>$list);

		$arr['root'] = 'CREATEDOMAIN';
		$arr['CREATEDOMAIN'] = [
			'name'          => 'ybc', 			//名字，名字必须唯一，最长12个字符，仅限于字母和数字
			'vpu'           => '16',			// 虚拟cpu个数，最大16
			'memory'        => '1024',			//内存，M为单位
			'passsword'     => '950809ybc',		//初始密码，最长8个字符，仅限于字母和数字
			'vncpassword'   => 'vncpassword',	// vnc密码
			'interface_lan' => [	//内存，M为单位网网卡
				'mac' => 'maca:a:@Z',			//mac地址
				'ip'  => [			
					'ip'        => '192.168.1.197',		//ip
					'netmask'   => '255.255.0.0',		//子网掩码
					'gateway'   => '192.168.1.1',		//网关
					'bandwidth' => '1'				//带宽,m为单位
				]
			],//nei wang
			'interface_wan' => [	//外网网卡
				'mac' => 'maca:a:@Z',				//mac地址
				'ip'  => [
					'ip'        => '192.168.1.197',	//ip
					'netmask'   => '255.255.0.0',	//子网掩码
					'gateway'   => '192.168.1.1',	//网关
					'bandwidth' => '1'				//带宽,m为单位
				]
			],//wai wang
			'disk'  => [ //硬盘
				'pool'   => 'cunchuchi', //存储池，根据配置的不同而不同
				'target' => '',
				'size'   => '' //m为单位
			],
			'flag'         => '',
			'os_type'      => 'os',   //操作系统类型
			'os_rbd_pool'  => 'zbye', //操作系统模板所在的存储池
			'os_rbd_image' => 'asdf', //操作系统模板镜像名        仅限字母，数字和下划线
			'os_rbd_snap'  => 'asdzxg'
		];
		
		// if( $data ) {
		// 	return $data;
		// } else {
		// 	return Socket::getErrorMsg();
		// }

		
	}

	/*
		@der 发送命令到服务器
	*/
	public static function sendCommand(array $send_str ,$server_ip ,$server_port ,$secret_key = 'kJ#ieNP38kzyh#Iof'){

		//如果是数组将它转换成xml
		if (is_array($send_str)) {
			//记录命令名称
			$command_name = $send_str['root'];
			unset($send_str['root']);
			//转换成xml数据
			$send_str 	  = Xml::createXml($send_str);	
		}
	
		//加密 
		$leng_all = strlen($send_str)+strlen(time())+4+1;//1个pack长度+3个空格个数(1个pack长度是4)

		$secret_key = trim($secret_key);

		// 网络字节顺序
		$query = pack("N",$leng_all).Tool::rc4Encrypt(time().' '.$send_str,$secret_key);
		
		/*发送请求*/
		if ( !$document = Socket::send($query ,$leng_all) ) {
			return Tool::output(Socket::getErrorMsg() ,false ,Socket::getCode());
		}

		$logsEndTime = microtime();

		list($msec,$sec)	= explode(' ',$logsStartTime);
		$logsStartTime		= (float)$msec + (float)$sec;
		
		list($msec,$sec)	= explode(' ',$logsEndTime);
		$logsEndTime		= (float)$msec + (float)$sec;
		
		$process_time		= $logsEndTime - $logsStartTime;

		$document = Tool::rc4Decrypt($document,$secret_key);
		
		if ($document) {
			//将xml数据转换成数组
			$xml = Xml::xml_convert($document ,true);

			if ($xml) {
				if ($xml['result'] = 0) {
					$result = Tool::output('ok',true,0,['result' => $xml['result'], 'xml' => $xml ,'document' => $document]);
				} else {
					$result = Tool::output('error！',false,$xml['result'] ,['document' => $document]);
				}
				
			
			} else {
				$result = Tool::output('解析xml数据失败！',false,402 ,['document' => $document]);
			}
		} else {
			$result = Tool::output('未读取到返回XML文档！',false,401 ,['document' => $document]);
		}
		
		$xml['result'] == 0 ? $status = 1 : $status = 0;
		//查找出命令ID
		$command_id = M('Command')->where(array('name' => $command_name))->getField('id');
		
		$add = array(
				 'command_id' => $command_id,
				 'user_id'	  => 1,
				 'client_ip'  => get_client_ip(),
				 'server_ip'  => $server_ip,
				 'secret_key' => $secret_key,
				 'operate'	  => 'class_AMCloudServer.class.php',
				 'content'	  => htmlspecialchars($send_str),
				 'result'	  => htmlspecialchars($document),
				 'status'	  => $status,
				 'process_time' => $process_time,
				 'time'	      => time()
			);

		//插入命令日志
		M('Logs_command')->add($add);
		return $result;
	}

	/*
		@der 禁止new 对象
	*/
	private function __construct(){}
}