<?php
/**
 * @der 公司动态分类管理
 */
Class DynamicCatAction extends CommonAction{

	/**
 	 * @der 列表页
	 */
	public function index(){
		$db = M('Dynamic_cat');
		//排序
		cat_sort('Dynamic_cat');
		
		$data = $db->order('sort DESC')->select();

		$this->data = $data;
		$this->display();
	}

	/**
	 * @der添加视图
	 */
	public function add(){
		
		$this->display();
	}

	/**
	 * @der 添加操作
	 */
	public function insert(){
		IS_POST || redirect(__APP__);

		$add = array(
			'cat_name'    => I('post.cat_name',''),
			'sort'		  => I('post.sort',0,'intval')
			);

		if (M('Dynamic_cat')->add($add)) {
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
		$data  = M('Dynamic_cat')->where($where)->find();

		$this->data = $data;
		$this->display();
	}

	/**
	 * @der 修改操作
	 */
	public function save(){
		IS_POST || redirect(__APP__);

		$save = array(
			'cat_name'    => I('post.cat_name',''),
			'sort'		  => I('post.sort',0,'intval'),
			'id'		  => I('post.id','0','intval')

			);

		if (M('Dynamic_cat')->save($save)) {
			$this->success('修改成功',U('index'));
		} else {
			$this->error('修改失败');
		}
	}

	/**
	 * @der 删除操作
	 */
	public function delete(){
		$this->db_delete(M('Dynamic_cat'));
	}

}