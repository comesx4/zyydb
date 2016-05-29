<?php
/*
 @der 模型
*/
Class Service_specViewModel extends ViewModel{
	public $viewFields=array(
             'service_spec'=>array(
                    'id','discount','price','spec','time',
                    '_type'=>'LEFT'
             	),
             'service'=>array(
                    'title',
                    '_on'=>'service_spec.sid=service.id'
             	)

		);

}