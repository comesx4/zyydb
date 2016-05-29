<?php 
Class GoodspriceViewModel extends ViewModel{
	  public $viewFields=array(
	  	   'goodsprice'=>array(
	  	   	'id','price','cid','gid',
	  	   	'_type'=>'LEFT'

	  	   	),
	  	   'performance'=>array(
	  	   	'remark',
	  	   	'_type'=>'LEFT',
	  	   	'_on'=>'goodsprice.pid=performance.id'
	  	   	),
	  	   'city'=>array(
	  	   	'city',
	  	   	'_type'=>'LEFT',
	  	   	'_on'=>'goodsprice.cid=city.id'
	  	   	),
	  	   'goods'=>array(
	  	   	'goods',
	  	   	'_on'=>'goodsprice.gid=goods.id'
	  	   	),

	  	);
}