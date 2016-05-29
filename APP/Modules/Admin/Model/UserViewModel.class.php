<?php
/**
 * 微博用户视图模型
 */
Class UserViewModel extends ViewModel {

	Protected $viewFields = array(
		'user' => array(
			'id', '`lock`','`username`', 'password',
			'_type' => 'LEFT'
			),
		'userinfo' => array(
			'uname', 'face' => 'face50', 'telephone',
			'_on' => 'user.id = userinfo.uid'
			)
		);
}
?>
