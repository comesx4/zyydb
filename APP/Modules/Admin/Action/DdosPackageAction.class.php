<?php

/*
  @der 高防云套餐设置
 */

Class DdosPackageAction extends CommonAction {
    /*
      @der 列表页
     */
    public function index() {
        $db = M(C('ddos'));
        //调用排序函数
        cat_sort(C('ddos'));
        $page = X($db);
        $data = $db->field(true)->order('sort DESC')->limit($page['0']->limit)->select();
        $this->xdd = C('ddos');
        $this->data = $data;
        $this->fpage = $page['1'];
        $this->display();
    }

    /*
      @der 添加页面
     */

    public function add() {

        $this->defensegradelist = FLFParameter::getStatusList('DefenseGrade', 'DefenseGradeList');
        $this->linelist = FLFParameter::getStatusList('LineType', 'LineList');
        $this->online = FLFParameter::getStatusList('online', 'Online',0, 1);
        $this->display();
    }

    /*
      @der 添加数据操作
     */

    public function insert() {
        if (!IS_POST)
            redirect(__APP__);
        post_isnull('band', 'price') && $this->error('请全部填写完整');

        /*  $add = array(
          'name' => I('post.name', ''),
          'band' => I('post.band', '', 'intval'),
          'number' => I('post.number', 'intval'),
          'DefenseGrade' => I('post.DefenseGrade', 'intval'),
          'LineType' => I('post.LineType', 'intval'),
          'price' => I('post.price'),
          'sort' => I('post.sort', 1, 'intval')
          ); */
        $add = I('post.');
        if (M(C('ddos'))->add($add)) {
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
        $data = M(C('ddos'))->field(true)->where(array('id' => $id))->find();
        $this->defensegradelist = FLFParameter::getStatusList('DefenseGrade', 'DefenseGradeList', $data['DefenseGrade']);
        $this->linelist = FLFParameter::getStatusList('LineType', 'LineList', $data['LineType']);
           $this->online = FLFParameter::getStatusList('online', 'Online', $data['online'],1 );
        $this->data = $data;
        $this->display();
    }

    /*
      @der 修改数据操作
     */

    public function save() {
        if (!IS_POST)
            redirect(__APP__);
        post_isnull('id', 'band', 'price') && $this->error('请全部填写完整');
        /*
          $save = array(
          'name' => I('post.name', ''),
          'band' => I('post.band', '', 'intval'),
          'number' => I('post.number', 'intval'),
          'DefenseGrade' => I('post.DefenseGrade', 'intval'),
          'LineType' => I('post.LineType', 'intval'),
          'price' => I('post.price'),
          'sort' => I('post.sort', 1, 'intval'),
          'id' => I('post.id', '0', 'intval')
          ); */
        $save = I('post.');
        if (M(C('ddos'))->save($save)) {
            $this->success('修改成功', U('index'));
        } else {
            $this->error('修改失败');
        }
    }

    /*
      @der 删除数据操作
     */

    public function delete() {
        $this->db_delete(M(C('ddos')));
    }

}
