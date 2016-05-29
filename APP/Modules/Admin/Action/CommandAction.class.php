<?php
/*
	@der 命令管理
*/
Class CommandAction extends CommonAction{

	/*
		@der 列表页
	*/
	public function index(){
		$page  = X(M('Command'),'',30);
		$field = array('id','name','title','rule','remark','status','time');
		$data  = D('CommandRelation')->relation('tag')->field($field)->limit($page['0']->limit)->select();

		$this->data  = $data;
		$this->fpage = $page['1'];
		$this->display();
	}

	/*
		@der 添加页面
	*/
	public function add(){

		$checkbox  = $this->tag_checkbox(['type' => 1 ]);
		$checkbox2 =$this->tag_checkbox(['type' => 2 ]);
		
		$this->checkbox  = $checkbox;
		$this->checkbox2 = $checkbox2;
		$this->display();
	}

	/*
		@der 添加数据操作
	*/
	public function insert(){
		if ( !IS_POST ) redirect(__APP__);
		
		$data = array('name'	=>I('post.name',''),
					  'title'	=>I('post.title',''),
					  'rule'	=>I('post.rule','',''),
					  'remark'	=>I('post.remark','',''),
					  'status'	=>I('post.status',1),
					  'time'	=>time()
					  );

		$data['rule'] 	= str_replace(array("\t"),array('    '),$data['rule']);
		$data['remark'] = str_replace(array("\t"),array('    '),$data['remark']);

		if($commandId = M('Command')->add($data)) {

			$this->set_tag($commandId);

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
		$data = D('CommandRelation')->where(['id' => $id])->relation('articles')->find();
		$pid = peatarr($data['articles'],'command_tag_id');
		
		$this->data = $data;
		$this->checkbox  = $this->tag_checkbox(['type' => 1 ],$pid);
		$this->checkbox2 = $this->tag_checkbox(['type' => 2 ],$pid);
		$this->display();
	}

	/*
		@der 修改数据操作
	*/
	public function save(){
		if ( !IS_POST ) redirect(__APP__);
		$data = array('name'	=>I('post.name',''),
				  'title'	=>I('post.title',''),
				  'rule'	=>I('post.rule','',''),
				  'remark'	=>I('post.remark','',''),
				  'status'	=>I('post.status',1),
				  'time'	=>time(),
				  'id'	    =>I('post.id',0,'intval')
				  );

		$data['rule'] 	= str_replace(array("\t"),array('    '),$data['rule']);
		$data['remark'] = str_replace(array("\t"),array('    '),$data['remark']);

		if( M('Command')->save($data)) {

			$this->set_tag($data['id'],true);

			$this->success('修改成功！',U('index'));
		} else {
			$this->error('修改失败！');
		} //if
		
	}

	/*
		@der 删除数据操作
	*/
	public function delete(){
		$id = I('get.id','0','intval');

		if (M('Command')->where(['id' => $id])->delete()) {
			/*删除中间表关系*/
			M('Command_and_tag')->where(['command_id' => $id])->delete();
	
			$this->success('删除成功！',U('index'));
		} else {
			$this->error('删除失败!');
		}
	}

	/*
		@der 将标签添加到中间表
		@param int $command_id 命令表ID
		@param boolean $isDel 是否删除之前的关系 true删除 false 否
	*/
	private function set_tag(int $command_id ,$isDel = false){
		$db = M('Command_and_tag');
		/*删除中间表关系*/
		if ($isDel) {
			$db->where(['command_id' => $command_id])->delete();
		}

		$tags 	= array();
		$tag_id = I('post.tag_id');
		foreach($tag_id as $value){
			if($value) {
				$tags[] = array('command_id'	=>$command_id,
								'command_tag_id'=>$value
							);	
			}//if
		}//foreach
		
		return $db->addAll($tags);
	}

	/*
		@der 组合标签复选框
		@param array $where where条件
		@param array $pid 需要勾选的复选框ID
		@param string $name 复选框的name
	*/
	private function tag_checkbox(array $where,$pid = 0, $name = 'tag_id[]'){
		$where ? array_merge($where,['status' => 1]) : ['status' => 1];
		$str = '';
		$data = M('Command_tag')->field('status',true)->where($where)->select();
		$checked = '';
		foreach ($data as $v) {
			if ($pid) {
				if (in_array($v['id'], $pid)) {
					$checked = 'checked';
				} else {
					$checked = '';
				}
			}
			$str .= "<label style='color:{$v['color']};padding:0 4px;' for='{$v['id']}'>
						<input {$checked} id='{$v['id']}' style='position:relative;top:5px;padding:0 4px;' type='checkbox'  name='{$name}' value='{$v['id']}'/>{$v['name']}
					</label>";
		}

		return $str;
	}
}