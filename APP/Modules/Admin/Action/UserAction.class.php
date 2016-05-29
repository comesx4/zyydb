<?php

Class UserAction extends CommonAction {

    //管理员列表
    public function index() {
        $this->admin = D('AdminRelation')->relation(true)->field('passwrod', true)->select();
        //p($this->admin);die;
        $this->display();
    }

    //添加管理员列表
    public function add() {
        $this->group = groupList();
        $this->display();
    }

    //管理员修改界面
    public function update() {
        $id = I('get.id'); //$this->_get('id','intval');
        //用户的信息
        $this->user = M('Admin')->field('id,username,lock')->where(array('id' => $id))->find();
        //查找出用户所在的用户组

        $group = M('Role_user')->field('role_id')->where(array('user_id' => $id))->select();
        $group = peatarr($group, 'role_id');

        $this->group = groupList($group);


        $this->display();
    }

    //管理员修改操作
    public function save() {
        if (!$this->ispost())
            $this->redirect('Index/index');
        $id = I('post.user_id'); //$this->_post('user_id','intval');
        $db = M('Role_user');
        //p();die;
        //先移除重复的值，防止用户选择了相同的2个以上用户组
        $_POST['role'] = array_unique($_POST['role']);

        //首先修改锁定或解锁
        M('Admin')->where(array('id' => $id))->setField(array('lock' => $this->_post('lock', 'intval')));
        //修改关系表之前先将关系清空        
        $db->where(array('user_id' => $id))->delete();

        //再将关系添加到中间表中
        $arr = array();
        foreach ($_POST['role'] as $v) {
            $arr[] = array('user_id' => $id, 'role_id' => $v);
        }
        if ($db->addAll($arr))
            $this->success('修改成功', U('index'));
        else
            $this->error('修改失败，请重试');
    }

    //管理员修改操作
    public function delroleuser() {
        //if(!$this->ispost()) $this->redirect('Index/index');
        $id = $this->_get('user_id', 'intval');
        $db = M('Role_user');
        //p();die;
        //先移除重复的值，防止用户选择了相同的2个以上用户组
        $_POST['role'] = array_unique($_POST['role']);

        //首先修改锁定或解锁
        M('Admin')->where(array('id' => $id))->setField(array('lock' => $this->_post('lock', 'intval')));
        //修改关系表之前先将关系清空        
        $db->where(array('user_id' => $id))->delete();

        //再将关系添加到中间表中
        $arr = array();
        foreach ($_POST['role'] as $v) {
            $arr[] = array('user_id' => $id, 'role_id' => $v);
        }
        if ($db->addAll($arr))
            $this->success('修改成功', U('index'));
        else
            $this->error('修改失败，请重试');
    }

    //管理员删除操作
    public function del() {
        $id = $this->_get('id', 'intval');

        //先删除该管理员
        if (M('Admin')->where(array('id' => $id))->delete()) {
            //再删除该角色和所属组的关系
            M('Role_user')->where(array('user_id' => $id))->delete();
            $this->success('删除成功', U('User/index'));
        } else {
            $this->error('删除失败请重试');
        }
    }

    //异步解锁和加锁
    public function userLock() {
        if (!$this->ispost())
            $this->redirect('Index/index');

        //如果存在aid就说明是管理员锁定操作
        if (isset($_POST['aid'])) {
            $db = M('Admin');

            $this->_clearOrBlock($db, 'aid');

            // 反之就是普通用户锁定操作
        } else {
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

    //添加新管理员到数据库
    public function insert() {
        if (!$this->ispost())
            $this->redirect('Index/index');

        $db = M('Admin');
        $where = array('username' => $this->_post('username'));
        $salt = work_order(6);
        $_POST['password'] = md5Salt(I('post.password'), $salt);
        $_POST['salt'] = $salt;

        //判断用户名是否存在
        if ($db->where($where)->count() > 0) {
            $this->error('用户名已存在');
        }

        //先将用户信息添加进去
        if ($id = $db->add($this->_post())) {
            //组合信息添加到中间表
            $arr = array();
            //先移除重复的值，防止用户选择了相同的2个以上用户组
            $_POST['role'] = array_unique($_POST['role']);
            foreach ($_POST['role'] as $v) {
                $arr[] = array('role_id' => $v, 'user_id' => $id);
            }

            M('Role_user')->addAll($arr);
            $this->success('添加成功', U('index'));
        } else {
            $this->error('添加失败');
        }
    }

    //添加管理员组视图
    public function addGroup() {
        $this->display();
    }

    //添加用户组到数据库
    public function insertGroup() {
        if (!$this->ispost())
            $this->redirect('Index/index');

        //判断用户有没有输入内容 
        if (empty($_POST['name']) || empty($_POST['remark']))
            $this->error('名称或描述不得为空');

        if (M('Role')->data($_POST)->add())
            $this->success('添加成功', U('addGroup'));
        else
            $this->error('添加失败');
    }

    //用户组列表
    public function group() {

        $this->role = M('Role')->field('pid', true)->select();
        $this->display();
    }

    //修改用户组视图
    public function updateGroup() {
        $this->role = M('Role')->field('pid', true)->where(array('id' => $this->_get('id', 'intval')))->find();
        $this->display();
    }

    //修改用户组操作
    public function saveGroup() {
        if (!$this->ispost())
            $this->redirect('Index/index');
        if (M('Role')->save($_POST))
            $this->success('修改成功', U('group'));
        else
            $this->error('修改失败');
    }

    //删除用户组操作
    public function delGroup() {
        $id = $this->_get('id', 'intval');

        //先删除用户组
        if (M('Role')->where(array('id' => $id))->delete()) {
            $where = array('role_id' => $id);
            //再删除用户组的权限
            M('Access')->where($where)->delete();

            //再删除所在这个用户组的角色权限
            M('Role_user')->where($where)->delete();

            $this->redirect('group');
        }
    }

    //添加节点视图
    public function addNode() {
        $this->pid = !empty($_GET['pid']) ? $this->_get('pid', 'intval') : 0;
        $this->level = !empty($_GET['level']) ? $this->_get('level', 'level') : 1;
        $mess = '';
        switch ($_GET['level']) {
            case 2:
                $mess = '控制器';
                break;
            case 3:
                $mess = '方法';
                break;
            default:
                $mess = '应用';
                break;
        }
        //如果是添加方法
        if ($this->level == 3) {
            $data = M('Node')->field('cid,name')->where(array('id' => $this->pid))->find();
            if ($data['cid']) {
                $this->cat = get_catAll($data['cid'], 'Nodecat');
            } else {
                $this->cat = get_catOne(0, false, 'Nodecat');
            }

            $this->data = $data;
        } else {
            $this->cat = get_catOne(0, false, 'Nodecat');
        }
        $this->mess = $mess;
        $this->display();
    }

    //添加节点到数据库
    public function insertNode() {
        if (!$this->ispost())
            $this->redirect('Index/index');
        //判断用户有没有输入内容 
        if (empty($_POST['name']) || empty($_POST['remark']))
            $this->error('名称或描述不得为空');

        //将数据插入到数据库中
        if ($id = M('Node')->data($this->_post())->add()) {

            //如果单击了添加常用属性
            if (isset($_POST['attr'])) {
                $add[] = array(
                    'cid' => $this->_post('cid', 'intval'),
                    'pid' => $id,
                    'level' => 3,
                    'status' => 1,
                    'type' => 1,
                    'name' => 'add',
                    'title' => $this->_post('name') . "/add",
                    'remark' => "添加{$this->_post('remark')}"
                );
                $add[] = array(
                    'cid' => $this->_post('cid', 'intval'),
                    'pid' => $id,
                    'level' => 3,
                    'status' => 1,
                    'type' => 1,
                    'name' => 'insert',
                    'title' => $this->_post('name') . "/insert",
                    'remark' => "插入{$this->_post('remark')}"
                );
                $add[] = array(
                    'cid' => $this->_post('cid', 'intval'),
                    'pid' => $id,
                    'level' => 3,
                    'status' => 1,
                    'type' => 1,
                    'name' => 'update',
                    'title' => $this->_post('name') . "/update",
                    'remark' => "修改{$this->_post('remark')}界面"
                );
                $add[] = array(
                    'cid' => $this->_post('cid', 'intval'),
                    'pid' => $id,
                    'level' => 3,
                    'status' => 1,
                    'type' => 1,
                    'name' => 'save',
                    'title' => $this->_post('name') . "/save",
                    'remark' => "修改{$this->_post('remark')}操作"
                );
                $add[] = array(
                    'cid' => $this->_post('cid', 'intval'),
                    'pid' => $id,
                    'level' => 3,
                    'status' => 1,
                    'type' => 1,
                    'name' => 'delete',
                    'title' => $this->_post('name') . "/delete",
                    'remark' => "删除{$this->_post('remark')}"
                );
                $add[] = array(
                    'cid' => $this->_post('cid', 'intval'),
                    'pid' => $id,
                    'level' => 3,
                    'status' => 1,
                    'type' => 0,
                    'name' => 'index',
                    'title' => $this->_post('name') . "/index",
                    'remark' => "{$this->_post('remark')}管理"
                );

                M('Node')->addAll($add);
            }

            $this->success('添加成功', U('group'));
        } else {
            $this->error('添加失败');
        }
    }

    //节点的列表
    public function node() {

        //调用排序函数
        cat_sort('Node');
        $this->ctrid = show_list('node', 'pid', 'remark', 10);

        $stmt = D('NodeView')->order('node.sort DESC,cid ASC')->select();
        $data = node_sort($stmt);
        $this->node = $data;
        $this->display('node1');
    }

    //配置权限页面
    public function level() {
        $stmt = M('Node')->field('title', true)->select();
        $where = array('role_id' => $this->_get('rid', 'intval'));

        //查找出用户已经有了的权限
        $role = M('Access')->field('node_id')->where($where)->select();

        $role = peatarr($role, 'node_id');
        $data = node_sort($stmt, $role);

        $this->node = $data;
        $this->display('level1');
    }

    //插入权限到数据库中
    public function insertLevel() {
        if (!$this->ispost())
            $this->redirect('Index/index');
        $db = M('Access');
        //要配置权限的角色的ID
        $role_id = $this->_post('rid', 'intval');
        $arr = array();

        foreach ($_POST['node'] as $v) {
            list($node_id, $level) = explode('_', $v);
            $data = array('role_id' => $role_id, 'node_id' => $node_id, 'level' => $level);
            $arr[] = $data;
        }


        //先把用户之前的权限给删除
        $db->where(array('role_id' => $role_id))->delete();

        //将多条内容插入到关系表 
        if ($db->addAll($arr))
            $this->success('配置成功', U('group'));
        else
            $this->error('配置失败，请重试');
    }

    //修改节点的视图
    public function updateNode() {
        $this->pid = !empty($_GET['pid']) ? $_GET['pid'] : 0;
        $this->level = !empty($_GET['level']) ? $_GET['level'] : 1;
        $mess = '';
        switch ($_GET['level']) {
            case 2:
                $mess = '控制器';
                break;
            case 3:
                $mess = '方法';
                break;
            default:
                $mess = '应用';
                break;
        }

        $this->mess = $mess;
        $this->node = M('Node')->where(array('id' => $this->_get('id', 'intval')))->find();

        if ($this->node['cid']) {
            $this->cat = get_catAll($this->node['cid'], 'Nodecat');
        } else {
            $this->cat = get_catOne(0, false, 'Nodecat');
        }
        $this->display();
    }

    //修改节点的操作
    public function saveNode() {
        if (!$this->ispost())
            $this->redirect('Index/index');
        $pst = I('post.');
        $son = $pst['linkson'];
        $id = I('id');
        unset($pst['linkson']);
        $db = M('Node');
        if ($db->where(array('id' => $id))->save($pst)) {
            if ($son) {
                $data['cid'] = $pst['cid'];
                $db->where(array('pid' => I('id')))->data($data)->save();                
            }
            $this->success('修改成功', U('node'));
        } else {
            $this->error('修改失败，请重试 ');
        }
//        $this->display('Public:msg');
    }

    //删除节点的操作
    public function delNode() {
        $db = M('Node');
        $id = I('node_id');
        $where = array('id' => $id);
        //如果有子节点，不让删除
        if ($db->where(array('pid' => $id))->count() > 1) {
            $this->error('该节点下还有子节点');
        }
        //先删除节点表的数据
        if ($db->where($where)->delete()) {

            //再删除用户组对应这个节点的数据
            M('Access')->where(array('node_id' => $id))->delete();

            $this->success('删除成功', U('group'));
        } else {
            $this->error('删除失败请重试');
        }
    }

}
