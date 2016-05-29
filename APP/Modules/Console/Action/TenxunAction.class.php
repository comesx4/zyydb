<?php
/*
	@der 云服务器控制器
*/
Class ServerAction extends CommonAction{

	/*
		@der 云服务器首页
	*/
	public function index(){	
		
		$id  = I('get.id','','intval');
		$db  = M('Living');
		/*该类产品的实例总数*/
		$sum = $db->where(array('gid'=>$id))->count();
		
		/*运行中的产品*/
		$where  = array('uid'=>$_SESSION['id'],'gid'=>$id,'status'=>2);
		$living = $db->field('instance_id,instance_name')->where($where)->select();
		
		/*即将过期的产品(还有1周到期的产品)*/
		$time    = time()+(86400*7);//7天后的时间
		$where   = array('uid'=>$_SESSION['id'],'end'=>array('lt',$time));
		$expired = $db->where($where)->count();
		
		/*获取监控指标列表*/		
		$this->data 	= D('Server')->get_chart_data(true , $living['0']['instance_id']);
		$this->living   = $living;
		$this->sum 		= $sum;
		$this->run 		= count($living);
		$this->expired  = $expired;
		$this->display();
	}

	/*
		@der 实例页面
	*/
	public function living(){
		/*查找出用户所有实例*/
		$field = 'id,cid,tid,instance_name,status,end';
		$where = array('uid' => $_SESSION['id'] , 'gid' => C('tenxunyun') , 'status' => array('NEQ',0));
		$data = M('Living')->field($field)->where($where)->select();
		//表名
		$dbName = M('Goodscat')->where(array('id' => $data['0']['cid']))->getField('tablename');
		$dbName = ltrim($dbName,'ali_');
		//组合数据
		$db = M($dbName);
		$field = 'intranet_ip,public_ip,region';
		foreach ($data as $key => $v) {
			$data[ $key ][ 'info' ] = $db->field($field)->where( array('id' => $v['tid']) )->find();
		}
		$this->dbName = $dbName;
		$this->living = $data;
		$this->display();
	}

	/*
		@der 获取实例详细信息
	*/
	public function getLivingInfo(){
		if ( !$this->ispost() ) redirect(__APP__);
		$dbName = I('dbName','');
		$tid   	= I('tid','','intval');

		$data 	= M($dbName)->field(true)->where(array('id' => $tid))->find();
		/*查找出镜像*/
		$db = M('mirroring');	
		$mirr = M('mirroring')->field('type,name,oid')->where(array('osid' => $data['osid']))->find();
		/*如果是操作系统则查找出系统名称*/
		if ( !$mirr['type']) {
			$mirr['name'] = M('Os')->where(array('id' => 'oid'))->getField('name');
		}
		
		$html  .= "<dl class='server-detail'><dt>镜像:</dt><dd>{$mirr['name']}</dd></dl>";
		$html  .= "<dl class='server-detail'><dt>CPU:</dt><dd>{$data['cpu']}核</dd></dl>";
		$html  .= "<dl class='server-detail'><dt>内存:</dt><dd>{$data['memory']}GB</dd></dl>";
		$html  .= "<dl class='server-detail'><dt>公网带宽:</dt><dd>{$data['band']}M</dd></dl>";
		$html  .= "<dl class='server-detail'><dt>系统盘:</dt><dd>{$data['disk']}GB</dd></dl>";
		//$html  .= "<dl class='server-detail'><dt>数据盘:</dt><dd>222</dd></dl>";
		
		echo $html;
	}	

	/*
		@der 返回图表所需要的数据
	*/
	public function chart_data(){
		if (!$this->isajax()) redirect(__APP__);

		D('Server')->get_chart_data();
	}

	/*
		@der 处理实例的操作
	*/
	public function livingOperation(){		
		if (!$this->ispost()) redirect(__APP__);
		/*重启*/
		echo D('Server')->server_restart();
		
	}

	/*
		@der 处理计时器的请求
	*/
	public function response_timer(){
		if ( !$this->isajax()) redirect(__APP__);
		/*改变实例状态*/
		echo D('Server')->change_status(I('post.status','2','intval'));
	}

	/*
		@der 服务器关机操作
	*/
	public function closeLiving(){
		if (!$this->ispost()) redirect(__APP__);

	}

	
}