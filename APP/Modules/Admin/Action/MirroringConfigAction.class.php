<?php
/**
	@der 镜像推荐配置
*/
Class MirroringConfigAction extends CommonAction{
	//推荐配置列表
    public function index(){

        $this->data=include "./Data/groom.php";

        $this->display();
    }

    //推荐配置添加视图
    public function add(){ 
 
        $this->display();
    }

    //推荐配置添加操作
    public function insert(){
        if(!$this->ispost()) redirect(__APP__);
        $arr=array();

        $memory=$_POST['memory']==0?'512M':$_POST['memory'].'G';

        //组合信息添加到文件中
        $arr[]=array(
            'type'=>$this->_post('type'),
            'cpu'=>$this->_post('cpu','intval'),
            'memory'=>$this->_post('memory','intval'),
            'daikuans'=>$this->_post('daikuans','intval'),
            'hard'=>$this->_post('hard','intval'),
            'gather'=>$_POST['type'].":{$_POST['cpu']}核CPU-{$memory}内存-{$_POST['daikuans']}M带宽-{$_POST['hard']}G硬盘"
        );

        //读取出之前的内容
        $conts=include "./Data/groom.php";
        $arr=array_merge($conts,$arr);
        
        if(F('groom',$arr,'./Data/')){
            $this->success('添加成功',U('groom'));
        }else{
            $this->error('添加失败');
        }

    }

    //推荐配置删除操作
    public function delete(){
        $id=$this->_get('id','intval');

        $arr=include "./Data/groom.php";
        unset($arr[$id]);
        
        if(F('groom',$arr,'./Data/')){
            $this->success('删除成功',U('groom'));
        }else{
            $this->error('删除失败');
        }

    }

    //推荐配置的修改视图
    public function update(){
        $id=$this->_get('id','intval');
        $arr=include "./Data/groom.php";
        $this->data=$arr[$id];
        $this->display();
    }

    //推荐配置的修改操作
    public function save(){
         if(!$this->ispost()) redirect(__APP__);        
         
         $id=$this->_post('id','intval');    

         unset($_POST['id']);

          $arr=include "./Data/groom.php";

          $arr[$id]=$_POST;
          
          $memory=$_POST['memory']==0?'512M':$_POST['memory'].'G';
          $arr[$id]['gather']=$_POST['type'].":{$_POST['cpu']}核CPU-{$memory}内存-{$_POST['daikuans']}M带宽-{$_POST['hard']}G硬盘";

          if(F('groom',$arr,'./Data/')){
            $this->success('修改成功',U('groom'));
          }else{
            $this->error('修改失败');
          }
    }
}