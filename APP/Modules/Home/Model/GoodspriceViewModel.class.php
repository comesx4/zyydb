<?php 
Class GoodspriceViewModel extends ViewModel{
	  public $viewFields=array(
	  	   'goodsprice'=>array(
	  	   	'id','price','cid',
	  	   	'_type'=>'LEFT'

	  	   	),
	  	   'performance'=>array(
	  	   	'name',
	  	   	'_type'=>'LEFT',
	  	   	'_on'=>'goodsprice.pid=performance.id'
	  	   	)
	  	  

	  	);
}