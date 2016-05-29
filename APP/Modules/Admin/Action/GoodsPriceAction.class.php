<?php

Class GoodsPriceAction extends CommonAction {

    //价格列表
    public function index() {
        $t = I('get.');
        $gid=I('get.gid',1);
        $cid=I('get.cid',2);
        $c=array(
            'gid'=>$gid,
            'cid'=>$cid
        );
        $this->goods = goodsList('gid', $gid);
        $this->city = show_list('City', 'cid', 'city', $cid);
        $this->goodslista = show_lista('goods', 'gid', 'goods', 'btn',$c);
        $this->citylista = show_lista('City', 'cid', 'city', 'btn', $c);

        $db = D('GoodspriceView');
        $where['gid'] = $gid;
        $where['cid'] = $cid;
//        $page = X($db, $where, C('PAGE_NUM'), getUrlParameter($t));
//        $this->list = $db->where($where)->scope('orderby')->limit($page[0]->limit)->select();
//        $this->fpage = $page[1];
        $this->list = $db->where($where)->scope('orderby')->select();
        $this->display();
    }

    //添加价格视图
    public function add() {
        //城市的列表
        $this->city = city('cid');

        //产品的列表
        $this->goodsList = goodsList('gid');
        $this->per = M('Performance')->field('id,remark')->order('sort ASC')->select();

        $this->display();
    }

    //添加视图操作
    public function insert() {
        if (!$this->ispost())
            $this->redirect('Index/index');
        $gid = $this->_post('gid', 'intval');
        $cid = $this->_post('cid', 'intval');
        unset($_POST['gid']);
        unset($_POST['cid']);

        //判断是否有空值	
        foreach ($_POST as $v) {
            if (empty($v))
                $this->error('有价钱内容为空');
        }

        $arr = array();
        $db = M('Goodsprice');

        //循环将内容添加到数组中
        foreach ($_POST as $key => $v) {
            $arr[] = array('pid' => $key, 'price' => $v, 'gid' => $gid, 'cid' => $cid);
        }

        if ($db->addAll($arr))
            $this->success('价格订制成功', U('index'));
        else
            $this->error('订制失败，请重试');
    }

    //修改视图
    public function update() {
        $id = $this->_get('id', 'intval');
        $arr = M('Goodsprice')->field('id,pid,price')->find($id);
        $this->id = $arr['id'];
        $this->price = $arr['price'];
        // echo M('Goodsprice')->getLastSql();p($arr);die;
        $this->remark = M('Performance')->where(array('id' => $arr['pid']))->getField('remark');


        $this->display();
    }

    //修改操作
    public function save() {
        if (!$this->ispost())
            $this->redirect('Index/index');

        if (M('Goodsprice')->save($this->_post()))
            $this->success('修改成功', U('index'));
        else
            $this->error('修改失败，请重试');
    }

    //删除操作
    public function del() {

        if (isset($_POST['submit'])) {
            $where = array('id' => array('IN', implode(',', I('post.id'))));
        } else {
            $where = array('id' => I('get.id', 0, 'intval'));
        }
        $this->db_delete(M('Goodsprice'), $where);
    }

    //异步获取产品性能
    public function getPer() {
        if (!$this->isAjax())
            redirect(__APP__);

        $id = $this->_post('gid', 'intval');

        //根据产品ID查找出它所属的分类ID
        // $where=array('id'=>$gid);
        // $id=M('Goods')->where($where)->getField('cid');
        //根据分类ID查找出性能
        $per = M('Performance')->field('remark,id')->where(array('cid' => $id))->order('sort ASC')->select();
        // p($per);

        $str = '';
        foreach ($per as $v) {
            $str .= "<tr class='demo'><td class='first'>{$v['remark']}:</td><td><input type='text' name='{$v['id']}' value=''/></td></tr>";
        }

        echo $str;
    }

}
