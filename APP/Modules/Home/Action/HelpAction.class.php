<?php
/*
帮助中心控制器
*/
Class HelpAction extends Action{

	/*
       主页
	*/
    public function index(){
    
    	//查找出用户信息
    	$user=M()->query("SELECT u.id AS `id`,u.username AS `name`,i.face AS `face` FROM kz_user u LEFT JOIN kz_userinfo i ON u.id=i.uid WHERE u.id={$_SESSION['id']}");

        $this->users=$user['0'];
      
    	$this->display(); 
    }


    /*
     帮助信息集合页
    */
     public function nav(){
        if(empty($_GET['id'])) redirect(__APP__);

        $pid=$this->_get('pid','intval');

        //找出该分类下的所有子分类
        $cid=get_son($pid,'Helpcat');
        $cid[]=$pid;

        $where=array('cid'=>array('IN',implode(',',$cid)));



        //找出该分类下的热点问题

        $section=M('Help')->field('id,title,good,cid')->where($where)->order('good DESC')->select();
        
        $stmt=M('Helpcat');
        foreach($section as $key=>$v){
            $section[$key]['pid']=$stmt->where(array('id'=>$v['cid']))->getField('pid');

        }
        $this->section=$section;  

        //根据ID查找出改分类下的所有顶级分类
        $id=$this->_get('id','intval');
        $this->parentCat($id);


        //组合快速入口的内容
        $fast=catArr('Helpcat',$_GET['pid']);
        $this->fast=$fast;
        
        
  

     	$this->display();
     }


     /*
      问题列表页
     */
     public function helpList(){
      
        //查找出改分类下的所有问题
        $db=M('Help');
        $where=array('cid'=>$_GET['id']);

        //导入分页类
        import('Class.Page',APP_PATH);

        $count=$db->where($where)->count();
        $page=new Page($count,16);
        $data=$db->field('id,cid,title,time,good')->where($where)->limit($page->limit)->select();
        
         $stmt=M('Helpcat');
        foreach($data as $key=>$v){
            $data[$key]['pid']=$stmt->where(array('id'=>$v['cid']))->getField('pid');

        }

        $this->data=$data;
        
        if($count>16)
            $this->fpage=$page->fpage();

        //根据ID查找出改分类下的所有顶级分类
        $id=$this->_get('id','intval');
        $this->parentCat($id);
        
        $this->display();
     }


     //问题详细页
     public function helpShow(){
        
        $db=M('Help');  
        
         //根据HID查找出问题
        $data=$db->field('id,cid,title,anwer,time')->where(array('id'=>$_GET['hid']))->find();
        $data['anwer']=htmlspecialchars_decode($data['anwer']);     
        
        $this->parentCat($data['cid']);

         //找出相关问题
        $related=$db->field('id,cid,title,time,good')->where(array('cid'=>$data['cid'],'id'=>array('neq',$data['id'])))->order('good DESC')->limit('9')->select();
        
        $stmt=M('Helpcat');
         foreach($related as $key=>$v){
            $related[$key]['pid']=$stmt->where(array('id'=>$v['cid']))->getField('pid');

        }

        $this->related=$related;
       
       

        $this->data=$data; 

        $this->display();
     }

     /*
       用户异步评价问题
     */
    public function history(){
        if(!$this->isAjax()) redirect(__APP__);
        
        $where=array('id'=>$this->_post('id','intval'));
        if(M('Help')->where($where)->setInc($_POST['type']))
            echo 1;
        else
            echo 0;
        

    }
    
     

      /*
      根据ID查找出改分类下的所有顶级分类
      @part int $cid 依据的分类ID
      */
     private function parentCat($cid){
        
        $cat=get_parent($cid);
       
        $cat=array_reverse($cat);
        
        $this->cats=$cat;

        $this->sum=count($cat)-1;
     }

}