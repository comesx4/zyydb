<?php
/*
	@der 公司公告
*/
Class NoticeViewModel extends ViewModel{
	public $viewFields = array(
		'notice' => array( 
				'id','title','content','sort','create_time',
				'_type' => 'LEFT'
				),
		'admin' => array(
			'username',
			'_on' => 'notice.uid = admin.id'
			)
		);

}