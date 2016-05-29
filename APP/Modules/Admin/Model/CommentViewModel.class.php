<?php
/**
 * 评论视图模型
 */
Class CommentViewModel extends ViewModel {

	Protected $viewFields = array(
		'comment' => array(
			'id', 'comment', 'time', 'wid',
			'_type' => 'LEFT'
			),
		'userinfo' => array(
			'uname', '_on' => 'comment.uid = userinfo.uid'
			)
		);
}
?>
