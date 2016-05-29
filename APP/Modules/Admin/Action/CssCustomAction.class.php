<?php

//定制产品属性样式控制器
Class CssCustomAction extends CommonAction{

	//样式列表
	public function index(){
	
		//如果单击了排序
        if(isset($_POST['submit'])){

	        	unset($_POST['submit']);
	        	$dbs=M('Csscustom');

	        	//循环修改排序
	        	foreach($_POST as $key=>$v){
	        		$where=array('id'=>$key);
	        		$dbs->where($where)->setField(array('sort'=>$v));
	        	}
        }
          $db=M('Csscustom');
		  //统计总条数 
          $sum=$db->count();       
      
        //导入分页类
        import('Class.Page',APP_PATH);

        $page=new Page($sum,10);
        $attr=$db->order('sort ASC')->limit($page->limit)->select();
      
		$this->css=$attr;
		$this->fpage=$page->fpage();
	

		$this->display();

	}

	//添加样式视图
	public function add(){
		$this->display();

	}

	//添加样式操作
	public function insert(){
		if(!$this->ispost()) $this->redirect('Index/index');
       

		//判断值是否为空
		if(empty($_POST['name']))
			$this->error('名称或内容不能为空');

		if(M('Csscustom')->data($_POST)->add())
			$this->success('添加成功',U('index'));
		else
			$this->error('添加失败请重试');

	}

	//修改视图
	public function update(){
		$id=$this->_get('id','intval');

		$this->attr=$attr=M('Csscustom')->where(array('id'=>$id))->find();
		
		$this->display();
	}


	//修改操作
	public function save(){
		if(!$this->ispost()) $this->redirect('Index/index');

		// 将内容在数据库中修改
		if(M('Csscustom')->save($_POST))
			$this->success('修改成功',U('index'));
		else
			$this->error('修改失败请重试');
	}

	
}