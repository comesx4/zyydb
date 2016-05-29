<?php
/*
	@der 磁盘管理
*/
Class DiskAction extends CommonAction{

	/*
		@der 磁盘列表
	*/
	public function index(){
		list($disk,$tmp,$page) = D('Cloud_diskView')->search();

		$this->fpage = $page['1'];
		$this->disk  = $disk;
		$this->tmp   = $tmp;
		$this->display();
	}

	/*
		@der 创建快照
	*/
	public function createSnap(){
		!IS_POST && redirect(__APP__);

		echo D('Disk')->create_snap();
	}

	/*
		@der 云磁盘改名
	*/
	public function reName(){
		IS_POST|| redirect(__APP__);
		$disk_id = I('post.disk_id');
		if (empty($disk_id)) {
			$this->error('未选择实例');
		}

		$where    = array('id' => array('IN',implode(',' ,$disk_id)) );
		$setField = array('disk_name' => I('post.newName',''));
		$setField['disk_name'] || $this->error('名称不能为空');

		if (M('Cloud_disk')->where($where)->setField($setField)) {
			$this->success('修改成功',U('index'));
		} else {
			$this->error('修改失败');
		}
	}
}