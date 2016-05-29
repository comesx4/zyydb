<?php
//镜像视图模型
Class MirroringViewModel extends ViewModel{
	 public $viewFields=array(
              'mirroring'=>array(
               'id','name','time','type','image_pool','image_code','image_snap_code','uid','status','image_action','default_port',
               '_type'=>'LEFT'
              	),             
             
              'os'=>array(
                  'name'=>'os','os_code',
                 '_on'=>'mirroring.oid=os.id',
                ),

	 	);
}