<?php
/*
  @der 服务介绍视图模型
*/
Class Service_introduceViewModel extends ViewModel{
	public $viewFields=array(
             'service_introduce'=>array(
                   'id','sort',
                   '_type'=>'LEFT'
  
             	),
             'service'=>array(
                   'title',
                   '_on'=>'service_introduce.sid=service.id'
             	)
  
		);
}