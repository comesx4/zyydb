<?php
/*
  问题Model
*/
Class HelpViewModel extends ViewModel{
	public $viewFields=array(
		
		'help'=>array(
			'id','title','time','good','poor',
			'_type'=>'LEFT'
		     ),

		'helpcat'=>array(
            'cat',
            '_on'=>'help.cid=helpcat.id'
			),



	);
}