<?php
/*
	@der 查看腾讯云参数控制器
*/
Class TenxunyunAction extends CommonAction{

	/*
		@der 用户可用镜像列表
	*/
	public function index(){
		$type = !empty($_POST['type']) ? I('post.type','2','intval') : 2;
		
		/*缓存*/
		if ( !$data = S("tenxun_mirroring{$type}") ) {
			import('Class.Tenxunyun',APP_PATH);
			$data = Tenxunyun::sendRequest(array('imageType'=>$type),'DescribeImages','image.api.qcloud.com');
			S("tenxun_mirroring{$type}",$data,86400);
		}
		
		$this->data = $data;
		$this->display();
	}

	/*
		@der 监控参数列表
	*/
	public function describeMetrics(){
		/*缓存*/
		if ( !$data = S("tenxun_describeMetrics") ) {
			import('Class.Tenxunyun',APP_PATH);
			$data = Tenxunyun::sendRequest(array('namespace'=>'qce/cvm'),'DescribeMetrics','monitor.api.qcloud.com');
			S("tenxun_describeMetrics",$data,86400);
		}
		
		$this->data = $data;
		
		$this->display();
	}

	/*
		@der 所有实例列表
	*/
	public function all_living(){
		import('Class.Tenxunyun',APP_PATH);
		$data = Tenxunyun::sendRequest('','DescribeInstances','cvm.api.qcloud.com');
		
		$this->data = $data;
		$this->display();
	}
}