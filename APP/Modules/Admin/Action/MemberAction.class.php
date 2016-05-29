<?php

//前台会员管理
class MemberAction extends CommonAction {

    //前台会员列表
    public function index() {
        $t = I('get.');
        if (!empty($t['username'])) {
            switch ($t['searchtype']) {
                case 0:
                    $where['username'] = array('like', '%' . trim($t['username']) . '%');
                    break;
                case 1:
                    $where['uname'] = array('like', '%' . trim($t['username']) . '%');
                    break;
                case 2:
                    $where['trueName'] = array('like', '%' . trim($t['username']) . '%');
                    break;
            }
        }
        if (empty($t['max-date']))
            $where['register'] = array(array('EGT', strtotime($t['min-date'])), array('ELT', time()), 'AND');
        else
            $where['register'] = array(array('EGT', strtotime($t['min-date'])), array('ELT', strtotime($t['max-date'])), 'AND');

        $db = D('MemberView');
        //统计总条数 
        $sum = $db->where($where)->count();
        //导入分页类
        import('Class.Page', APP_PATH);
        $page = new Page($sum, C('PAGE_NUM'), getUrlParameter($t));
        $this->Member = $db->where($where)->scope('orderby')->limit($page->limit)->select();
        $this->fpage = $page->fpage();
        $this->display();
    }

    public function mdetail() {
        $mid = $this->_checkdata();
        $this->display('detail');
    }

    public function morder() {
        $mid = $this->_checkdata();
        $this->display('detail');
    }

    public function mgoods() {
        $mid = $this->_checkdata();
        $this->display('detail');
    }

    public function mservers() {
        $mid = $this->_checkdata();
        $this->display('detail');
    }

    public function memberLock() {
        if (!$this->ispost())
            $this->redirect('Index/index');
        if (isset($_POST['uid'])) {
            $db = M('User');
            $this->_clearOrBlock($db, 'uid');
        }
    }

    //处理解锁和加锁的方法
    private function _clearOrBlock($db, $id) {

        $where = array('id' => I($id, 'intval'));
        $save = array('lock' => I('type', 'intval'));
        if ($db->where($where)->save($save))
            echo 1;
        else
            echo 0;
    }

    private function _checkdata() {
        $memberid = I('get.id', 0, 'Intval');
        if ($memberid == 0) {
            redirect(U('public/msg'), 0);
        } else {
            return $memberid;
        }
    }

}
