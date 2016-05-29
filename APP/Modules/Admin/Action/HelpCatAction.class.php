<?php
/*
 帮助中心的无限分类控制器
*/
Class HelpCatAction extends CommonAction{

	/*
	分类列表
	*/
	public function index() {
            
       // N('read', 1); //设置计数器步进值
       
        //调用排序函数
        cat_sort('Helpcat');
        $this->data = Mirroringcat('Helpcat');

        //$count = N('read'); //统计当前页面执行的查询数目
        //echo $count;
        $this->display();
    }

    /*
	添加分类视图
	*/
	public function add(){
		
		//判断有没有添加子分类操作
		if(isset($_GET['id'])){

			$this->cat=MirroringSelect('pid',$this->_get('id','intval'),'Helpcat');

		}else{
			$this->cat=MirroringSelect('pid',0,'Helpcat');
		}
		$this->display();
	}

	/*
	添加分类操作
	*/
	public function insert(){
		if(!$this->ispost()) redirect(__APP__);
                                 
                 $arr=array();
                foreach(array_unique(I('cat')) as $v){
                 $arr[]=array(
                'cat'=>$v,
                'pid'=>I('pid'),// $_POST['pid'],
                'sort'=>I('sort')//$_POST['sort'],              
                );      
		}

	    if(M('Helpcat')->addAll($arr)){
              $this->success('添加成功',U('add'));
	    }else{
              $this->error('添加失败');
	    }



	}

	 /*
	 分类的修改视图
	 */
     public function update(){
     	$id=I('id');

     	//查找出该分类的信息
     	$this->data=M('Helpcat')->where(array('id'=>$id))->find();

     	$this->cat=MirroringSelect('pid',$this->data['pid'],'Helpcat');
     
     	$this->display();
     }

     /*
     分类修改操作
     */
     public function save(){
     	if(!$this->ispost()) redirect(__APP__);
     	
     	if(M('Helpcat')->setField($this->_post()))
     		$this->success('修改成功',U('index'));
     	else
     		$this->error('修改失败');
     }


	/*
     分类的删除操作
    */
     public function del(){
     	$id=$this->_get('id','intval');

     	$db=M('Helpcat');

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

}