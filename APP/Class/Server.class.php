<?php
/*导入xml类*/
import('Class.Xml',APP_PATH);
/*socket处理类*/
import('Class.Socket',APP_PATH);
//导入工具类
import('Class.Tool',APP_PATH);

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
	const POOL		= 'cloud1';
	//G转换为M的单位
	const GTOM		= '1024';
	//linux镜像默认端口
	const LINUXPORT = '22';
	//windows镜像默认端口
	const WINPORT   = '3389';
	//iops
	const IOPS      = '1000';
	//io
	const IO 		= '100';
	/*
		@der 创建云服务器
		@param array $data
		@return mixed
	*/
	public static function createServer($data){
		
		//实例表 
		$db = M('Living');
		
		if(strlen($data['name'])>10){
			return Tool::output('服务器名称超过10位，请控制在10位以内！',false,442);
		}

		if(strpos($data['name'],"_") !== false){
			return Tool::output('服务器名称不能含有下划线_！',false,442);
		}

		/*如果不存在服务器名称则随机生成*/
		if (!$data['name']) {
			$data['name'] = Tool::randServerName();

			//验证云服务器名称是否存在
			$where = array( 'name' => $data['name']);
			$namedb = M(self::DB)->where($where)->count();
			
			if($namedb > 0){
				return Tool::output('服务器名称已经存在，请换一个！',false,442);
			}
		}

		/*如果用户没有输入密码则随机生成密码*/
		if(!$data['password']){
			$data['password'] = Tool::randStr(12);
		} elseif( !preg_match('/^[a-z,0-9,A-Z]{2,12}$/is',$data['password']) ){
			return Tool::output('密码最长12个字符，仅限于字母和数字！',false,400);
		}

		if(!$data['vncpassword']){
			$data['vncpassword'] = Tool::randStr(12);
		} elseif( !preg_match('/^[a-z,0-9,A-Z]{2,12}$/is',$data['vncpassword']) ){
			return Tool::output('vnc密码最长12个字符，仅限于字母和数字！',false,400);
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
			if($data['wan_upload_bandwidth'] < 5){
				$data['wan_download_bandwidth']=5;
			}else{
				$data['wan_download_bandwidth']=$data['wan_upload_bandwidth'];
			}
		}

		if(!$data['iops']){
			$data['iops']= self::IOPS;
		}

		if(!$data['io']){
			$data['io']= self::IO;
		}

		/*处理内存*/
		$memory = $data['memory'];
		if ($data['memory'] == 0) {	
			$memory		    = 0;
			$data['memory'] = '512';
		} else {
			$memory 		= $data['memory'];
			$data['memory'] = $data['memory'] * self::GTOM;
		}

		if (!$data['cpu']) {
			$data['cpu'] = 1;
		}

		if (!$data['remark']) {
			$data['remark'] = '';
		}

		//获取母服务器信息		
		$addsql=$sql='';
		//存在地域(一般是自动创建才有)
		if($data['region_id']){
				$where = "l.`region_id` IN(".implode(',', $data['region_id']).")";
				//根据IP线路标识且类型为云服务器或公用的找出开启状态线路下的所有开启状态的网关 id 和 机房区域ID
				$gatewaydb	= M()->query("SELECT g.`id`,g.`region_id`
								 FROM `kz_ip_line` l LEFT JOIN `kz_ip_gateway` g on l.`id`=g.`line_id` where g.`status`=1 and l.`status`=1 and l.`line_type` in('2','3') and {$where}");

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
		
		//指定了IP线路
		} elseif ($data['line_id']) {	
			//查找出该IP线路的的网关
			$field = 'id';
			$where = array('line_id' => $data['line_id']);
			$gateway_id = M('Ip_gateway')->field($field)->where($where)->select();
			$gateway_id = implode(',' , peatarr($gateway_id,'id'));

			$addsql =" AND i.`ip_gateway_id` IN({$gateway_id})";
		}
		
		/*===============母服务器ID================*/
		if($data['mu_server_id']){
			$sql=' AND m.`id`="'.$data['mu_server_id'].'"';
		}

		/*如果有可建云服务器，那么该区域下就一定有可用IP*/
		$mu_server = M()->query('SELECT m.`id`,m.`server_ip`,m.`server_port`,m.`secret_key`,m.`region_id`
							   FROM `kz_mu_server` m LEFT JOIN `kz_mu_region` r ON m.`region_id`=r.`id`
							   where m.`server_can_sum`-m.`server_sum`>0 and m.`status`="1" AND r.`status`="1" '.$sql.' ORDER BY m.`server_time` asc')['0'];
		
		if(!$mu_server) {
			return Tool::output('获取母服务器信息失败！',false,443);
		}
		
		/*========网关ID=============*/
		if($data['ip_gateway_id']){
			$addsql =' AND i.`ip_gateway_id`="'.$data['ip_gateway_id'].'"';
		}
		
		//获取镜像信息 disk_image_code磁盘对应镜像标识  快照标识   镜像标识 都从这里取出来（原始）
		$image_id= M()->query('SELECT i.`id`,i.`default_port`,i.`default_size`,i.`type`,o.os_code AS `os`,i.`image_code`,i.`image_snap_code`,i.`image_pool`,i.image_pool AS `pool`,i.image_snap_code AS `snap_code`
							   FROM `kz_mirroring` i LEFT JOIN `kz_os` o ON i.oid = o.id 
							   where i.`status`=1 and i.`id`="'.$data['image_id'].'"')['0'];
		
		if(!$image_id) {
			return Tool::output('获取镜像id失败！'.$data['image_id'],false,446);
		}
		$image_id['disk_image_code'] = $image_id['image_code'];
		

		//锁表
		M()->execute("LOCK TABLES `kz_mu_ip` READ,`kz_ip_gateway` READ,`kz_mu_ip` WRITE,`kz_mu_server` WRITE");

		//获取内网ip
		$lan_ip = M()->query('SELECT id,ip,mac
							   from `kz_mu_ip`
							   where status="0" AND type="lan" AND `region_id`='.$mu_server['region_id'].' LIMIT 1')['0'];

		if(!$lan_ip){
			return Tool::output('内网ip不足！',false,444);
		}
		
		//获取外网ip
		$wan_ip = M()->query('SELECT i.`id`,i.`ip`,i.`mac`,i.`ip_gateway_id`,g.lan_netmask,g.lan_gateway,g.wan_netmask,g.wan_gateway
							   from `kz_mu_ip` i left join `kz_ip_gateway` g on i.`ip_gateway_id`=g.`id`
							   where i.status="0" AND i.type="wan" '.$addsql.' AND g.`region_id`='.$mu_server['region_id'].'  limit 1')['0'];
		
		if(!$wan_ip) {
			return Tool::output('外网ip不足！',false,445);
		}

		/*查找出可用的存储池*/
		$pool = M('Cloud_pool')->where(array('status' => 1))->getField('code');

		if (!$pool) {
			return Tool::output('存储池不足！',false, 446);
		}
		
		// if($addsql2){
		// 	//获取外网ip
		// 	$wan_ip2 = M()->query('select i.`id`,i.`ip`,i.`mac`,i.`ip_gateway_id`,g.lan_netmask,g.lan_gateway,g.wan_netmask,g.wan_gateway
		// 						   from `kz_mu_ip` i left join `kz_ip_gateway` g on i.`ip_gateway_id`=g.`id`
		// 						   where i.status="0" AND i.type="wan" '.$addsql2.' AND g.`region_id`='.$mu_server['region_id'].' AND i.`ip`!="'.$wan_ip['ip'].'" order by i.`orderby` limit 1');
			
		// 	if(!$wan_ip2) {
		// 		return Tool::output('外网ip不足！',false,445);
		// 	}
		// 	//更新外网IP信息
		// 	M()->execute("update `kz_mu_ip` set `status`=1,`remark`='".$data['name']."' where `id`='".$wan_ip2['id']."'");
		// }

		//更新内网IP信息
		M()->execute("UPDATE `kz_mu_ip` set `status`=1,`remark`='".$data['name']."' where `id`='".$lan_ip['id']."'");

		//更新外网IP信息
		M()->execute("UPDATE `kz_mu_ip` set `status`=1,`remark`='".$data['name']."' where `id`='".$wan_ip['id']."'");
		//母服务器已建云服务器数量+1
		M()->execute("UPDATE `kz_mu_server` set `server_sum`=`server_sum`+1,`server_time`='".time()."' where `id`='".$mu_server['id']."'");
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
									  'vcpu'		=>$data['cpu'],
									  'memory'		=>$data['memory'],
									  'password'	=>$data['password'],
									  'vncpassword'	=>$data['vncpassword'],
									  'interface_lan'=>array('mac'=>$lan_ip['mac'],
															 'download_bandwidth'=>$data['lan_download_bandwidth'],
															 'upload_bandwidth'	 => $data['lan_upload_bandwidth'],
															 'ip' =>array('ip'=>$lan_ip['ip'],
																		  'netmask'		 => $wan_ip['lan_netmask'],
																		  'gateway'		 => $wan_ip['lan_gateway'],
																		  ),
															 			 
															 ),
									  'interface_wan'=>array('mac'=>$wan_ip['mac'],
															 'download_bandwidth'=>$data['wan_download_bandwidth'],
															 'upload_bandwidth' => $data['wan_upload_bandwidth'],
															 'ip' =>array('0'=>array('ip'=>$wan_ip['ip'],
																		  'netmask'=>$wan_ip['wan_netmask'],
																		  'gateway'=>$wan_ip['wan_gateway'],
																		  
																		  		)
															 		)
															 ),
									  'disk'=> array( 0 => array(
									  							'pool'  =>$pool,
									  							'image' => $disk_image_code_xvda ,
									  							'target'=>self::XVDA,
									  							'size'  =>$svdasize ,
									  							'iops'  => $data['iops'] ,
									  							'io'    => $data['io']
									  							) 
									  						),
									 
									  'os_type'		=>$image_id['os'],
									  'os_rbd_pool'	=>$pool,
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
		// 			$send_str['CREATEDOMAIN']['disk'][$key+1]		= array('pool'	=>$pool,
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
		// 	$send_str['CREATEDOMAIN']['disk']['1']=array('pool'=>$pool,'image'=>$disk_image_code_xvdd,'target'=>self::XVDD,'size'=>$data['size']);
		// 	if($data['iops']){
		// 		$send_str['CREATEDOMAIN']['disk']['1']['iops']=$data['iops'];
		// 	}
		// 	if($data['io']){
		// 		$send_str['CREATEDOMAIN']['disk']['1']['io']=$data['io'];
		// 	}
		// }
		
		//数据盘
		$data_add 	   = array();
		$data_add['0'] = array();
		//如果存在数据盘
		if(!empty($data['disk'])){
			$number = 'bcdefg';
			foreach ($data['disk'] as $key => $v) {
				$target = 'vd'.$number{$key};
				$disk_code_xvdd	= $target.$data['name'].((int)$mu_server['id']+10).Tool::randStr(1,1);
				$disk_image_code_xvdd=$disk_code_xvdd.'_image';
				$send_str['CREATEDOMAIN']['disk'][$key + 1]=array('pool'=>$pool,'image'=>$disk_image_code_xvdd,'target' => $target,'size'=>$v * self::GTOM);
				if($data['iops']){
					$send_str['CREATEDOMAIN']['disk'][$key + 1]['iops']=$data['iops'];
				}
				if($data['io']){
					$send_str['CREATEDOMAIN']['disk'][$key + 1]['io']=$data['io'];
				}
				$data_add[] = array(
						'uid'			  => $data['uid'],
						'mu_server_id'	  => $mu_server['id'],
					    'cloud_server_id' => '',
						'disk_code'	      => $disk_code_xvdd,
						'disk_image_code' => $disk_image_code_xvdd,
						'pool'			  => $pool,
						'target'		  => $target,
						'size'			  => $v * self::GTOM,
						'flag'			  => 'private',
						'iops'			  => $data['iops'],
						'io'			  => $data['io'],
						'status'		  => 'attach',
						'time'			  => $_SERVER['REQUEST_TIME']
					);
			}
        }

		// if($image_id['type']==3){//云虚拟主机
		// 	$send_str['CREATEDOMAIN']['flag']=1;//不限制IP
		// }

		/*发送请求到服务器*/
		$result = self::sendCommand($send_str ,$mu_server['server_ip'] ,$mu_server['server_port'] ,$mu_server['secret_key']);
		//$result = self::sendCommand($send_str ,'10.2.0.18' ,'35627' ,'Qzk#8vjA#390Pnv82');

		
		if($result['code']!=0) {
			//更新内网IP信息
			if($lan_ip['id']){
				M()->execute("UPDATE `kz_mu_ip` set `status`=0,`remark`='".$data['name']." fail' where `id`='".$lan_ip['id']."'");
			}
			//更新外网IP信息
			if($wan_ip['id']){
				M()->execute("UPDATE `kz_mu_ip` set `status`=0,`remark`='".$data['name']." fail' where `id`='".$wan_ip['id']."'");
			}
			//更新外网IP信息
			if($wan_ip2['id']){
				M()->execute("UPDATE `kz_mu_ip` set `status`=0,`remark`='".$data['name']." fail' where `id`='".$wan_ip2['id']."'");
			}
			//母服务器已建云服务器数量-1
			M()->execute("UPDATE `kz_mu_server` set `server_sum`=`server_sum`-1,`server_time`='".time()."' where `id`='".$mu_server['id']."'");

			return Tool::output('创建云服务器失败 ：'.$result['error'],false,240);
		}
		
		$data['password']	 = Tool::encryptPassword($data['password']);
		$data['vncpassword'] = Tool::encryptPassword($data['vncpassword']);
		
		/*直接创建的情况*/
		if (empty($data['server_id'])) {
			$city = M('Mu_region')->where(array('id' => $mu_server['region_id']))->getField('city_id');
			$add = array(
				'city' 					 => $city,
				'region_id'				 => $mu_server['region_id'],
				'mu_server_id'			 => $mu_server['id'],
				'ip_gateway_id' 		 => $wan_ip['ip_gateway_id'],
				'osid'					 => $image_id['id'],
				'name'					 => $data['name'],
				'cpu'					 => $data['cpu'],
				'memory'				 => $memory,
				'disk'					 => implode(',' , $data['disk'] ),
				'port'					 => $image_id['default_port'],
				'vncport'	 			 => $image_id['default_port'],
				'password'				 => $data['password'],
				'vncpassword'			 => $data['vncpassword'],
				'lan_ip'				 => $lan_ip['ip'],
				'wan_ip'				 => $wan_ip['ip'],	
				'lan_upload_bandwidth' 	 => $data['lan_upload_bandwidth'],
				'lan_download_bandwidth' => $data['lan_download_bandwidth'],
				'band'					 => $data['wan_upload_bandwidth'], //外网带宽
				'wan_download_bandwidth' => $data['wan_download_bandwidth'],
				//'status'				 => 2, //运行中
				// 'time'					 => $_SERVER['REQUEST_TIME'], 
				'remark'				 => $data['remark'],
				);
		//添加云服务器
		$cloud_server_id = M(self::DB)->add($add);
		
		//实例表数据添加
		$add = array(			
			'tid'		=> $cloud_server_id,
			'uid'		=> $data['uid'],
			'cid'		=> $data['cid'],
			'gid'	    => $data['gid'],
			'start'		=> $_SERVER['REQUEST_TIME'],
			'end'		=> strtotime("+{$data['end']} month"),
			'status'	=> 2,//创建成功
			'remark'    => $data['remark'],
			);
		
		M('Living')->add($add);
		
		/*修改的情况*/
		} else {
			$save = array(
				'region_id'				 => $mu_server['region_id'],
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
				//'status'				 => 2, //运行中
				// 'time'					 => $_SERVER['REQUEST_TIME'],
				'remark'				 => $data['remark'],
				'id'					 => $data['server_id']
				);
			//修改云服务器
			if (M(self::DB)->save($save)) {
				$cloud_server_id = $data['server_id'];
				/*修改实例表的状态*/
				M('Living')->where(array('tid' => $data['server_id']))->setField(array('status'=>2));

				//生成任务日志
				Tool::createTaskLogs(array('type' => 1,'info' => $data['name']));
			} else {
				$cloud_server_id = null;
			}
		}

		if(!$cloud_server_id){
			//生成任务日志
			Tool::createTaskLogs(array('status' => 0,'type' => 1,'info' => $data['name']));
			return Tool::output('插入云服务器信息失败',false,447);
		}

		//更新内网IP信息
		M()->execute("UPDATE `kz_mu_ip` set `cloud_server_id`='".$cloud_server_id."',`status`=1,`remark`='".$data['name']."' where `id`='".$lan_ip['id']."'");

		//更新外网IP信息
		M()->execute("UPDATE `kz_mu_ip` set `cloud_server_id`='".$cloud_server_id."',`status`=1,`remark`='".$data['name']."' where `id`='".$wan_ip['id']."'");

		//更新外网IP信息
		if($wan_ip2['id']){
			M()->execute("UPDATE `kz_mu_ip` set `cloud_server_id`='".$cloud_server_id."',`status`=1,`remark`='".$data['name']."' where `id`='".$wan_ip2['id']."'");
		}
		
		//插入硬盘信息
		if(!empty($data['disk'])){
			foreach ($data_add as $key => $v) {
				$data_add[ $key ]['cloud_server_id'] = $cloud_server_id;
			}
			// //数据盘
			// $sqlxvdd=',("'.$mu_server['id'].'","'.$cloud_server_id.'","'.$disk_code_xvdd.'","'.$disk_image_code_xvdd.'","'.$pool.'","'.self::XVDD.'","'.$data['size']*self::GTOM.'","private","'.$data['iops'].'","'.$data['io'].'","attach","'.time().'")';
		}

		$data_add['0'] = array(
						'uid'			  => $data['uid'],
						'mu_server_id'	  => $mu_server['id'],
					    'cloud_server_id' => $cloud_server_id,
						'disk_code'	      => $disk_code_xvda,
						'disk_image_code' => $disk_image_code_xvda,
						'pool'			  => $pool,
						'target'		  => self::XVDA,
						'size'			  => $svdasize,
						'flag'			  => 'private',
						'iops'			  => $data['iops'],
						'io'			  => $data['io'],
						'status'		  => 'attach',
						'time'			  => $_SERVER['REQUEST_TIME']
					);
		//系统盘
		// M()->execute('INSERT into `kz_cloud_disk`(`uid`,`mu_server_id`,`cloud_server_id`,`disk_code`,`disk_image_code`,`pool`,`target`,`size`,`flag`,`iops`,`io`,`status`,`time`) 
		// 		 values("'.$data['uid'].'","'.$mu_server['id'].'","'.$cloud_server_id.'","'.$disk_code_xvda.'","'.$disk_image_code_xvda.'","'.$pool.'","'.self::XVDA.'","'.$svdasize.'","private","'.$data['iops'].'","'.$data['io'].'","attach","'.time().'")'.$sqlxvdd);
		
		//添加硬盘到数据库
		M('Cloud_disk')->addAll($data_add);
		
		$iplist		= M()->query('select i.`ip`,l.`line_name`
										from `kz_mu_ip` i left join `kz_ip_gateway` g on i.`ip_gateway_id`=g.`id` left join `kz_ip_line` l on g.`line_id`=l.`id` where i.`cloud_server_id`="'.$cloud_server_id.'" and i.type="wan" and i.status=1');
		$wan_ip['ip']=$iplist;

		$list=array('name'	=>$data['name'],
					'lan_ip'=>$lan_ip['ip'],
					'wan_ip'=>$wan_ip['ip'],
					'status'=>'installing');
		
		return Tool::output('创建云服务器成功！',true,0,$list);
	}

	/**
		@der 添加硬盘 (废弃)
		@param array $data 
			   array(name,size,flag)
	*/
	public static function createDisk(array $data){
		if (!$data['name'] || !$data['size'] || !$data['flag'] || $data['size'] > 2000) {
			return Tool::output('参数有误!',false,404);
		}
		if (!$data['remark']){
			$data['remark'] = '';
		}
		$mu_server = M(self::DB)->field('id,mu_server_id')->where(array('name' => $data['name']))->find();
		
		if (!$mu_server) {
			return Tool::output('不存在的服务器name值!',false,404);
		}
		$db = M('Cloud_disk');
		$length = $db->where(array('cloud_server_id' => $mu_server['id']))->count();
		
		if ($length >=26) {
			return Tool::output('该云服务器云磁盘数量已达上限',false,500);
		}
		$str 	  = 'abcdefghijklmnopqrstuvwxyz';
		$send_str = array(
			'root' => 'ATTACHDISK',
			'ATTACHDISK' => array(
				'name'   => $data['name'],
				'pool'   => self::POOL,
				'target' => 'vd'.$str{$length},
				'size'   => $data['size']
				)
			);
		
		/*查找出母服务器的信息*/
		$send = M('Mu_server')->field('server_ip,server_port,secret_key')->where(array('id' => $mu_server['mu_server_id']))->find();

		$result = self::sendCommand($data , $send['server_ip'] , $send['server_port'] , $send['secret_key']);
		if ($result['code'] != 0) {
			return Tool::output('创建云磁盘失败',false,212);
		}
		//如果uid不存在则根据服务器查找出uid
		if (!$data['uid']) {
			$data['uid'] = M('Living')->where(array('tid' => $mu_server['id']))->getField('uid');
		}

		$newdisk_code	= Tool::randStr(12);
		$disk_image_code= $newdisk_code."_image";
		$add = array(
			'mu_server_id'    => $mu_server['mu_server_id'],
			'cloud_server_id' => $mu_server['id'],
			'disk_code'	      => $newdisk_code,
			'disk_image_code' => $disk_image_code,
			'pool'			  => self::POOL,
			'target'		  => $send_str['ATTACHDISK']['target'],
			'size'			  => $send_str['ATTACHDISK']['size'],
			'flag'			  => $data['flag'],
			'status'		  => 'detach',
			'time'			  => $_SERVER['REQUEST_TIME'],
			'uid'			  => $data['uid'],
			'remark'		  => $data['remark']
			);

		if (!$disk_id = $db->add($add)) {
			
			return Tool::output('添加云磁盘失败',false,213);
		}
		$result['disk_id'] = $id; 
		
		return json_encode($result , true);
	}

	/**
		@der 克隆云磁盘
		@param array $data array(
								'name'		=> '云服务器的的名称(要克隆云磁盘的云服务器)'
								'disk_flag' => '磁盘类型 private 或 public',
								'snap_code' => '快照标识'
							);
		克隆
		<CLONEDISK>
			<src_pool></src_pool>
			<dst_pool></dst_pool>
			<src_image></src_image>
			<dst_image></dst_image>
			<snap></snap>            快照名
		</CLONEDISK>
	*/
	public static function cloneDisk(array $data){
		if (!$data['name'] || !$data['disk_flag'] || !$data['snap_code'] ) {
			return Tool::output('缺少参数',false,404);
		}
		/*根据快照标识查找出该快照信息及对应的云磁盘信息*/
		$disk = M()->query('select d.`target`,d.`pool`,d.`size`,d.`disk_image_code`,s.`snap_type`,s.`snap_code`,s.`id`
										from `kz_cloud_snap` s left join `kz_cloud_disk` d on s.`cloud_disk_id`=d.`id` where s.`snap_code`="'.$data['snap_code'].'"')['0'];
		
		if (!$disk) {

			return Tool::output('快照标识有误',false,404);
		}

		$snap = M('Cloud_snap');

		/*获取云服务器和母服务器信息*/
		list($server , $send) = self::get_server_info($data['name']);

		/*如果该快照还没保护，就将它保护起来*/
		if($disk['snap_type']!=3){
			/*
			设置快照保护
			<PROTECTSNAP>
				<pool></pool>
				<image></image>
				<snap></snap>
			</PROTECTSNAP>
			*/
			$send_str	= array('root'=>'PROTECTSNAP',
							'PROTECTSNAP' =>array('pool'		=>$disk['pool'],
												  'image'		=>$disk['disk_image_code'],
												  'snap'		=>$disk['snap_code']));

			$result		= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);
			if($result['code']!=0) {
				return Tool::output('设置快照保护失败！',false,413);
			}else{
				//修改快照类型为保护中
				$setField = array('snap_type' => 3);
				$snap->where(array('id' => $disk['id']))->setField($setField);
			}
		}

		$length = M('Cloud_disk')->where(array('cloud_server_id' => $server['id']))->count();
		
		if ($length >=26) {
			return Tool::output('该云服务器云磁盘数量已达上限',false,500);
		}
		$str 	  = 'abcdefghijklmnopqrstuvwxyz';
		$target   = 'vd'.$str{$length};

		$newdisk_code	= $target.$data['name'].($server['mu_server_id']+10).Tool::randStr(1,1);
		$disk_image_code= $newdisk_code."_image";

		$send_str	= array('root'=>'CLONEDISK',
							'CLONEDISK' => array('src_pool'	=>$disk['pool'],
										  'dst_pool'	=>self::POOL,
										  'src_image'	=>$disk['disk_image_code'],
										  'dst_image'	=>$disk_image_code,
										  'snap'		=>$data['snap_code'])
							);
		$result	= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);
		if($result['code']!=0) {
			return Tool::output('克隆失败！',false,413);
		}else{

			/*将克隆的磁盘挂载
			<ATTACHDISK>
				<name></name>
				<pool></pool>
				<image></image>
				<target></target>
				<size></size>        硬盘大小,M为单位
				<flag></flag>        1表示挂载云磁盘，0或者不填表示私有的
			</ATTACHDISK>
			*/
			
			$send_str	= array('root'=>'ATTACHDISK',
								'ATTACHDISK' =>array('name'	=>$data['name'],
											  'pool'	=>self::POOL,
											  'image'	=>$disk_image_code,
											  'target'	=>$target,
											  'size'	=>$disk['size']));

			$result		= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);
			if($result['code']!=0) {
				return Tool::output('挂载克隆的云磁盘失败！',false,414);
			}else{

				//如果uid不存在则根据服务器查找出uid
				if (!$data['uid']) {
					$data['uid'] = M('Living')->where(array('tid' => $server['id']))->getField('uid');
				}

				/*将磁盘添加到数据库中*/
				$add = array(
					'uid'			  => $data['uid'],	  
					'mu_server_id'    => $server['mu_server_id'],
					'cloud_server_id' => $server['id'],
					'disk_code'		  => $newdisk_code,
					'disk_image_code' => $disk_image_code,
					'pool'			  => $disk['pool'],
					'target'		  => $target,
					'size'			  => $disk['size'],
					'flag'			  => $data['disk_flag'],
					'status'		  => 'attach',
					'type'			  => 2,
					'time'			  => $_SERVER['REQUEST_TIME']
					);
				if ( !M('Cloud_disk')->add($add) ) {
					
					return Tool::output('创建克隆磁盘失败',false,414);
				}

				$back['disk_code']=$newdisk_code;
				$back['disk_flag']=$data['disk_flag'];
				$back['size']	  =$disk['size']/self::GTOM;
				$back['status']	  ='attach';
			}
		}

		return Tool::output('ok',true,0,$back);
	}

	/**
	 *	@der 创建镜像
	 *	@param array $data  
	 *				array(
	 *					'image_name' => '镜像名称',
	 *					'snap_id'	 => '快照ID'
	 *				);
	 */
	public static function createImage(array $data){
		/*根据快照ID查找出硬盘、快照、镜像、服务器信息*/
		$item = M()->query('SELECT d.`target`,d.`disk_image_code`,d.`pool`,n.`id`,n.`snap_code`,n.`snap_type`,i.`image_code`,i.`image_pool`,os.`os_code` AS `os`,i.`default_size`,i.`default_port`,i.oid,s.`name`,d.mu_server_id
										from `kz_cloud_snap` n left join `kz_cloud_disk` d on n.`cloud_disk_id`=d.`id` left join `kz_yunji_server` s on d.`cloud_server_id`=s.`id` left join `kz_mirroring` i on s.`osid`= i.`id` LEFT JOIN kz_os os ON i.oid = os.id where n.`id`="'.$data['snap_id'].'" and d.`target`="'.self::XVDA.'"')['0'];
	
		if(!$item){
			return Tool::output('该快照不存在或者不是系统盘的快照！',false,243);
		}
		
		//查找出母服务器信息
		$field = 'id,server_ip,server_port,secret_key';
		$send  = M('Mu_server')->field($field)->where(array('id' => $item['mu_server_id']))->find();
		
		$image_code	= Tool::randStr(12);
		!$data['image_name'] && $data['image_name']=$item['os'].Tool::randStr(5);

		// if(strpos($item['os'],'server') != false){
		// 	$default_port=self::WINPORT;
		// 	$os_type='win2k';
		// }else{
		// 	$default_port=self::LINUXPORT;
		// 	$os_type='linux';
		// }

		if($data['default_port']){
			$default_port=$data['default_port'];
		}elseif($item['default_port']){
			$default_port=$item['default_port'];
		}

		if(!$item['default_size']){
			$item['default_size'] = self::XVDASIZE;
		}
		
		// !$data['type'] && $data['type']=2;
		// $data['status']=2;
		
		/*如果是没有被保护的快照*/
		if($item['snap_type']!=3){
			$send_str	= array('root'=>'PROTECTSNAP',
								'PROTECTSNAP' =>array('pool'	=>$item['image_pool'],
											  'image'	=>$item['disk_image_code'],
											  'snap'	=>$item['snap_code']));
			
			$result	= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);

			if ($result['code'] != 0) {
				return Tool::output('设置快照保护失败！',false,413);
			}

			//修改快照类型为保护中
			$setField = array('snap_type' => 3);
			M('Cloud_snap')->where(array('id' => $data['snap_id']))->setField($setField);
		}

		// 将镜像添加到数据库
		$add = array(
				'cloud_snap_id'  => $data['snap_id'],
				'mu_server_id'   => $send['id'],
				'name'	 	     => $data['image_name'],
				'image_code'	 => $image_code,
				'image_pool'	 => $item['image_pool'],
				'image_snap_code'=> $item['snap_code'],
				'oid' 			 => $item['oid'],
				'default_port'	 => $default_port,
			    'default_size'	 => $item['default_size'],
				'type'		     => 2,				
				'is_out'		 => 0,	//未脱离
				'remark'		 => $data['remark'],
				'time'			 => $_SERVER['REQUEST_TIME'],
				'uid'		     => $data['uid']
			);

		$image_id = M('Mirroring')->add($add);

		if (!$image_id) {
			//生成任务日志
			Tool::createTaskLogs(array('status' => 0,'type' => 21,'info' => "add,image_code : {$image_code}"));
			return Tool::output('创建镜像失败！',false,244);
		}
			
		/*克隆*/
		$send_str	= array('root'=>'CLONEDISK',
							'CLONEDISK' =>array('src_pool'	=>$item['pool'],
										  'dst_pool'	=>$item['image_pool'],
										  'src_image'	=>$item['disk_image_code'],
										  'dst_image'	=>$image_code,
										  'snap'		=>$item['snap_code']));
		
		$result	= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);

		if ($result['code'] != 0 ) {
			
			M('Mirroring')->where(array('id' => $image_id))->delete();
			//生成任务日志
			Tool::createTaskLogs(array('status' => 0,'type' => 21,'info' => "CLONEDISK,image_code : {$image_code}"));

			return Tool::output('克隆失败',false,414);
		}

		/*脱离上层关系*/
		$send_str	= array('root'=>'FLATTENDISK',
							'FLATTENDISK' =>array('task_id'	=>$image_id.'FLATTENDISK',
										  'pool'	=>$item['image_pool'],
										  'image'	=>$image_code)
							);
			
		$result	= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);
		
		if($result['code'] != 0) {
			$save = array(
				'image_action' => 'FLATTENDISK',
				'is_out'	   => 0,
				'status'	   => 2, //发送脱离请求失败了
				);
			M('Mirroring')->where(array('id' => $image_id))->save($save);

			//生成任务日志
			Tool::createTaskLogs(array('status' => 0,'type' => 21,'info' => "FLATTENDISK,image_code : {$image_code}"));
			
			return Tool::output('创建镜像失败！',false,244);
		}
		
		//生成任务日志
		Tool::createTaskLogs(array('status' => 1,'type' => 21,'info' => "image_id : {$image_code}"));

		//将镜像添加进计划任务
		$add = array(
				'mirroring_id'  => $image_id,
				'time'			=> $_SERVER['REQUEST_TIME']
			);
		M('Mirroring_plan')->add($add);
		return Tool::output('ok',true,0,array('image_code' => $image_code));
	}

	/**
	 * 	@der 查询镜像状态(判断克隆盘是否和上层关系脱离，如果脱离了就创建快照并保护起来)
	 *	@param int $image_id  镜像ID
	 */
	public function checkImage($image_id){

		$item = M()->query('SELECT i.`id`,i.`image_code`,i.`image_action`,i.update_time,i.`image_pool`,i.cloud_snap_id,i.`image_snap_code`,i.`status`,m.`server_ip`,m.`server_port`,m.`secret_key` from `kz_mirroring` i left join `kz_mu_server` m on i.`mu_server_id`=m.`id` where i.`id`="'.$image_id.'"')['0'];
	
		if (!$item) {
			return Tool::output('您查询的镜像不存在！',false,243);
		}

		$db_image = M('Mirroring');
		$db_snap  = M('Cloud_snap');
		$db_plan  = M('Mirroring_plan');
		$where_image = array('id' =>$image_id);

		/*刚发送完脱离关系请求*/
		if ($item['status'] == 0) {
			//查询快照进度
			$send_str = array(
					'root' => 'QUERYSNAPTASK',
					'QUERYSNAPTASK' => array(
							'task_id' => "{$item['id']}FLATTENDISK"
						)
				);

			$result	= self::sendCommand($send_str,$item['server_ip'],$item['server_port'],$item['secret_key']);
			
			if ($result['code'] != 0) {
				return Tool::output('查询进度失败！',false,413);
			}
			
			//正在解除中
			if ($result['result']['status'] == 0) {
				return Tool::output('正在解除中！',false,413);
			//解除失败
			} elseif($result['result']['status'] == 2) {
				$save = array('status' => 5 ,'image_action' => 'QUERYSNAPTASK');
				$db_image->where($where_image)->save($save);
			}

			//修改为：脱离成功!
			$save = array('is_out' => 1);
			$db_image->where($where_image)->save($save);

			//脱离关系成功，查找出之前的快照将其解除保护
			$snap = M()->query('SELECT d.`disk_image_code`,d.`pool`,s.`id`,s.`snap_code` from `kz_cloud_disk` d left join `kz_cloud_snap` s on d.`id`=s.`cloud_disk_id` where s.`id`="'.$item['cloud_snap_id'].'" and s.`snap_type`=3')['0'];

			if ($snap) {
				//解除保护
				$send_str	= array('root'	=>'UNPROTECTSNAP',
									'UNPROTECTSNAP'	=>array('pool'	=>$snap['pool'],
													'image'	=>$snap['disk_image_code'],
													'snap'	=>$snap['snap_code'])
									);

				$result		= self::sendCommand($send_str,$item['server_ip'],$item['server_port'],$item['secret_key']);
				if ($result['code'] != 0) {
					return Tool::output('取消保护失败！',false,413);
				}

				$db_snap->where(array('id' => $snap['id']))->setField(array('snap_type' => 1));
			}

			//创建快照(这个快照保护起来就是个镜像了，所以是不存到快照表的)
			$timestamp	= $_SERVER['REQUEST_TIME'];
			$send_str	= array('root'=>'CREATESNAP',
								'CREATESNAP' =>array('task_id'	=> $item['id'].$timestamp.'CREATESNAP',
											  'pool'	=>$item['image_pool'],
											  'image'	=>$item['image_code'],
											  'snap'	=>$item['image_snap_code']));
			
			$result	= self::sendCommand($send_str,$item['server_ip'],$item['server_port'],$item['secret_key']);

			if ($result['code'] != 0) {
				$save = array('status' => 4 ,'image_action' => 'CREATESNAP');
				$db_image->where($where_image)->save($save);
				return Tool::output('创建快照失败！',false,413);
			}

			//修改状态：创建快照成功
			$save = array('status' => 3 ,'image_action' => 'CREATESNAP SUCCESS','update_time' => $timestamp);
			$db_image->where($where_image)->save($save);

			/*判断快照是否创建成功*/
			$send_str = array(
					'root' => 'QUERYSNAPTASK',
					'QUERYSNAPTASK' => array(
							'task_id' => $item['id'].$timestamp.'CREATESNAP'
						)
				);

			$result	= self::sendCommand($send_str,$item['server_ip'],$item['server_port'],$item['secret_key']);

			//创建失败
			if ($result['result']['status'] == 2) {
				//修改状态：创建快照失败
				$save = array('status' => 4 ,'image_action' => 'CREATESNAP');
				$db_image->where($where_image)->save($save);
			}
			//创建中
			if ($result['result']['status'] == 0) {
				return Tool::output('快照创建中！',false,413);
			}

			/*创建成功，将快照保护*/
			$send_str	= array('root'=>'PROTECTSNAP',
								'PROTECTSNAP' => array('pool'	=>$item['image_pool'],
													   'image'	=>$item['image_code'],
													   'snap'	=>$item['image_snap_code'])
								);
	
			$result	= self::sendCommand($send_str,$item['server_ip'],$item['server_port'],$item['secret_key']);
			if ($result['code'] != 0) {
				//修改状态：保护快照失败
				$save = array('status' => 6 ,'image_action' => 'PROTECTSNAP');
				$db_image->where($where_image)->save($save);
				
				return Tool::output('保护快照失败！',false,413);
			}
		
		/*创建了快照（但还没有创建成功）*/
		} elseif($item['status'] == 3) {
			
			/*判断快照是否创建成功*/
			$send_str = array(
					'root' => 'QUERYSNAPTASK',
					'QUERYSNAPTASK' => array(
							'task_id' => $item['id'].$item['update_time'].'CREATESNAP'
						)
				);

			$result	= self::sendCommand($send_str,$item['server_ip'],$item['server_port'],$item['secret_key']);

			//创建失败
			if ($result['result']['status'] == 2) {
				//修改状态：创建快照失败
				$save = array('status' => 4 ,'image_action' => 'CREATESNAP');
				$db_image->where($where_image)->save($save);
			}
			//创建中
			if ($result['result']['status'] == 0) {
				return Tool::output('快照创建中！',false,413);
			}

			/*创建成功，将快照保护*/
			$send_str	= array('root'=>'PROTECTSNAP',
								'PROTECTSNAP' => array('pool'	=>$item['image_pool'],
													   'image'	=>$item['image_code'],
													   'snap'	=>$item['image_snap_code'])
								);
	
			$result	= self::sendCommand($send_str,$item['server_ip'],$item['server_port'],$item['secret_key']);
			if ($result['code'] != 0) {
				//修改状态：保护快照失败
				$save = array('status' => 6 ,'image_action' => 'PROTECTSNAP');
				$db_image->where($where_image)->save($save);
				
				return Tool::output('取消保护失败！',false,413);
			}
		}

		//修改状态：成功!
		$save = array('status' => 1 ,'image_action' => 'PROTECTSNAP');
		$db_image->where($where_image)->save($save);
		//删除计划任务
		$db_plan->where(array('mirroring_id' => $image_id))->delete();

		return Tool::output('ok！',true,0);		
	}

	/**
	 *	@der 删除镜像
	 *	@param int $image_id 镜像ID
	 */
	public function deleteImage($image_id){
		$item = M()->query('SELECT i.`id`,i.`image_code`,i.`image_pool`,i.`cloud_snap_id`,i.image_snap_code,m.server_ip,m.server_port,m.secret_key 
										from `kz_mirroring` i  left join `kz_mu_server` m on i.`mu_server_id` = m.`id`  where i.`id`='.$image_id.' AND i.type=2')['0'];
		
		if(!$item){
			return Tool::output('该镜像不存在！',false,243);
		}

		/*判断是否有服务器在使用此镜像*/
		$server_id = M('Yunji_server')->where(array('osid' => $item['id']))->getField('id');

		if ($server_id) {
			//生成任务日志
			Tool::createTaskLogs(array('status' => 0,'type' => 23,'remark' => "该镜像有云服务器在使用"));

			return Tool::output('该镜像有云服务器在使用，不能删除！',false,613);
		}

		/*取消快照保护*/
		$send_str	= array('root'=>'UNPROTECTSNAP',
							'UNPROTECTSNAP' =>array('pool'	=>$item['image_pool'],
										  			'image'	=>$item['image_code'],
										  			'snap'	=>$item['image_snap_code'])
							);
		$result	= self::sendCommand($send_str,$item['server_ip'],$item['server_port'],$item['secret_key']);

		if ($result['code'] != 0 ) {
			//生成任务日志
			Tool::createTaskLogs(array('status' => 0,'type' => 23,'info' => "UNPROTECTSNAP,image_id:{$image_id}"));

			return Tool::output('取消保护失败！',false,600);
		}		
		/*删除快照*/
		$send_str	= array('root'=>'DELETESNAP',
							'DELETESNAP' =>array('pool'	=>$item['image_pool'],
										 		 'image'=>$item['image_code'],
										 		 'snap'	=>$item['image_snap_code'])
							);
		$result	= self::sendCommand($send_str,$item['server_ip'],$item['server_port'],$item['secret_key']);

		if ($result['code'] != 0 ) {
			//生成任务日志
			Tool::createTaskLogs(array('status' => 0,'type' => 23,'info' => "DELETESNAP,image_id:{$image_id}"));
			return Tool::output('删除快照失败！',false,600);
		}	

		/*删除磁盘*/

		$send_str	= array('root'=>'DELETEDISK',
							'DELETEDISK' =>array('task_id'	=> $item['id'].'DELETEDISKIMAGE',
										 		 'pool'	=> $item['image_pool'],
										  		'image'	=> $item['image_code'])
							);

		$result	= self::sendCommand($send_str,$item['server_ip'],$item['server_port'],$item['secret_key']);

		if ($result['code'] != 0 ) {
			//生成任务日志
			Tool::createTaskLogs(array('status' => 0,'type' => 23,'info' => "DELETEDISK,image_id:{$image_id}"));

			return Tool::output('删除镜像失败！',false,600);
		}

		//生成任务日志
		Tool::createTaskLogs(array('status' => 1,'type' => 23,'info' => "image_id:{$image_id}"));

		$db = M('Mirroring');
		//删除数据库中的镜像
		$db->where(array('id' => $image_id))->delete();

		return Tool::output('ok',true,0);
	}

	/**
		@der 创建快照
			<CREATESNAP>
			    <task_id></task_id>
			    <pool></pool>
			    <image></image>
			    <snap></snap>        快照名，最长16个字符，仅限于字母和数字
			</CREATESNAP> 
	*/
	public static function createSnapshot(array $data){
		if (!$data['disk_code']) {
			return Tool::output('参数有误!',false,404);
		}
		if (!$data['uid']) {
			return Tool::output('uid不能为空!',false,404);
		}
		$disk = M('Cloud_disk')->field('id,mu_server_id,cloud_server_id,pool,disk_image_code')->where(array('disk_code' => $data['disk_code']))->find();
		if (!$disk) {
			return Tool::output('错误的disk_code!',false,404);
		}

		//如果uid不存在则根据服务器查找出uid
		if (!$data['uid']) {
			$data['uid'] = M('Living')->where(array('tid' => $disk['cloud_server_id']))->getField('uid');
		}
		
		/*快照表的数据*/
		$snap_code	= $data['disk_code'].Tool::randStr(1,4);
		!$data['snap_type'] && $data['snap_type']=1;
		$add = array(
			'uid'			  => $data['uid'],
			'snap_name'		  => $data['snap_name'],
			'mu_server_id'    => $disk['mu_server_id'],
			'cloud_server_id' => $disk['cloud_server_id'],
			'cloud_disk_id'	  => $disk['id'],
			'snap_code'		  => $snap_code,
			'snap_type'		  => $data['snap_type'],
			'status'		  => 0,
			'reamrk'		  => $data['remark'],
			'time'			  => $_SERVER['REQUEST_TIME']
			);
		$task_id = M('Cloud_snap')->add($add);

		//添加失败最有可能出现的问题：快照标识重复了
		if (!$task_id) {
			return Tool::output('创建快照失败',false,211);
		}

		//获取母服务器信息
		$send = M('Mu_server')->field('server_ip,server_port,secret_key')->where(array('id' => $disk['mu_server_id']))->find();
		
		/*发送的数据*/
		// $snap = Tool::randStr(10,16);
		$send_str = array(
			'root' 		 => 'CREATESNAP',
			'CREATESNAP' => array(
					'task_id' => "{$task_id}CREATESNAP",   //任务的ID
					'pool'	  => $disk['pool'], //硬盘磁盘所在存储池
					'image'	  => $disk['disk_image_code'],//磁盘所属镜像标识
					'snap'	  => $snap_code    //快照名，最长16个字符，仅限于字母和数字
					)
			);

		$result = self::sendCommand($send_str , $send['server_ip'] , $send['server_port'] , $send['secret_key']);
		if ($result['code'] != 0) {
			//生成任务日志
			Tool::createTaskLogs(array('status' => 0,'type' => 15,'info' => "disk_code : {$data['disk_code']}"));

		    M('Cloud_snap')->where(array('id' => $task_id))->delete();
			return Tool::output('创建快照失败',false,212);
		}

		$result['snap_id'] = $task_id;
		//生成任务日志
		Tool::createTaskLogs(array('type' => 15,'info' => "snap_id : {$task_id}"));
		return json_encode($result);
	}

	/**
	 *	@der 保护快照
	 *	@param int $snap_id 快照id
	 */
	public static function protectionSnap($snap_id){
		/*根据快照标识查找出该快照信息及对应的云磁盘信息*/
		$disk = M()->query('select d.`target`,d.`pool`,d.`size`,d.`disk_image_code`,s.`snap_type`,s.`snap_code`,s.`id`,s.`cloud_server_id`
										from `kz_cloud_snap` s left join `kz_cloud_disk` d on s.`cloud_disk_id`=d.`id` where s.`id`="'.$snap_id.'"')['0'];
		
		if (!$disk) {
			return Tool::output('错误的snap_id',false,414);
		}
		if ($disk['snap_type'] == 3) {
			return Tool::output('此快照已被保护',false,414);
		}

		/*获取云服务器和母服务器信息*/
		list($server , $send) = self::get_server_info($disk['cloud_server_id'],'id');

		$send_str	= array('root'=>'PROTECTSNAP',
							'PROTECTSNAP' => array('pool'	=>$disk['pool'],
												   'image'	=>$disk['disk_image_code'],
												   'snap'	=>$disk['snap_code'])
							);

		$result	= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);
		if ($result['code'] != 0) {
			//生成任务日志
			Tool::createTaskLogs(array('is_show' => 0,'status' => 0,'type' => 18,'info' => "PROTECTSNAP fail,snap_id:{$snap_id}"));
			return Tool::output('保护快照失败！',false,413);
		}

		$setField = array('snap_type' => 3);
		if ( !M('Cloud_snap')->where(array('id' => $snap_id))->setField($setField) ) {
			//生成任务日志
			Tool::createTaskLogs(array('is_show' => 0,'status' => 0,'type' => 18,'info' => "UPDATE snap_type fail,snap_id:{$snap_id}"));

			return Tool::output('修改快照信息失败！',false,413);
		}

		//生成任务日志
		Tool::createTaskLogs(array('is_show' => 0,'status' => 1,'type' => 18,'info' => "snap_id:{$snap_id}"));

		return Tool::output('OK',true,0);
	}

	/**
	 *	@der 取消快照保护
	 *	@param int $snap_id 快照id
	 */
	public static function cancelSnapProtection($snap_id){
		/*根据快照标识查找出该快照信息及对应的云磁盘信息*/
		$disk = M()->query('select d.`target`,d.`pool`,d.`size`,d.`disk_image_code`,s.`snap_type`,s.`snap_code`,s.`id`,s.`cloud_server_id`
										from `kz_cloud_snap` s left join `kz_cloud_disk` d on s.`cloud_disk_id`=d.`id` where s.`id`="'.$snap_id.'"')['0'];
		
		if (!$disk) {
			return Tool::output('错误的snap_id',false,404);
		}
		if ($disk['snap_type'] != 3) {
			return Tool::output('此快照并未保护',false,404);
		}

		//发送的数据
		$send_str = array('root'=>'UNPROTECTSNAP',
						  'UNPROTECTSNAP' => array(
						  		'pool'  => $disk['pool'],
							    'image'	=> $disk['disk_image_code'],
							    'snap'	=> $disk['snap_code']
							    )
					);

		/*获取云服务器和母服务器信息*/
		list($server , $send) = self::get_server_info($disk['cloud_server_id'],'id');

		$result	= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);
				
		if($result['code'] != 0) {
			//生成任务日志
			Tool::createTaskLogs(array('is_show' => 0,'status' => 0,'type' => 19,'info' => "UNPROTECTSNAP fail,snap_id:{$snap_id}"));

			return Tool::output('取消保护失败！',false,413);
		}

		//修改快照类型为1
		$setField = array('snap_type' => 1);
		if ( !M('Cloud_snap')->where(array('id' => $disk['id']))->setField($setField) ) {
			//生成任务日志
			Tool::createTaskLogs(array('is_show' => 0,'status' => 0,'type' => 19,'info' => "UPDATE snap_type fail,snap_id:{$snap_id}"));
		}

		//生成任务日志
		Tool::createTaskLogs(array('is_show' => 0,'status' => 1,'type' => 19,'info' => "UNPROTECTSNAP fail,snap_id:{$snap_id}"));

		return json_encode($result);
	}

	/**
	 *	@der 删除快照
	 *	@param int $snap_id 快照id
	 *
	 */
	public static function deleteSnap($snap_id){
		/*根据快照标识查找出该快照信息及对应的云磁盘信息*/
		$disk = M()->query('SELECT d.`target`,d.`pool`,d.`size`,d.`disk_image_code`,s.`snap_type`,s.`snap_code`,s.`id`,s.`cloud_server_id`,s.`status`
										from `kz_cloud_snap` s left join `kz_cloud_disk` d on s.`cloud_disk_id`=d.`id` where s.`id`="'.$snap_id.'"')['0'];
		if (!$disk) {
			return Tool::output('错误的snap_id',false,404);
		}
		if ($disk['snap_type'] == 3) {
			//生成任务日志
			Tool::createTaskLogs(array('status' => 0,'type' => 20,'remark'=>'此快照被保护','info' => "snap_id:{$snap_id}"));

			return Tool::output('此快照已被保护，无法删除',false,404);
		}
		if ($disk['status'] != 2) {
			//发送的数据
			$send_str = array('root'=>'DELETESNAP',
							  'DELETESNAP' => array(
							  		'pool'  => $disk['pool'],
								    'image'	=> $disk['disk_image_code'],
								    'snap'	=> $disk['snap_code']
								    )
						);

			/*获取云服务器和母服务器信息*/
			list($server , $send) = self::get_server_info($disk['cloud_server_id'],'id');

			$result	= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);
					
			if($result['code'] != 0) {
				//生成任务日志
				Tool::createTaskLogs(array('status' => 0,'type' => 20,'info' => "DELETESNAP,snap_id:{$snap_id}"));
				return Tool::output('删除快照失败！',false,413);
			}
		}
		
		
		//生成任务日志
		Tool::createTaskLogs(array('status' => 1,'type' => 20,'info' => "snap_id:{$snap_id}"));

		M('Cloud_snap')->where(array('id' => $disk['id']))->delete();

		return json_encode($result);
	}
	
	/**
	 *	@der 回滚快照  会关机自己启动
	 *  @param array $data array ('snap_id'=>'快照ID');
	 */
	public static function rollback(array $data){
		//查找出快照的信息
		$snap = M('Cloud_snap')->field('kz_cloud_snap.cloud_server_id,kz_cloud_snap.snap_code,target')->where(array('kz_cloud_snap.id' => $data['snap_id']))->join('LEFT JOIN kz_cloud_disk cloud_disk ON kz_cloud_snap.cloud_disk_id = cloud_disk.id')->find();
	
		list($server,$send) = self::get_server_info($snap['cloud_server_id'],'id');
		
		if (!$server) {
			return Tool::output('错误的name',false,404);
		}
		
		if (!$server) {
			return Tool::output('错误的snap_id',false,404);
		}

		$send_str = array(
			'root' => 'ROLLBACK',
			'ROLLBACK' => array(
				'name' 	 => $server['name'],
				'target' => $snap['target'],
				'snap'	 => $snap['snap_code']
				)
			);

		$result	= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);
		if ($result['code'] != 0) {
			//生成任务日志
			Tool::createTaskLogs(array('status' => 0,'type' => 17,'info' => "ROLLBACK,snap_id:{$data['snap_id']}"));
			return Tool::output('回滚失败',false,414);
		}

		//生成任务日志
		Tool::createTaskLogs(array('status' => 1,'type' => 17,'info' => "snap_id:{$data['snap_id']}"));

		//修改服务器状态为正在回滚
		$setField = array('status' => 11);
		M('Living')->where(array('tid' => $server['id']))->setField($setField);

		return json_encode($result);
	}

	/**
	 *	@der 获取快照进度
		 <QUERYSNAPTASK>
	    	<task_id></task_id>
		</QUERYSNAPTASK>
	 */
	public static function querySnapTask($snap_id){
		//获取母服务器信息
		$field = 'server_ip,server_port,secret_key';
		$where = array('kz_cloud_snap.id' => $snap_id);
		$join  = 'LEFT JOIN kz_mu_server mu_server ON kz_cloud_snap.mu_server_id = mu_server.id';
		$send  = M('Cloud_snap')->field($field)->where($where)->join($join)->find();
		
		$send_str = array(
				'root' => 'QUERYSNAPTASK',
				'QUERYSNAPTASK' => array(
						'task_id' => "{$snap_id}CREATESNAP"
					)
			);

		$result	= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);	
		
		if ($result['code'] != 0) {
			return Tool::output('获取进度失败',false,404);
		}

		$setField = array('status' => $result['result']['status']);

		M('Cloud_snap')->where(array('id' => $snap_id))->setField($setField);

		return json_encode($result);
    }

    /**
	 *	@der  升级服务器
	 *	@param array $data
	 *				 array(
	 						'uid',
	 						'cloud_server_id' => '服务器ID',
	 						'disk'   => array(硬盘大小),
	 						'cpu'    => 'cpu大小',
	 						'memory' => '内存大小',
	 						'band'   => '带宽'
	 						);
     */
    public static function upServer(array $data){
    	
    	if ( !$data['cloud_server_id']  || !$data['cpu']  || !$data['uid']  || $data['size'] > 2000) {
			return Tool::output('参数有误!',false,404);
		}
		$db 	   = M('Living');
		$where     = array('tid' => $data['cloud_server_id']);
		$field     = 'id,mu_server_id,name,memory,cpu,band';
		list($server,$send) = self::get_server_info($data['cloud_server_id'],'id',$field);
		$sendArray = array($server,$send);
		
		//磁盘
		if (!empty($data['disk'])){
			foreach ($data['disk'] as $v) {
				$send = array('uid' => $data['uid'],'cloud_server_id' => $data['cloud_server_id'],'size' => $v);
				$result = json_decode(self::createCloudDisk($send,$sendArray),true);

				if ($result['code'] != 0) {		
					//生成任务日志
					Tool::createTaskLogs(array('status' => 0,'type' => 2,'info' => "createCloudDisk fail,server_id:{$data['cloud_server_id']}"));			
					return json_encode($result);
				}
			}			
		}

		//带宽
		if ($server['band'] != $data['band']) {
			$send = array('cloud_server_id' => $data['cloud_server_id'],'value' => $data['band']);
			
			$result = json_decode(self::setBandWidth($send,$sendArray),true);
			if ($result['code'] != 0) {
				//生成任务日志
				Tool::createTaskLogs(array('status' => 0,'type' => 2,'info' => "setBandWidth fail,server_id:{$data['cloud_server_id']}"));			
				return json_encode($result);
			}
			
		}

		//内存
		if ($server['memory'] != $data['memory']) {
			$send = array('cloud_server_id' => $data['cloud_server_id'],'value' => $data['memory']);
			
			$result = json_decode(self::setMemory($send,$sendArray),true);
			if ($result['code'] != 0) {
				//生成任务日志
				Tool::createTaskLogs(array('status' => 0,'type' => 2,'info' => "setMemory fail,server_id:{$data['cloud_server_id']}"));			
				return json_encode($result);
			}			
		}

		//CPU
		if ($server['cpu'] != $data['cpu']) {
			$send = array('cloud_server_id' => $data['cloud_server_id'],'value' => $data['cpu']);
			
			$result = json_decode(self::setCpu($send,$sendArray),true);
			if ($result['code'] != 0) {
				//生成任务日志
				Tool::createTaskLogs(array('status' => 0,'type' => 2,'info' => "setCpu fail,server_id:{$data['cloud_server_id']}"));			
				return json_encode($result);
			}
		}

		//生成任务日志
		Tool::createTaskLogs(array('status' => 1,'type' => 2,'info' => "server_id:{$data['cloud_server_id']}"));			

		return Tool::output('OK',true,0);
    }

    /**
	 *	@der 创建云磁盘(会立即挂载)
	 *  @param array $data
	 *				 array('uid','cloud_server_id' => '服务器ID','size' => '磁盘大小(G)');
     */
	public static function createCloudDisk(array $data ,$sendArray = false){
		if ( !$data['cloud_server_id'] || !$data['size']  || $data['size'] > 2000) {
			return Tool::output('参数有误!',false,404);
		}
		
		if (!$data['remark']){
			$data['remark'] = '';
		}
		if (!$data['uid']){
			$data['uid'] = 0;
		}
		
		if ($sendArray) {
			list($server,$send) = $sendArray;
		} else {
			list($server,$send) = self::get_server_info($data['cloud_server_id'],'id');
		}
		
		
		if (!$server) {
			return Tool::output('cloud_server_id有误!',false,404);
		}

		$db = M('Cloud_disk');
		$length = $db->where(array('cloud_server_id' => $server['id']))->count();
		
		if ($length >=26) {
			return Tool::output('该云服务器云磁盘数量已达上限',false,500);
		}

		$str 	  = 'abcdefghijklmnopqrstuvwxyz';
		$target   = 'vd'.$str{$length};
		$newdisk_code	= $target.Tool::randStr(12);
		$disk_image_code= $newdisk_code."_image";
		$send_str = array(
			'root' => 'CREATEDISK',
			'CREATEDISK' => array(				
				'pool'   => self::POOL,
				'image'  => $disk_image_code,
				'size'   => $data['size'] * self::GTOM
				)
			);

		$result	= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);	
		
		if ($result['code'] != 0) {
			//生成任务日志
			Tool::createTaskLogs(array('is_show' => 0,'status' => 0,'type' => 10,'info' => "image_code:{$disk_image_code},server_id:{$server['id']}"));

			return Tool::output('创建云磁盘失败',false,414);
		}

		$db  = M('Cloud_disk');
		$add = array(
			'uid'			  => $data['uid'],
			'mu_server_id'	  => $send['id'],
		    'cloud_server_id' => $server['id'],
			'disk_code'	      => $newdisk_code,
			'disk_image_code' => $disk_image_code,
			'pool'			  => self::POOL,
			'target'		  => $target,
			'size'			  => $data['size'] * self::GTOM,
			'flag'			  => 'private',
			'iops'			  => self::IOPS,
			'io'			  => self::IO,
			'status'		  => 'attach',
			'time'			  => $_SERVER['REQUEST_TIME'],
			'remark'		  => $data['remark']
		);

		$disk_id = $db->add($add);

		if (!$disk_id) {
			return Tool::output('插入云磁盘失败',false,414);
		}

		$send_str	= array('root'=>'ATTACHDISK',
							'ATTACHDISK' =>array(
										  'name'	=>$server['name'],
										  'pool'	=>self::POOL,
										  'image'	=>$disk_image_code,
										  'target'	=>$target,
										  'size'	=>$data['size'] * self::GTOM
										  )
							);

		$result		= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);
		if($result['code']!=0) {
			$setField = array('status' => 'detach');
			$db->where(array('id' => $disk_id))->setField($setField);

			//生成任务日志
			Tool::createTaskLogs(array('is_show' => 0,'status' => 0,'type' => 24,'info' => "image_code:{$disk_image_code}"));

			return Tool::output('挂载云磁盘失败！',false,414);
		}

		//生成任务日志
		Tool::createTaskLogs(array('is_show' => 0,'status' => 1,'type' => 10,'info' => "image_code:{$disk_image_code},server_id:{$server['id']}"));

		return json_encode($result);
		
	}

	/**
	 *	@der 挂载云磁盘
	 *	@param array data array('server_name' => '服务器name','disk_id' => '磁盘ID','uid' );
	 */
	public static function mountCloudDisk(array $data , $sendArray = false){
		if ($sendArray) {
			list($server,$send) = $sendArray;
		} else {
			list($server,$send) = self::get_server_info($data['server_name']);
		}
		
		if (!$server) {
			return Tool::output('server_name有误!',false,404);
		}
		$db    =  M('Cloud_disk');
		$field = 'status,disk_image_code,size,target';
		$where = array('id' => $data['disk_id']);
		$disk  = $db->field($field)->where($where)->find();

		if (!$server) {
			return Tool::output('disk_id有误!',false,404);
		}

		if ($disk['target'] == self::XVDA) {
			return Tool::output('系统盘不能挂载',false,414);
		}

		if ($disk['status'] != 'detach') {
			return Tool::output('云磁盘已挂载',false,414);
		}

		if (!$data['uid']){
			$data['uid'] = 0;
		}
		if (!$data['remark']){
			$data['remark'] = '';
		}

		//查找出改服务器的磁盘个数
		$length = $db->where(array('cloud_server_id' => $server['id']))->count();
		$str 	  = 'abcdefghijklmnopqrstuvwxyz';
		$target   = 'vd'.$str{$length};

		$send_str	= array('root'=>'ATTACHDISK',
							'ATTACHDISK' =>array(
										  'name'	=>$server['name'],
										  'pool'	=>self::POOL,
										  'image'	=>$disk['disk_image_code'],
										  'target'	=>$target,
										  'size'	=>$disk['size']
										  )
							);

		$result		= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);
		if($result['code']!=0) {
			//生成任务日志
			Tool::createTaskLogs(array('is_show' => 0,'status' => 0,'type' => 24,'info' => "image_code:{$disk_image_code}"));

			return Tool::output('挂载云磁盘失败！',false,414);
		}

		$save = array(
			'status'		  => 'attach',
			'uid'			  => $data['uid'],
			'cloud_server_id' => $server['id'],
			'reamrk'		  => $data['remark'],
			'target'		  => $target,
			'type'			  => 1
			);
		if ( !$db->where(array('id' => $data['disk_id']))->save($save) ) {
			//生成任务日志
			Tool::createTaskLogs(array('is_show' => 0,'status' => 0,'type' => 24,'info' => "UPDATE status fail,ATTACHDISK,disk_id:{$data['disk_id']},server_name:{$server['name']}"));

			return Tool::output('挂载云磁盘失败！',false,600);
		}

		//生成任务日志
		Tool::createTaskLogs(array('is_show' => 0,'status' => 1,'type' => 24,'info' => "ATTACHDISK,disk_id:{$data['disk_id']},server_name:{$server['name']}"));
		
		return Tool::output('OK',true,0);
	}

	/**
 	 * 	@der 卸载云磁盘
 	 *	@param int disk_id 云磁盘ID
	 */
	public static function unCloudDisk($disk_id , $sendArray = false){
		if ($sendArray) {
			list($server,$send) = $sendArray;
			$send['name'] = $server['name'];
		} else {
			$where     = array('kz_cloud_disk.id' => $disk_id);
			$field     = 'living.status AS living_status,yunji_server.name,kz_cloud_disk.status,kz_cloud_disk.target,kz_cloud_disk.pool,kz_cloud_disk.disk_image_code,mu_server.server_ip,server_port,secret_key';
			$join 	   = 'LEFT JOIN kz_mu_server mu_server ON kz_cloud_disk.mu_server_id = mu_server.id LEFT JOIN kz_yunji_server yunji_server ON kz_cloud_disk.cloud_server_id = yunji_server.id LEFT JOIN kz_living living ON yunji_server.id = living.tid';
			$send      = M('Cloud_disk')->field($field)->where($where)->join($join)->find();
		}
		
		if (!$send) {
			return Tool::output('disk_id fail',false,404);
		}

		if($send['status'] != 'attach') {
			return Tool::output('磁盘已卸载',false,414);
		}
		
		if($send['living_status'] != 4) {
			return Tool::output('请先将服务器关机',false,414);
		}

		/*卸载云磁盘*/
		$send_str	= array('root'=>'DETACHDISK',
							'DETACHDISK' =>array('name'	   => $send['name'],
										 		 'target' => $send['target'],
										  		 )
							);

		$result	= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);

		if ($result['code'] != 0 ) {
			//生成任务日志
			Tool::createTaskLogs(array('is_show' => 0,'status' => 0,'type' => 12,'info' => "DETACHDISK,disk_id:{$disk_id},server_name:{$send['name']}"));
			return Tool::output('卸载云磁盘失败！',false,600);
		}

		//修改云磁盘状态
		$save = array('status' => 'detach','cloud_server_id' => 0);
		if ( !M('Cloud_disk')->where(array('id' => $disk_id))->save($save) ) {
			//生成任务日志
			Tool::createTaskLogs(array('is_show' => 0,'status' => 0,'type' => 12,'info' => "UPDATE status fail,DETACHDISK,disk_id:{$disk_id},server_name:{$send['name']}"));
		}

		//生成任务日志
		Tool::createTaskLogs(array('is_show' => 0,'status' => 1,'type' => 12,'info' => "DETACHDISK,disk_id:{$disk_id},server_name:{$send['name']}"));

		return Tool::output('OK',true,0);
	}

	/**
	 *	@der 删除云磁盘
	 *	@param int $disk_id 磁盘ID
	 */
	public static function deleteCloudDisk($disk_id , $sendArray = false){
		if ($sendArray) {
			list($server,$send) = $sendArray;
		} else {
			$where     = array('kz_cloud_disk.id' => $disk_id);
			$send      = M('Cloud_disk')->field('kz_cloud_disk.status,kz_cloud_disk.pool,kz_cloud_disk.disk_image_code,mu_server.server_ip,server_port,secret_key')->where($where)->join('kz_mu_server mu_server ON kz_cloud_disk.mu_server_id = mu_server.id')->find();
		}
		
		if (!$send) {
			return Tool::output('disk_id error',false,404);
		}

		if($send['status'] != 'detach') {
			return Tool::output('磁盘未卸载',false,414);
		}
		
		/*删除磁盘*/
		$send_str	= array('root'=>'DELETEDISK',
							'DELETEDISK' =>array('task_id'	=> $disk_id.'DELETEDISKIMAGE',
										 		 'pool'		=> $send['pool'],
										  		 'image'	=> $send['disk_image_code'])
							);

		$result	= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);

		if ($result['code'] != 0 ) {
			//生成任务日志
			Tool::createTaskLogs(array('status' => 0,'type' => 14,'info' => "DELETEDISK,disk_id:{$disk_id}"));

			return Tool::output('删除云磁盘失败！',false,600);
		}

		M('Cloud_disk')->where(array('id' => $disk_id))->delete();

		//生成任务日志
		Tool::createTaskLogs(array('status' => 1,'type' => 14,'info' => "disk_id:{$disk_id}"));
		
		return Tool::output('OK',true,0);
	}

	/**
	 * 	@der 设置带宽	
	 *  @param array $data
	 *				 array(
	 						'cloud_server_id' => '服务器ID',
	 						'field' => '带宽类型' LANDOWNLOAD（内网下行）,LANUPLOAD（内网上行）,WANDOWNLOAD（外网下行）,WANUPLOAD（外网上行）
	 						'value' => '值'
	 						);
	 *	@param array || boolean $sendArray 服务器和母服务器信息
	 */
	public static function setBandWidth(array $data , $sendArray = false){
		
		switch ($data['field']) {
			case 'LANDOWNLOAD': //内网下行
				$key = 'lan_download_bandwidth';
				break;
			case 'LANUPLOAD':   //内网上行
				$key = 'lan_upload_bandwidth';
				break;
			case 'WANDOWNLOAD': //外网下行
				$key = 'wan_download_bandwidth';
				break;
			case 'WANUPLOAD':  //外网上行
				$key = 'band';
				break;
			default:
				$data['field'] = 'WANUPLOAD';
				$key = 'band';
				break;
		}

		if ($sendArray) {
			list($server,$send) = $sendArray;
		} else {
			list($server,$send) = self::get_server_info($data['cloud_server_id'],'id',"id,mu_server_id,name,{$key}");
		}
		
		if (!$server) {
			return Tool::output('cloud_server_id有误!',false,404);
		}

		if ($data['value'] == $server[$key]) {
			return Tool::output('新的值与原来的值重复!',false,404);
		}

		$send_str = array(
				'root' => 'SETIFTUNE',
				'SETIFTUNE' => array(
						'name'  => $server['name'],
						'field' => $data['field'],
						'value' => $data['value']
					)
			);

		$result		= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);
		
		if ($result['code'] != 0) {
			//生成任务日志
			Tool::createTaskLogs(array('is_show' => 0,'status' => 0,'type' => 25,'info' => "server_id:{$server['id']},value:{$data['value']}"));
			return Tool::output('设置带宽失败！',false,414);
		}

		//生成任务日志
		Tool::createTaskLogs(array('is_show' => 0,'status' => 1,'type' => 25,'info' => "server_id:{$server['id']},value:{$data['value']}"));

		M(self::DB)->where(array('id' => $server['id']))->setField(array($key => $data['value']));

		return json_encode($result);
		
	}

	/**
	 * 	@der 设置内存大小
	 *	@param array $data 
	 *				 array('cloud_server_id' => '服务器ID' ,'value' => '值');
	 *	@param array || boolean $sendArray 服务器和母服务器信息
	 */
	public static function setMemory(array $data , $sendArray = false){
		if ($sendArray) {
			list($server,$send) = $sendArray;
		} else {
			list($server,$send) = self::get_server_info($data['cloud_server_id'],'id',"id,mu_server_id,name,memory");
		}
		
		if (!$server) {
			return Tool::output('cloud_server_id有误!',false,404);
		}

		if ($data['value'] == $server['memory']) {
			return Tool::output('新的值与原来的值重复!',false,404);
		}

		if ($data['value'] == 0) {
			$memory = 0;
			$data['value'] = 512;
		} else {
			$memory = $data['value'];
			$data['value'] *= 1024;
		}

		$send_str = array(
				'root' => 'SETMEMORY',
				'SETMEMORY' => array(
						'name'  => $server['name'],
						'memory' => $data['value']
					)
			);

		$result		= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);
		
		if ($result['code'] != 0) {
			//生成任务日志
			Tool::createTaskLogs(array('is_show' => 0,'status' => 0,'type' => 26,'info' => "server_id:{$server['id']},value:{$data['value']}"));
			return Tool::output('设置内存失败！',false,414);
		}
		
		M(self::DB)->where(array('id' => $server['id']))->setField(array('memory' => $memory));

		//生成任务日志
		Tool::createTaskLogs(array('is_show' => 0,'status' => 1,'type' => 26,'info' => "server_id:{$server['id']},value:{$data['value']}"));

		return json_encode($result);		
	}

	/**
	 * 	@der 设置CPU
	 *	@param array $data
	 *				 array('cloud_server_id' => '服务器ID' ,'value' => '值');
	 *	@param array || boolean $sendArray 服务器和母服务器信息
	 */
	public static function setCpu(array $data ,$sendArray){
		if ($sendArray) {
			list($server,$send) = $sendArray;
		} else {
			list($server,$send) = self::get_server_info($data['cloud_server_id'],'id',"id,mu_server_id,name,cpu");
		}
		
		if (!$server) {
			return Tool::output('cloud_server_id有误!',false,404);
		}

		if ($data['value'] == $server['cpu']) {
			return Tool::output('新的值与原来的值重复!',false,404);
		}

		$send_str = array(
				'root' => 'SETVCPU',
				'SETVCPU' => array(
						'name'  => $server['name'],
						'vcpu'  => $data['value']
					)
			);

		$result		= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);
		
		if ($result['code'] != 0) {
			//生成任务日志
			Tool::createTaskLogs(array('is_show' => 0,'status' => 0,'type' => 27,'info' => "server_id:{$server['id']},value:{$data['value']}"));
			return Tool::output('设置cpu失败！',false,414);
		}
		
		M(self::DB)->where(array('id' => $server['id']))->setField(array('cpu' => $data['value']));

		//生成任务日志
		Tool::createTaskLogs(array('is_show' => 0,'status' => 1,'type' => 27,'info' => "server_id:{$server['id']},value:{$data['value']}"));

		return json_encode($result);
		
	}

	/**
	 *	@der 重装系统
	 *	@param array $data array(镜像ID，服务器名称，[密码])
	 */
	public static function reInstallOs(array $data){
		list($server,$send) = self::get_server_info($data['name']);
		if (!$data['image_id']) {
			return Tool::output('镜像ID不能为空',false,404);
		}
		if ( !$server ) {
			return Tool::output('错误的name',false,404);
		}
		/*如果用户没有输入密码则随机生成密码*/
		if(!$data['password']){
			$data['password'] = Tool::randStr(12);
		} elseif( !preg_match('/^[a-z,0-9,A-Z]{2,12}$/is',$data['password']) ){
			//生成任务日志
			Tool::createTaskLogs(array('status' => 0,'type' => 8,'remark' => '密码不符合规范','info' => "server_id:{$server['id']}"));

			return Tool::output('密码最长12个字符，仅限于字母和数字！',false,400);
		}

		//获取镜像信息 disk_image_code磁盘对应镜像标识  快照标识   镜像标识 都从这里取出来（原始）
		$image =  M()->query('SELECT i.`id`,o.os_code AS `os_type`,i.`image_code`,i.`image_snap_code`,i.`image_pool`,i.image_pool AS `pool`,i.image_snap_code AS `snap_code`
							   FROM `kz_mirroring` i LEFT JOIN `kz_os` o ON i.oid = o.id 
							   where i.`status`=1 and i.`id`="'.$data['image_id'].'"')['0'];	

		if ( !$image ) {
			return Tool::output('错误的image_id',false,404);
		}	
		//发送的数据
		$send_str = array(
			'root' => 'REINSTALL',
			'REINSTALL' => array(
				'name'		   => $data['name'],
				'os_type'      => $image['os_type'],
				'os_rbd_pool'  => $image['image_pool'],
				'os_rbd_image' => $image['image_code'],
				'os_rbd_snap'  => $image['image_snap_code'],
				'password'	   => $data['password']
				)
			);
		//发送请求
		$result	= self::sendCommand($send_str,$send['server_ip'],$send['server_port'],$send['secret_key']);			  	
		if ($result['code'] != 0) {
			//生成任务日志
			Tool::createTaskLogs(array('status' => 0,'type' => 8,'info' => "REINSTALL,server_id:{$server['id']}"));
			return Tool::output('重装系统时遇到了问题!',false,414);
		}
		/*修改服务器状态为重装系统中*/
		M('Living')->where(array('tid' => $server['id']))->setField(array('status' => 14));
		/*修改服务器的镜像ID和密码*/
		$where 	  = array('id' => $server['id']);
		$save 	  = array(
			'osid' => $data['image_id'],
			'password' => Tool::encryptPassword($data['password'])
			);
		
		if ( !M('Yunji_server')->where($where)->save($save) ) {
			return Tool::output('修改系统时出错!',false,414);
		}

		//生成任务日志
		Tool::createTaskLogs(array('status' => 1,'type' => 8,'info' => "server_id:{$server['id']}"));

		return json_encode($result);
	}

	/**
	 *	@der 获取监控情况
	 *	@param array $data 
	 *	@param int $type 
	 *				1 CPU使用数据
	 *				2 网络使用数据	
	 */
	public static function getMonitorUsage(array $data , $type = 1){
		list($server,$send) = self::get_server_info($data['name']);

		if ( !$server ) {
			return Tool::output('错误的name',false,404);
		}

		switch ($type) {
			case 1://CPU使用数据
				$name = 'GETCPUUSAGE';
				break;
			case 2://网络使用数据
				$name = 'GETNETUSAGE';
				break;
		}

		$send_str = array(
			'root' => $name,
			$name  => array(
				'name' => $data['name'],
				'year' => $data['year'],
				'month'=> $data['month'],
				'day'  => $data['day'],
				'hour' => $data['hour']
				)
			);

		$result = self::sendCommand($send_str , $send['server_ip'] , $send['server_port'] , $send['secret_key']);
		
		if ($result['code'] != 0) {
			return Tool::output('获取信息失败',false,212);
		}

		return json_encode($result);
	}

	/**
	 *	@der 获取实时监控
	 *	@param int $mu_server_id 母服务器ID
	 */
	public function getMonitor($mu_server_id){
		/*查找出母服务器的信息*/
		$send     = M('Mu_server')->field('server_ip,server_port,secret_key')->where(array('id' => $mu_server_id))->find();
		$send_str = array(
			'root' => 'GETMONITOR',
			'GETMONITOR' => ''
			);
		
		$result = self::sendCommand($send_str , $send['server_ip'] , $send['server_port'] , $send['secret_key']);
		
		return $result;
	}

	/**
	 *	@der 获取服务器状态
     *  @param string $name 服务器名称
     *	@param int $status 状态。如果填写了则只有获取到的状态和该参数一样时才返回true，其余返回false
	 */
	public static function getStatus($name , $status = ''){
		if (!$name) {
			return Tool::output('name参数不存在',false,404);
		}
		list($server,$send) = self::get_server_info($name);
		if ( !$server ) {
			return Tool::output('错误的name',false,404);
		}
		$data = array(
			'root' => 'GETDOMAINSTATUS',
			'GETDOMAINSTATUS' => array(
				'name' => $name
				)
			);

		$result = self::sendCommand($data , $send['server_ip'] , $send['server_port'] , $send['secret_key']);
		
		if ($result['code'] != 0) {
			return Tool::output('获取状态失败',false,212);
		}
		
		if ($status) {
			if ( self::status_convert($result['result']['status']) == $status) {

				$where = array('tid' => $server['id'] );
				M('Living')->where($where)->setField(array('status' => $status));
				return Tool::output('ok',true,0);
			} else {
				return Tool::output('Wait',false,400);
			}
		}

		$arr = array('status' => self::status_convert($result['result']['status']));
		return Tool::output('ok',true,0,$arr );
	}

	/**
	 *	@der 云服务器的删除操作
	 */
	public function serverDelete($name,$server_id){

		$result = json_decode(self::serverOperation($name,4),true);

		if ($result['code'] != 0) {
			return $result;
		} 
		
		//修改为回收站状态
		$save = array('isdelete' => 1 , 'delete_time' => $_SERVER['REQUEST_TIME']);
		M('Living')->where(array('tid' => $server_id))->save($save);
		
		//将已用的IP释放
		M('Mu_ip')->where(array('cloud_server_id' => $server_id))->setField(array('status' => 0));
		
		//所在母服务器的已建云服务器-1
		$mu_server_id = M(self::DB)->where(array('id' => $server_id))->getField('mu_server_id');
		M('Mu_server')->where(array('id' => $mu_server_id))->setDec('server_sum');
		
		//该云服务器的存储盘状态改变为云服务器已删除
		$where = array('cloud_server_id' => $server_id);
		$save  = array('type' => 3 , 'status' => 'detach');
		M('Cloud_disk')->where($where)->save($save);
		//删除该云服务器的快照
		M('Cloud_snap')->where($where)->delete();

		return $result;
	}

	/**
		@der 云服务器的 开机 关机 重启 删除
		@param string $name 服务器名称
		@param int    $type 操作类型
							1、开机
							2、关机
							3、重启
							4、删除
		@return json
	*/
	public static function serverOperation($name , $type = 3){
		if (!$name) {
			return Tool::output('name参数不存在',false,404);
		}
		list($server,$send) = self::get_server_info($name);
		if ( !$server ) {
			return Tool::output('错误的name',false,404);
		}
		switch ($type) {
			case '1':
				$logs_type = 5;
				$type   = 'BOOTDOMAIN';
				$status = 8;
				break;
			case '2':
				$logs_type = 4;
				$type   = 'SHUTDOMAIN';
				$status = 9;
				break;
			case '3':
				$logs_type = 6;
				$type   = 'REBOOTDOMAIN';
				$status = 7;
				break;
			case '4':
				$type   = 'DELETEDOMAIN';
				$status = 23;
				break;
			default:
				return Tool::output('错误的类型',false,405);
				break;
		}

		$data = array(
			'root' => $type,
			 $type => array(
				'name' => $name
				)
			);

		$result = self::sendCommand($data , $send['server_ip'] , $send['server_port'] , $send['secret_key']);
		
		if ($result['code'] != 0) {
			
			if (isset($logs_type)) {
				//生成任务日志
				Tool::createTaskLogs(array('status' => 0,'type' => $logs_type,'info' => "{$type},server_id:{$server['id']}"));
			}

			return Tool::output('操作失败',false,212);
		}
		
		if (isset($logs_type)) {
			//生成任务日志
			Tool::createTaskLogs(array('status' => 1,'type' => $logs_type,'info' => "{$type},server_id:{$server['id']}"));
		}

		/*修改服务器状态*/
		M('Living')->where(array('tid' => $server['id']))->setField(array('status' => $status));

		return json_encode($result);
	}

	/**
		@der 更改密码(包括VNC) 更改完自动重启
		@param array $data 数据 array(name,password)
		@param int 	 $type 密码修改类型 
							1、普通密码
							2、VNC密码
		@return json
	*/
	public static function  changePassword($data , $type = 1){
		if (!$data['name']) {
			return Tool::output('name参数不存在',false,404);
		}
		list($server,$send) = self::get_server_info($data['name']);

		if( !preg_match('/^[a-z,0-9,A-Z]{2,12}$/is',$data['password']) ){
			//生成任务日志
			Tool::createTaskLogs(array('status' => 0,'type' => 9,'remark' => '密码不符合规范','info' => "server_id:{$server['id']}"));

			return Tool::output('密码最长12个字符，仅限于字母和数字！',false,400);
		}
		
		if ( !$server ) {
			return Tool::output('错误的name',false,404);
		}
		switch ($type) {
			case '1':
				$type   = 'CHANGEPASSWORD';
				$status = 10;
				break;
			case '2':
				$type   = 'CHANGEVNCPASSWORD';
				$status = 10;
				break;
			default:
				return Tool::output('错误的类型',false,405);
				break;
		}
		
		$send_str = array(
			'root' => $type,
			 $type => array(
				'name' 	   => $data['name'],
				'password' => $data['password']
				)
			);

		$result = self::sendCommand($send_str , $send['server_ip'] , $send['server_port'] , $send['secret_key']);
		if ($result['code'] != 0) {
			//生成任务日志
			Tool::createTaskLogs(array('status' => 0,'type' => 9,'info' => "{$type},server_id:{$server['id']}"));

			return Tool::output('操作失败',false,212);
		}

		/*修改服务器状态*/
		M('Living')->where(array('tid' => $server['id']))->setField(array('status' => $status));
		
		/*修改服务器密码*/
		$setField = array('password' => Tool::encryptPassword($data['password']) );
		M(self::DB)->where(array('id' => $server['id']))->setField($setField);

		Tool::createTaskLogs(array('status' => 1,'type' => 9,'info' => "server_id:{$server['id']}"));

		return json_encode($result);
	}

	/*
		@der 发送命令到服务器
	*/
	public static function sendCommand($send_str ,$server_ip ,$server_port ,$secret_key){
		
		//如果是数组将它转换成xml
		if (is_array($send_str)) {
			//记录命令名称
			$command_name = $send_str['root'];
			unset($send_str['root']);
			$send_str 	  = Xml::createXml($send_str);
			//return json_decode( Tool::output('测试' ,false ,'666') ,true);
		}
		
		$secret_key = trim($secret_key);
		//加密 
		$leng_all = strlen($send_str)+strlen(time())+4+1;//1个pack长度+3个空格个数(1个pack长度是4)

		// 网络字节顺序
		$query = pack("N",$leng_all).Tool::rc4Encrypt(time().' '.$send_str,$secret_key);
		
		$logsStartTime = microtime(true);

		/*发送请求*/
		if ( !$document = Socket::send($query ,$leng_all, $server_ip,$server_port) ) {
			return json_decode( Tool::output(Socket::getErrorMsg() ,false ,Socket::getCode()) ,true);
		}

		$logsEndTime = microtime(true);
		
		$process_time		= $logsEndTime - $logsStartTime;

		$document = Tool::rc4Decrypt($document,$secret_key);
		
		if ($document) {
			//将xml数据转换成数组
			$xml = Xml::xml_convert($document ,true);
			//p(htmlspecialchars($document));die;
			
			if ($xml) {
				if ($xml['result'] == 0) {
					$result = Tool::output('ok',true,0,['result' => $xml]);
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

		if ($command_name != 'GETMONITOR') {
			//查找出命令ID
			$command_id = M('Command')->where(array('name' => $command_name))->getField('id');
			!empty($_SESSION['uid']) ? $user_id = $_SESSION['uid'] : $user_id = 0;
			$add = array(
					 'command_id' => $command_id,
					 'user_id'	  => $user_id,
					 'client_ip'  => get_client_ip(),
					 'server_ip'  => $server_ip,
					 'secret_key' => $secret_key,
					 'operate'	  =>  GROUP_NAME.'/'.MODULE_NAME.'/'.ACTION_NAME,
					 'content'	  => htmlspecialchars($send_str),
					 'result'	  => htmlspecialchars($document),
					 'status'	  => $status,
					 'process_time' => $process_time,
					 'time'	      => time()
				);

			//插入命令日志
			M('Logs_command')->add($add);
		}
		
		
		return json_decode($result , true);
	}

	/**
		@der 获取服务器和母服务器的信息
		@return array
	*/
	private static function get_server_info($name , $key = 'name' ,$field = 'id,mu_server_id,name'){
		$mu_server = M(self::DB)->field($field)->where(array($key => $name))->find();

		/*查找出母服务器的信息*/
		$send = M('Mu_server')->field('id,server_ip,server_port,secret_key')->where(array('id' => $mu_server['mu_server_id']))->find();

		return array($mu_server , $send);
	}

	/**
	 *	@der 服务器状态转换
     *	@param string $key 状态
     *	@return int status
	 */
	private static function status_convert($key){

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

	/*
		@der 禁止new 对象
	*/
	private function __construct(){}
}

