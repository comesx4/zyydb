<?php
Class MirroringModel extends Model{

	/**
		@der 删除镜像
		@return json
	*/
	public function delete_image(){
		import('Class.Server',APP_PATH);
		$image_id = I('post.id');
		
		//验证改镜像是否属于该用户
		$where = array('id' => array('in', $image_id),'uid' => $_SESSION['id']);
		$image_count = M('Mirroring')->where($where)->count();

		if ($image_count != count($image_id)) {
			redirect(__APP__);
		}
		
		$image_id = explode(',', $image_id);

		foreach ($image_id as $v) {
			$result  = json_decode(Server::deleteImage($v),true);
		}

		return $result;
	}

	/*
		@der 处理改变实例状态额异步请求
		@param int $status [要修改的状态]
	*/
	public function get_status(){
	
		/*验证数据*/
		$id = I('post.id');

		/*请求接口，判断重启是否成功*/
		$where  = array('id' => array('IN',$id));
		$result = $this->field('id,status')->where($where)->select();
		
		$i 	  = 0;
		$data = array(); 
		foreach ($result as $v) {			
			//有数据时
			if ($result) {
				//状态改变成功的
				if ($v['status'] == 1) {
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

}