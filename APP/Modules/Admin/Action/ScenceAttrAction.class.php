<?php
/*
  工单属性控制器
*/
Class ScenceAttrAction extends CommonAction{

	/*
      工单属性列表
	*/
    public function index(){

        //调用排序函数
        cat_sort('Scenceattr');
        
        //查找出所有属性
        $data=D('ScenceAttrView')->order('sort DESC')->select();

        $this->data=$data;

    	$this->display();
    }

    /*
      添加工单属性视图
    */
    public function add(){
    	$this->cat=MirroringSelect('cid',0,'Scencecat');  //分类下拉列表
    	$this->display();
    }

    /*
      添加工单属性操作
    */
    public function insert(){
    	if(!$this->ispost()) redirect(__APP__);
     
        $add=array();

        //组合添加的内容
        foreach($_POST['name'] as $v){
               $add[]=array(
                    'cid'=>$this->_post('cid','intval'),
                    'type'=>$this->_post('type','intval'),
                    'must'=>$this->_post('must','intval'),
                    'sort'=>$this->_post('sort','intval'),
                    'name'=>$v,
                    'title'=>$this->_post('title')
                );
        }
        
        if(M('Scenceattr')->addAll($add)){
            $this->success('添加成功',U('add'));

        }else{
            echo M('Scenceattr')->getLastSql();
            $this->error('添加失败，请重试!');
        }

    }

    /*
      修改工单属性视图
    */
    public function update(){
        $where=array('id'=>$this->_get('id','intval'));
        //查找出该工单的属性
        $data=M('Scenceattr')->where($where)->find();
        
        //分类下拉列表
        $this->cat=MirroringSelect('cid',$data['cid'],'Scencecat');

        $this->data=$data;


    	$this->display();
    }

    /*
      修改工单属性操作
    */
    public function save(){
        if(!$this->ispost()) redirect(__APP__);

        if(M('Scenceattr')->save($this->_post())){
             
             $this->success('修改成功',U('index'));

        }else{
             $this->error('修改失败');

        }
    }

    /*
     工单属性的删除操作
    */
    public function del(){
        if(empty($_GET['id'])) redirect(__APP__);

        $where=array('id'=>$this->_get('id','intval'));
        
        if(M('Scenceattr')->where($where)->delete()){
             
             $this->success('删除成功',U('index'));

        }else{
             $this->error('删除失败!');

        }

    }

}