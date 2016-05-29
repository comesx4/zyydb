<?php
/*
 @der RBAC的无限分类控制器
*/
Class NodecatAction extends CommonAction{

	/*
	分类列表
	*/
	public function index(){

		//调用排序函数
		cat_sort('Nodecat');
		
		$this->data=Mirroringcat('Nodecat');
		$this->display();
	}

	/*
	添加分类视图
	*/
	public function add(){
		
		//判断有没有添加子分类操作
		if(isset($_GET['id'])){

			$this->cat=MirroringSelect('pid',$this->_get('id','intval'),'Nodecat');

		}else{
			$this->cat=MirroringSelect('pid',0,'Nodecat');
		}
		$this->display();
	}

	/*
	添加分类操作
	*/
	public function insert(){
		if(!$this->ispost()) redirect(__APP__);

		$arr=array();
		foreach(I('post.cat') as $v){
			$arr[]=array('class'=>I('post.class'),//$this->_post('class'),
                            'cat'=>$v,
                            'pid'=>I('post.pid'),//$this->_post('pid','intval'),
                            'sort'=>I('post.sort'),//$this->_post('sort','intval'),
                            'status'=>I('post.status'));
		}	

	    if(M('Nodecat')->addAll($arr)){

              $this->success('添加成功',U('add'));
	    }else{
              $this->error('添加失败');
	    }



	}

	 /*
	 分类的修改视图
	 */
     public function update(){
     	$id=$this->_get('id','intval');

     	//查找出该分类的信息
     	$this->data=M('Nodecat')->where(array('id'=>$id))->find();

     	$this->cat=MirroringSelect('pid',$this->data['pid'],'Nodecat');

     	
     
     	$this->display();
     }

     /*
     分类修改操作
     */
     public function save(){
     	if(!$this->ispost()) redirect(__APP__);
     	
     	if(M('Nodecat')->setField($this->_post()))
     		$this->success('修改成功',U('index'));
     	else
     		$this->error('修改失败');
     }


	/*
     分类的删除操作
    */
     public function del(){
     	$id=$this->_get('id','intval');

     	$db=M('Nodecat');

     	//判断该分类下有没有子分类
     	if($demo=$db->where(array('pid'=>$id))->getField('id')){     	
     		$this->error('该分类下有子分类，请先删除子分类');
     	}

     	if($db->where(array('id'=>$id))->delete()){
             $this->success('删除成功',U('index'));
     	}else{
             $this->error('删除失败');
     	}
     }


    /*
     异步获取分类信息的方法
    */
    public function get_info(){
	    if(!$this->isAjax()) redirect(__APP__);
	      
	    $info=get_catOne($this->_post('pid','intval'),false,'Nodecat');

	    if(!empty($info))
	        echo $info;
    } 

}