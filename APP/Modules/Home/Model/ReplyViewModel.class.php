<?php
Class ReplyViewModel extends ViewModel{
	protected $viewFields=array(
		'reply'=>array(
			'wid',
			'_type'=>'LEFT'
		),
		'comment'=>array(
			'id','comment','time',
			'_type'=>'LEFT',
			'_on'=>'reply.cid = comment.id'


		),
		'userinfo'=>array(
			'uname','face80'=>'face','uid',
			'_type'=>'LEFT',
			'_on'=>'comment.uid = userinfo.uid'
		
		),
	
	
	
	
	
	
	
	
	);

    
    
    
    
    
    
    
}
