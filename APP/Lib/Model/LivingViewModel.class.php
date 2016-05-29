<?php
/*
	@der 实例表的视图模型
*/
Class LivingViewModel extends ViewModel{
	
	public $viewFields = array(
		'living' => array(
			'id','tid','gid','start','end','status','remark',
			'_type' => 'LEFT'
			),
		'goods' => array(
			'table_name','goods' => 'name',
			'_on'	=> 'living.gid = goods.id'
			)
		);


	/**
		@der 获取结果集
		@param array $where where条件 
		@return $result
	*/
	public function get_result($where = '' , $field = ''){
		$where || $where = array('id' => I('get.id',0,'intval'));
		$field || $field = 'cpu,memory,disk,band,city,osid';

        $goods = D('LivingView')->where($where)->find();
       
        $table = $goods['table_name'];
        $data  = M($table)->field($field)->where(array('id'=>$goods['tid']))->find();
    
        $data  = array_merge($goods,$data);

        return $data;
	}

	/**
		@der 判断产品是否审核 未审核则修改状态
		@param int $tid 产品id
	*/
	public function isCheck($tid){
		$where = array('tid' => $tid);
		$db    = M('living');
		if ($db->where($where)->getField('status') != 0) {
			return false;
		}
		//修改状态
		$setField = array(
			'status' => I('post.status','0','intval'),
			'remark' => I('post.remark')
			);
		$db->where($where)->setField($setField);
		
		return true;
	}

	/**
		@der 处理套餐云的审核修改
		@param int $gid 产品ID 
		@param int $gid 套餐云的ID 
		@return boolean
	*/
	public function packageCheck($gid,$id){
		switch ($gid) {
			case 19:  //高防云
				$setField = array(
					'wan_ip' => I('post.wan_ip')
					);
				break;
				
			case 20: //主机租用
				$setField = array(
					'wan_ip' 		   => I('post.wan_ip'),
					'cpu'	 		   => I('post.cpu'),
					'memory' 		   => I('post.memory'),
					'band'   		   => I('post.band'),
					'cabinet_position' => I('post.cabinet_position'),
					'cabinet_size'	   => I('post.cabinet_size'),
					'assets_code'	   => I('post.assets_code')
					);
				break;

			case 22: //存储云
				$setField = array(
					'wan_ip' => I('post.wan_ip')
					);
				break;

			case 23: //主机托管
				$setField = array(
					'wan_ip' 		   => I('post.wan_ip'),
					'cpu'	 		   => I('post.cpu'),
					'memory' 		   => I('post.memory'),
					'band'   		   => I('post.band'),
					'cabinet_position' => I('post.cabinet_position'),
					'cabinet_size'	   => I('post.cabinet_size'),
					'assets_code'	   => I('post.assets_code')
					);
				break;
		}
		$db    = M(C("goods{$gid}")); 
		$where = array('id' => $id);

		if ( $db->where($where)->save($setField) ) {
			return true;
		} else {
			//将状态修改回待审核
			$db->where(array('tid' => $id))->setField(array('status' => 0));
		
			return false;
		}
	}
}