<?php
/**
	@der 任务日志	
*/
Class TaskLogsAction extends CommonAction{

	/**
		@der 列表
	*/
	public function index(){
		$db    = M('Task_logs');
		list($logs,$tmp,$page) = $this->search($db);
		
		$this->logs  = $logs;
		$this->fpage = $page['1'];
		$this->tmp   = $tmp;
		$this->display();
	}

	/**
	 *	@der 日志删除
	 */
	public function deleteLogs(){
		IS_POST || redirect(__APP__);
		$where    = array('id' => array('IN',I('post.id') ) ,'uid' => $_SESSION['id']);
		$setField = array('is_show' => 0);
		
		if ( M('Task_logs')->where($where)->setField($setField) ){
			echo json_encode(array('status' => 1 ,'message' => '删除成功!'));
		} else {
			echo json_encode(array('status' => 0 ,'message' => '删除失败!'));
		}
	}

	/*
		@der 处理搜索
	*/
	private function search($db){
		$tmp    = $_REQUEST; 
		$where = array('uid' => $_SESSION['id'] , 'is_show' => 1);
		$pwhere = array();
		
		//如果有搜索
		if (isset($tmp['submit'])) {
			$pwhere['submit'] = 1;
			
			// 状态
			if (!empty($tmp['status'])) {
				$tmp['status'] != -1 ? $status = $tmp['status'] : $status = 0;
				$where['status']  = $status;				
				$pwhere['status'] 			 = $tmp['status'];
			}

			//类型
			if (!empty($tmp['type']) && $tmp['type'] != 'null') {
				$where['type']  = $tmp['type'];
				$pwhere['type'] = $tmp['type'];
			}

			//开始时间
			if (!empty($tmp['min_date'])) {
				$date 				= strtotime($tmp['min_date']);	 
				$where['time']  	= array('EGT',$date);
				$pwhere['min_date'] = $tmp['min_date'];
			}

			//结束时间
			if (!empty($tmp['max_date'])) {
				$date 				= strtotime($tmp['max_date']);	 
				$where['time']  	= array('ELT',$date);
				$pwhere['max_date'] = $tmp['max_date'];
			}

		}

		//分页
		$page = X($db,$where,C('page_num'),$pwhere);
		$data = $db->where($where)->limit($page['0']->limit)->order('id DESC')->select();

		return array($data,$tmp,$page);
	}
}