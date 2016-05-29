<?php
//产品打折管理
Class SaleAction extends CommonAction{

	//打折列表
	public function index(){
		$this->sale=M('Sale')->query("select s.id as id,s.month as month,s.sale as sale,g.goods as goods from kz_sale s LEFT JOIN kz_goods g ON s.gid=g.id  ");
		
		$this->display();


	}

	//添加打折视图
    public function add(){
    	$this->goods=goodsList();
    	$this->display();

    }
 
    //添加打折操作
    public function insert(){
    	if(!$this->ispost()) redirect(__APP__);

    	if(empty($_POST['month'])||empty($_POST['sale']))
    		$this->error('值不能为空');

    	if(M('Sale')->data($this->_post())->add())
    		$this->success('添加成功',U('index'));
    	else
    		$this->error('添加失败，请重试!');

    }

    //修改视图
    public function update(){
        $sale=M('Sale')->where(array('id'=>$this->_get('id','intval')))->find();
        $this->goods=goodsList('gid',$sale['gid']);
        $this->sale=$sale;
        $this->display();

    }

    //修改操作
    public function save(){
        if(!$this->ispost()) redirect(__APP__);
        
        if(M('Sale')->save($this->_post()))
            $this->success('修改成功',U('index'));
        else
            $this->error('修改失败，请重试!');

    }

    //删除
    public function delete(){
        $this->db_delete(M('Sale'));
    }
}