<?php
/**
	@der 用户个人设置
*/
Class UserSetAction extends CommonAction{
	
	/**
		@der 修改密码视图
	*/
	public function updatePassword(){
		$this->display();
	}

	/**
		@der 修改密码操作
	*/
	public function savePassword(){
		IS_POST || redirect(__APP__);
		$db    = M('Admin');
		$where = array('id' => $_SESSION['uid']);
		$old   = I('post.oldPassword','');
		/*验证旧密码*/
		$user = $db->field('id,password,salt')->where($where)->find();

		if ( md5Salt($old,$user['salt']) != $user['password']) {
			$this->error('旧密码有误!');
		}
		
		$password = I('post.password');
		if ($password != I('post.rePassword') ) {
			$this->error('两次密码不一致!');
		}
		$salt = work_order(6);
		$save = array(
			'password' => md5Salt($password , $salt),
			'salt'	   => $salt
			);
		
		if ($db->where($where)->save($save) ){
			$this->success('修改成功');
		} else {
			$this->error('修改失败');
		}
	}
}