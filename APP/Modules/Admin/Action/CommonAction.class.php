<?php
Class CommonAction extends Action {

    public function _initialize() {
        if (empty(I('session.')[C('USER_AUTH_KEY')])) {
            $this->redirect('Login/index');
        }

        /* 后台记录 */
        if (C('ADMIN_DETAIL')) {
            if (preg_match('/\w*?((insert)|(save)|(delete)|(del)|(memberLock)|(userLock))\w*?/is', ACTION_NAME) > 0) {

                $name = MODULE_NAME . '/' . ACTION_NAME;
                $title = M('Node')->where(array('title' => $name))->getField('remark');
                $title || $title = '未知操作';
                if (isset($_REQUEST['id'])) {
                    is_array($_REQUEST['id']) ? $id = implode(',', $_REQUEST['id']) : $id = $_REQUEST['id'];
                    $title .= "(id => {$id})";
                }
                
                if (I('uid')) {
                    $title .= "(用户id => " . I('uid') . ")";
                }

                if (I('aid')) {
                    $title .= "(管理员id => " . I('aid') . ")";
                }

                $add = array(
                    'name' => $name,
                    'remark' => $title,
                    'uid' =>getAdminID(),
                );
                create_record(M('Admin_detail'), '', $add);
            }
        }

        //如果开启了验证
        if (C('USER_AUTH_ON')) {
            $bol = in_array(strtolower(MODULE_NAME), explode(",", C('NOT_AUTH_MODULE'))) || in_array(strtolower(ACTION_NAME), explode(",", C('NOT_AUTH_ACTION')));
            //如果访问的控制器或方法是无需认证的就不验证了                     
            if (!$bol) {
                import('ORG.Util.RBAC');
                //如果使用分组一定要加GROUP_NAME   
            if (!RBAC::AccessDecision(GROUP_NAME)) {
                    log::write( '超出用户权限的操作=>' . $title . '('.$name.')->当前用户ID：' . I('session.')[C('USER_AUTH_KEY')], WARN);
                    $this->error("你没有这个权限");
                }
            }
        }
        $name = MODULE_NAME . '/' . ACTION_NAME;
      
        $where = array('title' => $name);
        $node_pid = M('Node')->where($where)->getField('id');

        $field = 'id,pid,title,remark';
        $node = get_parent($node_pid, 'Node', $field);
        $this->now_node = array_reverse($node); 
        $this->tmp =  I('get.');   
        $this->actionname = ACTION_NAME;   
    }
    
 

    /**
		@der 处理删除的方法
		@param object $db 数据库连接
		@param array  $where 
    */
	protected function db_delete($db,$where = ''){
		if (empty($where)) {
			$where = array('id' => I('get.id',0,'intval'));
		}
		if ($db->where($where)->delete()) {
			$this->success('删除成功',U('index'));
		} else {
			$this->error('删除失败');
		}

	}
        
        public function getlistbyarr() {
        if (!$this->ispost()) {
            $data['status'] = 0;
            $data['message'] = '非法的请求';
            $this->ajaxReturn($data, 'JSON');
            die(0);
        }

        $p = I('post.');
        $str = FLFParameter::getStatusList($p['key'], $p['arr'],$p['val'], $p['mode']);
        $data['status'] = 0;
        $data['message'] = $str;
        $this->ajaxReturn($data, 'JSON');
    }

}