<?php
Class SnapshotModel extends Model{

	/**
	 * 	@der 回滚快照
	 */
	public function rollback_snap(){
		//验证该快照是否属于该用户
		$where = array('id' => I('post.snap_id',0,'intval'),'uid' => $_SESSION['id']);
		$snap_count = M('Cloud_snap')->where($where)->count();

		if (!$snap_count) {
			redirect(__APP__);
		}	

		//发送回滚请求
		import('Class.Server',APP_PATH);
		$data   = array('snap_id' => I('post.snap_id',0,'intval'));
		$result = json_decode(Server::rollback($data) , true);

		return $result;
	}

	/**
     *	@der 创建快照
     *
	 */
	public function create_image(){
		
		$data = array(
			'image_name' => I('post.newName','未命名'),
			'snap_id'	 => I('post.snap_id',0,'intval'),
			'uid'		 => $_SESSION['id']
			);

		import('Class.Server',APP_PATH);
		$result = json_decode(Server::createImage($data) ,true);

		return $result;
	}

	/*
		@der 处理改变实例状态额异步请求
		@param int $status [要修改的状态]
	*/
	public function get_snap_status(){
		$db = M('Living');
		/*验证数据*/
		$snap_id = explode(',',I('post.id'));

		/*请求接口，判断重启是否成功*/
		$result = array();
		
		import('Class.Server',APP_PATH);
		foreach ($snap_id as $key => $v) {
			$result[$key] 		= json_decode(Server::querySnapTask($v),true);
			$result[$key]['id'] = $v;
		}
		
		$i 	  = 0;
		$data = array(); 
		foreach ($result as $v) {			
			//请求成功时
			if ($v['code'] == 0) {
				//状态改变成功的
				if ($v['result']['status'] == 1) {
					
					$data[] = $v['id'];
					$i++;
				}	
			}
		}
		
		if ($i != 0) {
			if ($i == $count) {
				//全部修改成功
				return json_encode(array('status' => 2 , 'id' => $data));
			} else {
				//部分修改成功
				return json_encode(array('status' => 1 , 'id' => $data));
			}
			
		} else {
			//无修改成功
			return json_encode(array('status' => 0 , 'message' => '哎呀，出错啦！'));
		}
	}

	/**
		@der 删除快照
		@return json
	*/
	public function delete_snap(){
		import('Class.Server',APP_PATH);
		$image_id = I('post.id',0);
		
		//验证该快照是否属于该用户
		$where = array('id' => array('IN', $image_id),'uid' => $_SESSION['id']);
		$image_count = M('Cloud_snap')->where($where)->count();
		
		if ($image_count != count($image_id)) {
			redirect(__APP__);
		}
		
		$image_id = explode(',', $image_id);
		foreach ($image_id as $v) {

			$result  = json_decode(Server::deleteSnap($v),true);
		}

		return $result;
	}

	
}