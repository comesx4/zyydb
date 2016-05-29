<?php
/*
	@der 机房区域管理控制器
*/
Class MuRegionAction extends CommonAction{

	/*
		@der 机房区域列表
	*/
	public function index(){
		$db    = M('Mu_region');
		$page  = X($db);
		$field = array('id,region_name,region_code,jifang,remark,time');
		$data  = $db->field($field)->limit($page['0']->limit)->select();
		
		$this->fpage = $page['1'];
		$this->data  = $data;
		$this->display();
	}

	/*
		@der 添加机房区域视图
	*/
	public function add(){

		//城市下拉列表
		$this->city = show_list('City','city_id','city',0);
		$this->display();
	}

	/*
		@der 添加机房区域操作
	*/
	public function insert(){
		if (!IS_POST) redierct(__APP__);
		if ( post_isnull('region_code','jifang','jifang') ) {
			$this->error('请填写完整');
		}
		$data = array('region_name'	=>I('region_name',''),
					  'region_code'	=>I('region_code',''),
					  'jifang'		=>I('jifang',''),
					  'jifang'		=>I('jifang',''),
					  'status'		=>I('status',1),
					  'remark'		=>I('remark',''),
					  'city_id'		=>I('city_id',1,'intval'),
					  'time'		=>time()
					  );
		
		if(M('Mu_region')->add($data)) {
			$this->success('添加成功！',U('index'));
		} else {
			$this->error('添加失败！');
		}
	}

	/*
		@der 修改机房区域视图
	*/
	public function update(){
		$muserverId = I('muregion_id',0,'intval');
		
		$field = array('id,city_id,region_name,region_code,jifang,status,remark,time');
		$where = array('id'=>$muserverId);
		$data  = M('Mu_region')->field($field)->where($where)->find();
	
		//区域下拉列表
		$this->data = $data;
		$this->city = show_list('City','city_id','city',$data['city_id']);
		
		$this->display();
	}

	/*
		@der 修改机房区域操作
	*/
	public function save(){
		if (!IS_POST) redierct(__APP__);
		if ( post_isnull('muregion_id','region_code','jifang','lan_netmask','lan_gateway','wan_netmask','wan_gateway','jifang') ) {
			$this->error('请填写完整');
		}

		$muserverId = I('post.muregion_id',0,'intval');
	
		$data = array('id'			=>$muserverId,
					  'region_name'	=>I('region_name',''),
					  'region_code'	=>I('region_code',''),
					  'jifang'		=>I('jifang',''),
					  'jifang'		=>I('jifang',''),
					  'status'		=>I('status',1),
					  'remark'		=>I('remark',''),
					  'city_id'		=>I('city_id',1,'intval'),
					  'time'		=>time());
		
		if(M('Mu_region')->save($data)) {
			$this->success('编辑成功！',U('index'));
		}else{			
			$this->error('编辑失败！');
		}
	}

	/*
		@der 机房区域删除操作
	*/
	public function delete(){

		$muserverId = I('get.muregion_id',0,'intval');
		$where = array('id' => $muserverId);
		$this->db_delete(M('Mu_region'),$where);
	}
}