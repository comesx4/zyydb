<?php
/*
	@der 主机租用托管套餐设置
*/
Class RentPackageAction extends CommonAction{

	/*
		@der 列表页
	*/
	public function index(){
		//$db   = M(C('rent'));
                $db=D('RentView');
		//调用排序函数
		cat_sort(C('rent'));
		$page = X($db);
		$data = $db->field(true)->order('sort DESC')->limit($page['0']->limit)->select();
		
		$this->data  = $data;
		$this->fpage = $page['1'];
		$this->display();
	}

	/*
		@der 添加页面
	*/
	public function add(){
	$this->zytypelist = FLFParameter::getStatusList('zytype', 'Zytype');
        $this->linelist = FLFParameter::getStatusList('LineType', 'LineList');
        $this->oplist = show_list('os', 'os', 'name');
        $this->online = FLFParameter::getStatusList('online', 'Online',0, 1);
		$this->display();
	}

	/*
		@der 添加数据操作
	*/
	public function insert(){
		if ( !IS_POST ) redirect(__APP__);
		post_isnull('band','price','cpu','memory','disk') && $this->error('请全部填写完整');

//		$add = array(
//			'name'		 => I('post.name',''),
//			'band' 		 => I('post.band','','intval'),
//			'price'	     => I('post.price'),
//			'sort'	     => I('post.sort',1,'intval'),
//			'memory'	 => I('post.memory','','intval'),
//			'cpu'		 => I('post.cpu','','intval'),
//			'disk'		 => I('post.disk','','intval')
//			);
//		if (M(C('rent'))->add($add)) {
                if (M(C('rent'))->add(I('post.'))) {
			$this->success('添加成功',U('index'));
		} else {			
		
			$this->error('添加失败');
		}
	}

	/*
		@der 修改页面
	*/
       public function update() {
        $id = I('get.id', 0, 'intval');
        $data = M(C('rent'))->field(true)->where(array('id' => $id))->find();
        
        $this->zytypelist = FLFParameter::getStatusList('zytype', 'Zytype', $data['zytype']);
        $this->linelist = FLFParameter::getStatusList('LineType', 'LineList', $data['LineType']);
        $this->oplist = show_list('os', 'os', 'name', $data['os']);
        $this->online = FLFParameter::getStatusList('online', 'Online', $data['online'],1);
        $this->data = $data;
        $this->display();
    }

    /*
		@der 修改数据操作
	*/
	public function save() {
        if (!IS_POST)
            redirect(__APP__);
        post_isnull('id', 'band', 'price', 'cpu', 'memory', 'disk') && $this->error('请全部填写完整');

//		$save = array(
//			'name'		 => I('post.name',''),
//			'band' 		 => I('post.band','','intval'),
//			'price'	     => I('post.price'),
//			'sort'	     => I('post.sort',1,'intval'),
//			'memory'	 => I('post.memory','','intval'),
//			'cpu'		 => I('post.cpu','','intval'),
//			'disk'		 => I('post.disk','','intval'),
//			'id'		 => I('post.id','0','intval')
//			);
//		if (M(C('rent'))->save($save)) {
        if (M(C('rent'))->save(I('post.'))) {
            $this->success('修改成功', U('index'));
        } else {
            $this->error('修改失败');
        }
    }

    /*
		@der 删除数据操作
	*/
	public function delete(){
		$this->db_delete(M(C('rent')));
	}

}