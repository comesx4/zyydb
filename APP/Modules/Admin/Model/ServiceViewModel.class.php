<?php
/*
  @der 服务的视图模型
*/
Class ServiceViewModel extends ViewModel{
	public $viewFields=array(
              'service'=>array(
                   'id','title','num','payTime','remark','warrantyTime','os',
                   '_type'=>'LEFT'

              	),
              'service_spec'=>array(
                    'price',
                    '_on'=>'service.id=service_spec.sid',
                    '_type'=>'LEFT'
              	),
              'source'=>array(
                    'source',
                    '_on'=>'service.sid=source.id',
                    '_type'=>'LEFT'
              	),
              'goods'=>array(
                    'goods',
                    '_on'=>'service.gid=goods.id',
                    '_type'=>'LEFT'
              	),
              'mirroringcat'=>array(
                     'cat',
                     '_on'=>'service.cid=mirroringcat.id'
                )
		);
}