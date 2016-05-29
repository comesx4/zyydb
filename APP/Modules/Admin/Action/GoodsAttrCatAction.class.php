<?php
/*
	@产品属性分类控制器(此控制器无意义)
*/
Class GoodsAttrCatAction extends CommonAction{

	//属性分类列表
	public function index(){
		//如果单击了排序
        if(isset($_POST['submit'])){
	        	unset($_POST['submit']);
	        	$db=M('Attrcat');

	        	//循环修改排序
	        	foreach($_POST as $key=>$v){
	        		$where=array('id'=>$key);
	        		$db->where($where)->setField(array('sort'=>$v));
	        	}
        }


		$this->cat=D('AttrcatView')->order('gid ASC')->select();
		
		$this->display();
	}

	//添加属性分类视图
	public function add(){
		//取得产品列表
		$this->goods=goodsList();
		$this->display();

	}

	//插入属性分类到数据库
	public function insert(){
		if(!$this->ispost()) $this->redirect('Index/index');
		

        //验证数据是否为空
		if(empty($_POST['attrcat']))
			$this->error('属性分类名不能为空');

		if(M('Attrcat')->data($_POST)->add())
			$this->success('添加成功',U('index'));
		else
			$this->error('添加失败');

	}

	//属性分类修改视图
	public function update(){
		$id=$this->_get('id','intval');
		$this->attr=$attr=M('Attrcat')->where(array('id'=>$id))->find();
		//取得产品列表
		$this->goods=goodsList('gid',$attr['gid']);
		$this->display();
	}

	//属性分类修改操作
	public function save(){

	}

}