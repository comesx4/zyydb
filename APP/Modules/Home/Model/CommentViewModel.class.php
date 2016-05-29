<?php
Class CommentViewModel extends ViewModel{
	protected $viewFields=array(
		'comment'=>array(
			'comment','time','uid',
			'_type'=>'LEFT'		 
		
		),
		'userinfo'=>array(
			'uname','face50'=>'face','uid',
			'_on'=>'comment.uid = userinfo.uid'	
		)
	
	
	
	
	
	
	
	
	);




   
   
   
   
   
   
   
   
   
   
   }
