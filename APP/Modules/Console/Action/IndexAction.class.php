<?php
/*
	@der 控制台主页控制器
*/
Class IndexAction extends CommonAction{

	/*
		@der 主页
	*/
	public function index(){
		
		$gid = M('Living')->field('gid')->where(array('uid'=>$_SESSION['id']))->select();
		$where = array('id'=>array( 'in',implode(',',peatarr($gid,'gid')) ));	
		$data = M('Goods')->field('id,goods')->where($where)->select();

		$this->display();
	}

}