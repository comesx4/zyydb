<?php
Class DiskModel extends Model{

	/*	
		@der 创建快照
	*/
	public function create_snap(){
		//查找出磁盘标识
		$where = array('id' => I('post.id',0,'intval') , 'uid' => $_SESSION['id']);
		$disk_code = M('Cloud_disk')->where($where)->getField('disk_code');
		//发送创建接口请求
		import('Class.Server',APP_PATH);
		$send   = array(
			'disk_code' => $disk_code , 
			'uid' 		=> $_SESSION['id'],
			'snap_name' => I('post.snap_name','')
			);
		$result = json_decode(Server::createSnapshot($send) , true);

		if ($result['code'] != 0) {
			return json_encode(array('status' => 0 , 'message' => $result['error']));
		}

		return json_encode(array('status' => 1 , 'message' => '创建成功'));
	}
}