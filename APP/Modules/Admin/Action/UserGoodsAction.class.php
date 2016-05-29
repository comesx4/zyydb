<?php
/*
	@der 用户产品管理
*/
Class UserGoodsAction extends CommonAction{

	/*
		@der 产品列表
	*/
	public function index(){

	$db=M('Living');
        list ($goods,$tmp,$page) = $this->_search(D('LivingView'));       
 
                
		foreach ($goods as $key => $value) {
                    $m=M($value['table_name'])->where(array('id' => $value['tid']))->field(true)->find(); 
			//$goods[$key]['cat'] = $m;
                        $goods[$key]['cat'] = productinfo2str($value['gid'], $m,':','<br />');
		}	
              
                
		$this->goods=$goods;
		$this->fpage = $page['1'];
		$this->tmp   = $tmp;
		$this->all_goods = show_list('goods','gid','goods',$tmp['gid']);
		$this->display();
	}

	/**
		@der 审核界面
	*/
	public function check(){
		switch ($_GET['gid']) {
			case 1:
				$display = '';
				$data = D('LivingView')->get_result();	
				break;
			case 19:
				$display = 'ddos';
				$data = D('LivingView')->get_result('','wan_ip');	
				break;
			case 20:
				$display = 'rent';
				$data = D('LivingView')->get_result('',true);	
				break;
			case 22:
				$display = 'storage';
				$data = D('LivingView')->get_result('',true);	
				break;		
			case 23:
				$display = 'rent';
				$data = D('LivingView')->get_result('',true);	
				break;
		}
		
		$this->data = $data;
		$this->display($display);
	}

	/**
		@der 处理云服务器审核的创建
	*/
	public function create_server(){
		IS_POST || redirect(__APP__); 
		if (post_isnull('cpu','memory','band','image_id','city','server_id')) {
			$this->error('带(*)的不能为空');
		}
		/*只有待审核的产品才可以进来*/
		$db    = M('Yunji_server');
		$where = array('id'=>I('post.id'));
		if ( $db->where($where)->getField('status') != 0) {
			$this->error('已审核过的产品');
		}
		$data = array(
			'cpu'				     => I('post.cpu','2','intval'),
			'momery'			     => I('post.monery','1','intval'),
			'image_id' 			     => I('post.image_id',0,'intval'),
			'size'	   			     => I('post.disk','','intval'),
			'wan_upload_bandwidth'   => I('post.band','1','intval'),
			'lan_download_bandwidth' => I('post.lan_download_bandwidth'),
			'wan_download_bandwidth' => I('post.wan_download_bandwidth'),
			'server_id'				 => I('post.server_id','','intval')
			);
		import('Class.Server',APP_PATH);
		$where     		   = array('city_id' => I('post.city') , 'status' => 1);
		$region_id 		   = M('Mu_region')->field('id')->where($where)->select();
		$data['region_id'] = peatarr($region_id , 'id');
		
		if (!$region_id) {
			$this->error('未找到该城市下的机房');
		}
		
		$setFiled = array('status' => 2);		
		$result = json_decode(Server::createServer($data),true);
	
		if ($result['code'] == 0) {
			$this->success('审核成功',U('index'));
		} else {
			$this->error($result['error']);
		}
	}

	/*
		@der 产品编辑视图
	*/
	public function update(){
		$gid   = I('get.gid',0,'intval');
		
		$data = D('LivingView')->get_result();	
		$this->data = $data;
		$this->dbName = $dbName;
		
		if ($gid == 1) { //云集服务器
			$this->display();
		} elseif ($gid == 17){ //腾讯云服务器
			$this->display('tenxun_update');
		} elseif ($gid == 18) { //阿里云服务器
			$this->display('ali_update');
		}
	}	

	/*
		@der 产品编辑操作
	*/
	public function save(){
		if (!$this->ispost()) redirect(__APP__);
		
		/*在实例表中修改云服务器ID和状态*/
		$where = array('id'=>I('post.id','','intval'));
		$save = array(
			'instance_id' 	=> I('post.instance_id',''),
			'status'      	=> I('post.status','2','intval'),
			'instance_name' => I('post.instance_name')
			);
		M('Living')->where($where)->save($save);

		/*修改产品表的信息*/
		$where = array('id'=>I('post.tid','','intval'));
		$save = array(
			'region' 		=> I('post.region'),		//可用区
			'device_class'  => I('post.device_class'),  //类型
			'intranet_ip'   => I('post.intranet_ip'),   //内网IP
			'public_ip'     => I('post.public_ip'),		//公网IP
			'subnet_id'     => I('post.subnet_id'),		//子网ID
			'osid'			=> I('post.osid','1','intval'),//镜像ID
			);
	
		if ( M(I('post.dbName',''))->where($where)->save($save) ) {

			$this->success('配置成功!',U('index'));
		} else {

			$this->error('配置失败!');
		}
	}

	/**
		@der 处理套餐产品的修改
	*/
	public function ddosSave(){
		IS_POST || redirect(__APP__);
		$gid   = I('post.gid','','intval');
		$tid   = I('post.tid','','intval');
		$db    = D('LivingView');
		$where = array('tid'=>$tid);
		/*验证是否是审核状态的产品*/
		if ( !$db->isCheck($tid) ) {
			redirect(__APP__);
		}

		//修改产品表的信息状态
		if ($db->packageCheck($gid,$tid)) {
			$this->success('审核成功!',U('index'));
		} else {
			$this->error('审核失败!');
		}

	}

	// //产品的开通和关闭
	// public function operate(){
	// 	if(!$this->ispost()) redirect(__APP__);
	// 	//判断是多个还是单个
	// 	if(isset($_POST['id'])){	
	// 	if(M('Living')->where(array('id'=>$this->_post('id','intval')))->setField(array('status'=>$this->_post('status','intval'))))
	// 		 echo 1;
	//     else
	//     	 echo 0;
	// 	}else{
			
	// 	//判断是开启还是关闭
	// 	if(isset($_POST['start']))
	// 		$this->is_true(0);
	//     else
	// 		 $this->is_true(2);	
	// 	}
	// }

	// //处理开通和关闭的私有方法
	// private function is_true($status){
	// 	  $where=array();
	// 	  foreach($this->_post('status') as $v){
	// 	   	$where[]=array('id'=>$v);
	// 	  }
	// 	  $count=count($where);
	// 	  $save=array('status'=>$status);
 //          for($i=0;$i<$count;$i++){
	// 	 	  $is=M('Living')->where($where[$i])->save($save);
	//  	  }
	//  	  if($is)
	//  	  	 $this->success('操作成功',U('goods'));
	// 	  else
	// 	     $this->error('操作失败请重试');
	// }

	/**
		@der 处理搜索
	*/
	private function _search($db,$where = ''){
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

			// 产品
			if ( !empty($tmp['gid']) ) {
				$where['gid']  = $tmp['gid'];				
				$pwhere['gid'] = $tmp['gid'];
			}

			//购买开始时间
			if ( !empty($tmp['min_start_time']) ) {
				$date 				= strtotime($tmp['min_start_time']);	 
				$where['start']  	= array('EGT',$date);
				$pwhere['min_start_time'] = $tmp['min_start_time'];
			}

			//购买结束时间
			if ( !empty($tmp['max_start_time']) ) {
				$date 				= strtotime($tmp['max_start_time']);	 
				$where['start']  	= array('ELT',$date);
				$pwhere['max_start_time'] = $tmp['max_start_time'];
			}

			//用户名称
			// if ( !empty($tmp['username']) ) {
			// 	$where['username']  = array('LIKE',"{$tmp['username']}%");
			// 	$pwhere['username'] = $tmp['username'];
			// }
		}

		//分页
		$page = X($db,$where,C('PAGE_NUM'),$pwhere);
		$data = $db->where($where)->limit($page['0']->limit)->order('id DESC')->select();
		return array($data,$tmp,$page);
	}
}