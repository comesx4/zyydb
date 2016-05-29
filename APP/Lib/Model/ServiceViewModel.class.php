<?php
/*
  @der 服务的视图模型
*/
Class ServiceViewModel extends ViewModel{
	public $viewFields=array(
              'service'=>array(
                   'id','title','num','payTime','remark','logo','warrantyTime','os','price',
                   '_type'=>'LEFT'

              	),              
              'source'=>array(
                    'source',
                    '_on'=>'service.sid=source.id',
                    '_type'=>'LEFT'
              	),
              'goods'=>array(
                    'goods',
                    '_on'=>'service.gid=goods.id'
              	)
		);
}