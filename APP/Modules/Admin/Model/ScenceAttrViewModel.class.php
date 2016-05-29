<?php
/*
  工单属性模型
*/
Class ScenceAttrViewModel extends ViewModel{
	  public $viewFields=array(
             'scenceattr'=>array(
             	'id','title','type','must','sort',
             	'_type'=>'LEFT',
             	),
             'scencecat'=>array(
             	'cat',
             	'_on'=>'scenceattr.cid=scencecat.id'

             	),
	  	);

}