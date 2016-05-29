<?php
/*
  @der 服务与定制开发控制器
*/
Class ServiceAction extends Action{
     
     /*
       @der 主页
     */
     public function index(){
         $this->display();
     }


     /*
       @der 列表页
     */
     public function products(){
       
       $id=$this->_get('id','intval');
       $db=D('ServiceView');
       $where=array('service.cid'=>$id);
       //导入分页类
       $page=X($db,$where,10);
       
       //查找出该分类下面的所有服务
       $service=$db->where($where)->limit($page['0']->limit)->select();
       $this->service=$service;
       $this->fpage=$page['1'];
     	 $this->display();
     }


     /*
       @der 详细信息页面
     */
     public function datailed(){
        if(empty($this->_get('id'))) redirect(__APP__);
        $id=$this->_get('id','intval');
        $where=array('sid'=>$id);

        //查找出该环境的详细信息
        $service=D('ServiceView')->where(array('id'=>$id))->find();
         
        //查找出该服务的规格
        $spec=M('Service_spec')->field('id,spec')->where($where)->select();

        //查找该服务的介绍
        $this->introduce=htmlspecialchars_decode( M('Service_introduce')->where($where)->getField('introduce') );
        $this->spec=$spec;
        $this->service=$service;
     	  $this->display();
     }


     /*
       @der 异步获取服务价钱的方法
     */
    public function getPrice(){
        if(!$this->isAjax()) redirect(__APP__);
        $sid=$this->_post('sid','intval');

        //查找出该规格的价钱
        $price=M('Service_spec')->field('price,discount')->where(array('id'=>$sid))->find();

        echo json_encode($price);
    }

}