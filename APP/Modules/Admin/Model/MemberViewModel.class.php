<?php
/**
 * 微博用户视图模型
 */
Class MemberViewModel extends ViewModel {

	Protected $viewFields = array(
		'user' => array(
			'id', '`lock`','username','logintime','loginip','register',
			'_type' => 'LEFT'
			),
		'userinfo' => array(
                        'uname', 'face' => 'face50', 'telephone','trueName',
                        '_on' => 'user.id = userinfo.uid'
			)
		);  
        protected $_scope = array(  
            
        // 命名范围 orderby
        'orderby'=>array(
            'order'=>'logintime DESC',       
        ),
    );
}
?>
