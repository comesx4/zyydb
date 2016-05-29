<?php
/*
	@der 快照管理控制器
*/
Class SnapshotAction extends CommonAction{

	/*
		@der 快照列表
	*/
	public function index(){
		
		list($snap,$tmp,$page) = D('Cloud_snapView')->search();
		
		$this->snap  = $snap;
		$this->fpage = $page['1'];
		$this->tmp 	 = $tmp;
		$this->display();
	}

	/*
		@der 快照改名
	*/
	public function reName(){
		IS_POST|| redirect(__APP__);
		$snap_id = I('post.snap_id');
		if (empty($snap_id)) {
			$this->error('未选择实例');
		}

		$where    = array('id' => array('IN',implode(',' ,$snap_id)) );
		$setField = array('snap_name' => I('post.newName',''));
		$setField['snap_name'] || $this->error('名称不能为空');

		if (M('Cloud_snap')->where($where)->setField($setField)) {
			$this->success('修改成功',U('index'));
		} else {			
			$this->error('修改失败');
		}
	}

	/**
		@der 回滚快照
	*/
	public function rollbackSnap(){
		IS_POST|| redirect(__APP__);
		$_POST['id'] = I('post.snap_id');

		if (I('post.type')) {
			$code = decode($_COOKIE['checkEmail']);
		} else {
			$code = decode($_COOKIE['kz_'.$_SESSION['userinfo']['telephone']]);
		}

		if ( I('post.code') != $code || empty(I('post.code'))) {
			$this->error('验证码有误！'); die;
		}
		
		$result = D('Snapshot')->rollback_snap();

		if ($result['code'] == 0) {
			$this->success('回滚成功!',U('Server/index'));
		} else {
			$this->error($result['error']);
		}
	}

	/**
		@der 创建镜像
	*/
	public function createImage(){
		IS_POST|| redirect(__APP__);

		$result = D('Snapshot')->create_image();
		
		if ($result['code'] == 0) {
			$this->success('创建成功!',U('index'));
		} else {
			$this->error($result['error']);
		}
	}

	/**
		@der 异步获取快照状态
	*/
	public function getSnapStatus(){
		IS_POST|| redirect(__APP__);

		echo D('Snapshot')->get_snap_status();
	}

	/**
		@der 删除快照
	*/
	public function deleteSnap(){
		IS_POST|| redirect(__APP__);

		$result =  D('Snapshot')->delete_snap();

		if ($result['code'] == 0) {
			echo json_encode(array('status' => 1 ,'message' => '删除成功!'));
		} else {
			echo json_encode(array('status' => 0 ,'message' => '删除失败!'));
		}
	}
}