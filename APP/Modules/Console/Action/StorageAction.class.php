<?php
/**
	@der 存储云
*/
Class StorageAction extends CommonAction{

	/**
		@der 存储云列表
	*/
	public function index(){
		$db	   = D('Storage_serverView');
		$where = array('uid' => $_SESSION['id'] , 'gid' => C('storage_yun') , 'status' => array('NEQ',0) ,'isdelete' => 0);
		$data  = $db->where($where)->order('id DESC')->select();
		
		$this->data = $data;
		$this->display();
	}
}