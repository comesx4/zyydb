<?php
/*
  工单Model
*/
Class ScenceViewModel extends ViewModel{
	public $viewFields=array(
		
		'scence'=>array(
			'id','title','time','good','poor',
			'_type'=>'LEFT'
		     ),

		'scencecat'=>array(
            'cat',
            '_on'=>'scence.cid=scencecat.id'
			),



	);
}