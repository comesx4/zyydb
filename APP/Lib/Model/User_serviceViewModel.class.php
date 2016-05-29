<?php
/*
  @der 用户服务模型 
*/
Class User_serviceViewModel extends ViewModel{
	public $viewFields=array(
                'user_service'=>array(
                	'id','createTime','endTime','isSubmit','status','remark',
                	'_type'=>'LEFT'

                	),
                'service'=>array(
                	'title','remark'=>'info','wid',
                	'_on'=>'user_service.service_id=service.id',
                
                	),
		);
}