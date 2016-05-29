<?php
/**
	@der 镜像
*/
Class MirroringAction extends CommonAction{

	/**
		@der 镜像列表
	*/
	public function index(){
		list($data,$tmp,$page)  = D('MirroringView')->search();
		
		$this->mirroring = $data;
		$this->tmp 		 = $tmp;
		$this->page  	 = $page;
		$this->display();
	}

	/*
		@der 镜像改名
	*/
	public function reName(){
		IS_POST|| redirect(__APP__);
		$id = I('post.id');
		if (empty($id)) {
			redirect(__APP__);
		}

		$where    = array('id' => array('IN',implode(',' ,$id)) );
		$setField = array('name' => I('post.newName',''));
		$setField['name'] || $this->error('名称不能为空');

		if (M('Mirroring')->where($where)->setField($setField)) {
			$this->success('修改成功',U('index'));
		} else {			
			$this->error('修改失败');
		}
	}

	/**
		@der 删除镜像
	*/
	public function deleteImage(){
		IS_POST|| redirect(__APP__);

		$result =  D('Mirroring')->delete_image();

		if ($result['code'] == 0) {
			echo json_encode(array('status' => 1 ,'message' => '删除成功!'));
		} else {
			echo json_encode(array('status' => 0 ,'message' => '删除失败!'));
		}
	}

	/**
		@der 异步获取状态
	*/
	public function getStatus(){
		IS_POST|| redirect(__APP__);

		echo D('Mirroring')->get_status();
	}
}