<?php
/*
 工单回复表的Model
*/
Class WoreplyModel extends RelationModel{
	protected $_link=array(
        'woreply_img'=>array(
             'mapping_type'=>HAS_MANY,
             'foreign_key'=>'rid',
             'mapping_fields'=>'img'
        	),
		);
  
	/*
      删除工单的操作
	*/
    public function del($id){
        //删除工单图片表的数据和图片
        $img=M('Wo_img')->field('img')->where(array('wid'=>$id))->select();
        if(!empty($img)){
            //循环删除图片
            foreach($img as $v){
                 unlink("./Uploads/Scence/{$v['img']}");
            }

            //删除数据库的数据
            M('Wo_img')->where(array('wid'=>$id))->delete();

        }
    	
        //删除工单对应的回复和回复图片
		    $rid=$this->field('id')->where(array('wid'=>$id))->select();
        $where=array('rid'=>array('in',implode(',',peatarr($rid,'id'))));
		    $path=M('Woreply_img')->field('img')->where($where)->select();
        
        //循环删除附件
    		foreach($path as $v){
    			 unlink("./Uploads/Scence/{$v['img']}");
    		}

        //删除工单的所有记录
        M('Wo_record')->where(array('wid'=>$id))->delete();

        $this->where(array('wid'=>$id))->delete();
        M('Woreply_img')->where($where)->delete();
    }

    /*
       查询工单信息的方法
    */
    public function get_info($class){

      $where=array('wid'=>$_GET['id']);

      //查找出工单的信息
        $wo=M('Wo')->where(array('id'=>$_GET['id']))->find();
        if(empty($wo)) redirect(__APP__);

        //判断该工单是否有评价
        if($wo['status']==3&&$wo['appraise']!=0){
           $advice=M('Advice')->field('advice,time')->where(array('wid'=>$wo['id']))->find();
           $class->advice=empty($advice)?array('前台用户确认工单已解决!',"评价内容：无建议和意见"):array('前台用户确认工单已解决!',"评价内容：{$advice['advice']} ");
           $class->adviceTime=$advice['time']; //评价的时间
        }

        //分页
        $page=X($this,$where,10);

        //查找出工单的附件
        $this->img=M('Wo_img')->field('img')->where(array('wid'=>$_GET['id']))->select();

        //查找出工单的所有回复
        $reply=$this->relation(true)->where($where)->order('id ASC')->limit($page['0']->limit)->select();

        $class->wo=$wo;
        $class->reply=$reply;
        $class->fpage=$page['1'];

        return $wo;

    }
}