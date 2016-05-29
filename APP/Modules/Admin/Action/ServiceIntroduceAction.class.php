<?php
/*
  @der 服务介绍控制器
*/
Class ServiceIntroduceAction extends CommonAction{
     
     /*
       @der 列表页面
     */
    public function index(){
    	//调用排序函数
		cat_sort('Service_introduce');

    	$data=D('Service_introduceView')->order('sort DESC')->select();
    	
    	$this->data=$data;
    	$this->display();
    }


     /*
      @der 添加服务 视图
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

        if(M('Service_introduce')->add($this->_post())){
             $this->success('添加成功',U('index'));
        }else{
             $this->error('添加失败，请重试！');
        } 
    }


    /*
      @der 修改服务 视图
    */
    public function update(){
    	
    	$where=array('id'=>$this->_get('id','intval'));
    	$data=M('Service_introduce')->where($where)->find();
    	$this->service=show_list('Service','sid','title',$data['sid']);
    	$this->data=$data;
       
        $this->display();
    }

    /*
     @der 修改操作
    */
    public function save(){
        if(!$this->ispost()) redirect(__APP__);
        
        if ( M('Service_introduce')->save($this->_post()) ) {
        	$this->success('修改成功',U('index'));
        } else {
        	$this->error('修改失败!');
        }
    }


    /*
     @der 删除操作
    */
    public function del(){
    	if(empty($_GET['id'])) redirect(__APP__);
        $where = array('id'=>$this->_get('id','intval'));

        if ( M('Service_introduce')->where($where)->delete() ) {
            $this->success('删除成功',U('index'));
        } else {
            $this->error('删除失败!');
        }
    }
}