<?php
/*
	@der 公司动态
*/
Class DynamicViewModel extends ViewModel{
	public $viewFields = array(
		'dynamic' => array(
			'id','title','content','source','create_time','sort',
			'_type' => 'LEFT'
			),
		'dynamic_cat' => array(
			'cat_name',
			'_on'   => 'dynamic.cid = dynamic_cat.id',
			'_type' => 'LEFT'
			),
		'admin' => array(
			'username',
			'_on' => 'dynamic.uid = admin.id',
			),
		);

	public function demo(){
		echo 11;
	}
}