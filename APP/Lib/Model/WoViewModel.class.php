<?php
/* 
  用户工单视图模型
*/
Class WoViewModel extends ViewModel{
	public $viewFields=array(
		    'wo'=>array(
                'id','number','title','time','status','complaint','isCharge',
                '_type'=>'LEFT'
		    	),
		    'scencecat'=>array(
                 'cat',
                 '_on'=>'wo.cid=scencecat.id',
                 '_type'=>'LEFT'
		    	),
		    'admin'=>array(
                 'username',
                 '_on'=>'wo.aid=admin.id'
		    	),

		);


	/*
      处理搜索加分页的方法 
	*/
     public function serach($where){
     	$pwhere='';
        $tmp=isset($_POST['serach'])?$_POST:$_GET;

        //查找出工单的所有二级分类
        //$cat=get_appoint_cat(M('Scencecat'),2);
        //p($cat);die;

     	 //如果单击了搜索
        if(isset($tmp['serach'])){
           $pwhere .="serach=1";//通过分类在GET传递的where条件

           //状态
           if(!empty($tmp['status'])){
               $where['status']=$tmp['status']-1;
               $pwhere .="&status={$tmp['status']}";
           }

           //起始日期
           if(!empty($tmp['date'])){
               $where['time'][]=array('egt',strtotime($tmp['date']));
               $pwhere .="&date={$tmp['date']}";
           }

           //目标日期
           if(!empty($tmp['date1'])){
               $where['time'][]=array('elt',strtotime($tmp['date1']));
               $pwhere .="&date1={$tmp['date1']}";
           }

           // 工单号
           if(!empty($tmp['number'])){
               $where['number']=array('like',"%{$tmp['number']}%");
               $pwhere="&number={$tmp['number']}";
           }

           //标题
           if(!empty($tmp['title'])){
               $where['title']=array('like',"%{$tmp['title']}%");
               $pwhere .="&title={$tmp['title']}";
           }

           
        }

        return array($where,$pwhere,$tmp);
     }
}