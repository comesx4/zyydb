<?php
 Class PerformanceViewModel extends ViewModel{
 	  public $viewFields=array(
 	  	  'performance'=>array(
 	  	  	'id','name','remark','title','cid','sort',
 	  	  	'_type'=>'LEFT'

 	  	  	),
 	  	  'goods'=>array(
 	  	  	'goods',
 	  	  	'_on'=>'performance.cid=goods.id'

 	  	  	),       

 	  	);
 }