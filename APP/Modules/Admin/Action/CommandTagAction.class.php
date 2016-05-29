<?php
/*
	@der 标签管理
*/
Class CommandTagAction extends CommonAction{

	/*
		@der 列表页
	*/
	public function index(){
		$db    = M('Command_tag');
		$page  = X($db,'',10);
		$field = 'id,type,name,color,status,remark,time';
		$data  = $db->limit($page['0']->limit)->select();

		$this->data  = $data;
		$this->fpage = $page['1'];
		$this->display();
	}

	/*
		@der 添加页面
	*/
	public function add(){
		
		$this->display();
	}

	/*
		@der 添加数据操作
	*/
	public function insert(){
		if ( !IS_POST ) redirect(__APP__);
		if (post_isnull('type','name','color','status')) {
			$this->error('请填写完整!');
		}
		$data = array('type'	=>I('post.type',1),
					  'name'	=>I('post.name',''),
					  'color'	=>I('post.color','#FFF'),
					  'status'	=>I('post.status',1),
					  'remark'	=>I('post.remark',''),
					  'time'	=>time()
					  );
		
		if(M('Command_tag')->add($data)) {
			$this->success('添加成功！',U('index'));
		} else {
			$this->error('添加失败！');
		} //if
	}

	/*
		@der 修改页面
	*/
	public function update(){
		$id = I('get.id',0,'intval');
		$where = array('id' => $id);
		$field = 'id,type,name,color,status,remark,time';
		$this->data = M('Command_tag')->field($field)->where($where)->find();

		$this->display();
	}

	/*
		@der 修改数据操作
	*/
	public function save(){
		if ( !IS_POST ) redirect(__APP__);
		if (post_isnull('type','name','color','status','id')) {
			$this->error('请填写完整!');
		}
		$data = array('type'	=>I('post.type',1),
					  'name'	=>I('post.name',''),
					  'color'	=>I('post.color','#FFF'),
					  'status'	=>I('post.status',1),
					  'remark'	=>I('post.remark',''),
					  'time'	=>time(),
					  'id'	 	=>I('post.id',0,'intval')
					  );
		
		if(M('Command_tag')->save($data)) {
			$this->success('编辑成功！',U('index'));
		} else {
			$this->error('编辑失败！');
		} //if		
	}

	/*
		@der 删除数据操作
	*/
	public function delete(){
		$this->db_delete(M('Command_tag'));
	}
}