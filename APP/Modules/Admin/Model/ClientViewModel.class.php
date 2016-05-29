<?php
/**
 * 视图模型
 */
Class ClientViewModel extends ViewModel {

	Public $viewFields = array(
		'client' => array(
			'id', 'name', 'img',
			'_type' => 'LEFT'
			),
		'clientcat' => array(
			'cat',
			'_on' => 'client.cid=clientcat.id'
			)
		);
	

	
}
?>
