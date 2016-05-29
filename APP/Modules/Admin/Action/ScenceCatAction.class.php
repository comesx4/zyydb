<?php
/*工单分类控制器*/
Class ScenceCatAction extends CommonAction{

	/*
	分类列表
	*/
	public function index(){
		//调用排序函数
		cat_sort('Scencecat');
		
		$this->data=Mirroringcat('Scencecat');
		$this->display('HelpCat:index');
	}

	/*
	添加分类视图
	*/
	public function add(){

        $this->checkbox=show_checkbox('Role','rid','name'); //复选框
		
		//判断有没有添加子分类操作
		if(isset($_GET['id'])){

			$this->cat=MirroringSelect('pid',$this->_get('id','intval'),'Scencecat');

		}else{
			$this->cat=MirroringSelect('pid',0,'Scencecat');
		}
		$this->display();
	}

	/*
	  添加分类操作
	*/
	public function insert(){
		if(!$this->ispost()) redirect(__APP__);
                
               $_POST['cat']=array_unique($_POST['cat']);//去除得复项                
                 $arr=array();
                foreach($_POST['cat'] as $v){   
            
                 $arr[]=array(
                'cat'=>$v,
                'pid'=>I('pid'),// $_POST['pid'],
                'sort'=>I('sort'),//$_POST['sort'],
                'type'=>I('type'),//$_POST['type'],
                'remark'=>I('remark')//$_POST['remark']
                );      
		}

		//判断有没有上传图片
        if($_FILES['img']['error']==0){
        	$info=uploadimg('./Uploads/ScenceCat/');

        	if(!is_array($info))  $this->error($info);

        	$arr['0']['img']=$info['0']['savename'];

        }
        
	    if($id=M('Scencecat')->addAll($arr)){
              //用户组的添加
              $this->role(false,$id); 
              
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
     	$this->data=M('Scencecat')->where(array('id'=>$id))->find();
        
        //查找出该分类所属的用户组
        $rid=M('Scencecat_role')->field('rid')->where(array('sid'=>$id))->select();
        $rid=peatarr($rid,'rid');        
        $this->checkbox=show_checkbox('Role','rid','name',$rid);  //复选框

     	$this->cat=MirroringSelect('pid',$this->data['pid'],'Scencecat');
     
     	$this->display();
     }

     /*
     分类修改操作
     */
     public function save(){
     	if(!$this->ispost()) redirect(__APP__);

     	$db=M('Scencecat');

     	//判断有没有上传图片
        if($_FILES['img']['error']==0){

        	//将之前的LOGO查找出来
        	$logo=$db->where(array('id'=>$_POST['id']))->getField('img');
        	
        	//图片上传
        	$info=uploadimg('./Uploads/ScenceCat/');

        	if(!is_array($info))  $this->error($info);
        	
        	$_POST['img']=$info['0']['savename'];

        }
     	
     	if($db->setField($this->_post())|$this->role(true)){
          
     		if(!empty($logo))  unlink("./Uploads/ScenceCat/{$logo}");    

     		$this->success('修改成功',U('index'));
     	}else{
     		$this->error('修改失败');
     	}
     }
      
      


	/*
     分类的删除操作
    */
     public function del(){
     	$id=$this->_get('id','intval');

     	$db=M('Scencecat');

     	//判断该分类下有没有子分类
     	if($demo=$db->where(array('pid'=>$id))->getField('id')){     	
     		$this->error('该分类下有子分类，请先删除子分类');
     	}
        
        //将之前的LOGO查找出来
     	$logo=$db->where(array('id'=>$id))->getField('img');

     	if($db->where(array('id'=>$id))->delete()){

     		if(!empty($logo))  unlink("./Uploads/ScenceCat/{$logo}");
             
             $this->success('删除成功',U('index'));
     	}else{
             $this->error('删除失败');
     	}
     }


     /*
        所属用户组的添加或修改的方法
        @part $type  true为修改，false为添加
      */
     private function role($type=false,$id){
       $id=!empty($_POST['id'])?$this->_post('id','intval'):$id;
       if($type){
           M('Scencecat_role')->where(array('sid'=>$id))->delete();
       }


        if(isset($_POST['rid'])){
            $add=array();
            $rid=$this->_post('rid','intval');

            foreach($rid as $v){
                   $add[]=array('rid'=>$v,'sid'=>$id);
            }
            
            M('Scencecat_role')->addAll($add);
            

            return true;
          
        }

         return false;

     }

    

}