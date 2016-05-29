<?php
//镜像视图模型
Class MirroringViewModel extends ViewModel{
	 public $viewFields=array(
              'mirroring'=>array(
               'id','name','gather','price','recom','img','new',
               '_type'=>'LEFT'
              	),             
             
              'os'=>array(
                  'name'=>'os',
                 '_on'=>'mirroring.oid=os.id',
                 '_type'=>'LEFT'

                ),
              'mirroringcat'=>array(
                  'cat',
                  '_on'=>'mirroring.cid=mirroringcat.id',
                  '_type'=>'LEFT'
              	),
              'source'=>array(
                  'source','custom',
                  '_on'=>'mirroring.sid=source.id'
                ),
             


	 	);
}