<?php
/*
	@der 公司公告
*/
Class NoticeAction extends CommonAction{

	/**
 	 * @der 列表页
	 */
	public function index(){
		$db = D('NoticeView');

		//排序
		cat_sort('Notice');
		$data = $db->order('sort DESC')->select();

		$this->data = $data;
		$this->display();
	}

	/**
	 * @der添加视图
	 */
	public function add(){

		$this->content = ueditor1();
		$this->display();
	}

	/**
	 * @der 添加操作
	 */
	public function insert(){
		IS_POST || redirect(__APP__);

		$add = array(
			'title'   	  => I('post.title',''),
			'content'     => I('post.content',''),
			'uid'	  	  => $_SESSION['uid'],
			'create_time' => $_SERVER['REQUEST_TIME'],
			'sort'		  => I('post.sort',0,'intval')
			);

		if (M('Notice')->add($add)) {
			$this->success('添加成功',U('index'));
		} else {
			
			$this->error('添加失败');
		}
	}

	/**
	 * @der 修改视图
	 */
	public function update(){
		$where = array('id' => I('get.id',0,'intval'));
		$data  = M('Notice')->where($where)->find();

		$this->data    = $data;
		$this->content = ueditor1();
		$this->display();
	}

	/**
	 * @der 修改操作
	 */
	public function save(){
		IS_POST || redirect(__APP__);

		$save = array(
			'title'   	  => I('post.title',''),
			'cid'	  	  => I('post.cid',0,'intval'),
			'source'  	  => I('post.source'),
			'content'     => I('post.content',''),
			'uid'	  	  => $_SESSION['uid'],
			'create_time' => $_SERVER['REQUEST_TIME'],
			'id'		  => I('post.id','0','intval'),
			'sort'		  => I('post.sort',0,'intval')

			);

		if (M('Notice')->save($save)) {
			$this->success('修改成功',U('index'));
		} else {
			$this->error('修改失败');
		}
	}

	/**
	 * @der 删除操作
	 */
	public function delete(){
		$this->db_delete(M('Notice'));
	}


}