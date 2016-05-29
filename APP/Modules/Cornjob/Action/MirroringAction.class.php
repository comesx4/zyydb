<?php
/*
	@der 镜像计划任务
*/
Class MirroringAction extends CommonAction{

	/**
		@der 处理创建镜像
	*/
	public function index(){
		import('Class.Server',APP_PATH);
		$data = M('Mirroring_plan','kz_','DB_DSN2')->field('mirroring_id')->select();

		if (!$data) {
			return false;
		}

		foreach ($data as $v) {
			Server::checkImage($v['mirroring_id']);
		} 
		
	}
}