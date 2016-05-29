<?php

/*
  @der 普防服务器
 */

Class NormalProtectAction extends CommonAction {
    /*
      @der 列表页
     */
    public function index() {
        $db = M('normal_protect');
        //调用排序函数
        cat_sort('normal_protect');
        $page = X($db);
        $data = $db->field(true)->order('sort DESC')->limit($page['0']->limit)->select();
        $this->xdd = 'normal_protect';

        $this->data = $data;
        $this->fpage = $page['1'];
        $this->display();
    }

    /*
      @der 添加页面
     */

    public function add() {
        $this->display();
    }

    /*
      @der 添加数据操作
     */

    public function insert() {
        if (!IS_POST)
            redirect(__APP__);
        post_isnull('name', 'price','type') && $this->error('请全部填写完整');

        $add = I('post.');
        if (M('normal_protect')->add($add)) {
            $this->success('添加成功', U('index'));
        } else {
            $this->error('添加失败');
        }
    }

    /*
      @der 修改页面
     */

    public function update() {
        $id = I('get.id', 0, 'intval');
        $data = M('normal_protect')->field(true)->where(array('id' => $id))->find();
        $this->data = $data;
        $this->display();
    }

    /*
      @der 修改数据操作
     */

    public function save() {
        if (!IS_POST)
            redirect(__APP__);

        $save = I('post.');

        if (M('normal_protect')->save($save)) {
            $this->success('修改成功', U('index'));
        } else {
            $this->error('修改失败');
        }
    }

    /*
      @der 删除数据操作
     */

    public function delete() {
        $this->db_delete(M('normal_protect'));
    }

}
