<?php
/*
  @dr 服务规格控制器
*/
Class ServiceSpecAction extends CommonAction{

	 /*
      @der 列表页面
	 */
    public function index(){
        $data=D('Service_specView')->select();
        $this->data=$data;	

    	$this->display();
    }

    /*
      @der 添加视图
    */
    public function add(){
        
        //服务列表
        $this->service=show_list('Service','sid','title');
    	$this->display();

    }

    /*
      @der 添加操作
    */
    public function insert(){
    	if(!$this->ispost()) redirect(__APP__);

    	if(M('Service_spec')->add($this->_post())){
            $this->success('添加成功',U('add'));
    	}else{
            $this->error('添加失败');
    	}

    }


    /*
      @der 修改视图
    */
    public function update(){
        
        $spec=M('Service_spec')->where(array('id'=>$this->_get('id','intval')))->find();
        
        //服务列表
        $this->service=show_list('Service','sid','title',$spec['sid']);
        $this->data=$spec;
        $this->display();
    }


    /*
     @der 修改操作
    */
    public function save(){
        if(!$this->ispost()) redirect(__APP__);

        if( M('Service_spec')->save($this->_post()) ) {
            $this->success('修改成功',U('index'));
        } else {
            $this->error('修改失败');
        }
    }

    /*
     @der 删除操作
    */
    public function del(){
         $this->db_delete(M('Service_spec'));
    }


}