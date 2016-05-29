<?php
/**
	@der 后台操作记录
*/
Class AdminDetailAction extends CommonAction{

	/**
		@der 列表页
	*/
	public function index(){
		$db = D('Admin_detailView');
		
		//调用Model中的方法处理搜索
        list($where,$pwhere,$tmp)=$db->search($where);
      
		$page = X(M('Admin_detail'),$where, C('PAGE_NUM'),$pwhere);
		$data = $db->where($where)->order('id DESC')->limit($page['0']->limit)->select();
		
		$this->tmp 	 = $tmp;
		$this->fpage = $page['1'];
		$this->data = $data;
		$this->display();
	}

	/*
		@der 删除操作
	*/
	public function delete(){
		
		if (isset($_POST['del'])) {
			$where = array('id' => array('IN',implode(',' , I('post.id'))) );
		} else {
			$where = array('id'=>I('get.id',0,'intval'));
		}

		$this->db_delete(M('Admin_detail') , $where);
	}

}