<?php
Class GoodsAttrAction extends CommonAction{
	//属性列表
	public function index() {
            
          $t = I('get.');
        if (!empty($t['gid']))              
                    $where['gid'] = array('EQ', $t['gid']); 
       if (!empty($t['retrieve'])) 
            $where = array('attr' => array('like', "%{$t['retrieve']}%"));
     
        $db = D('GoodsattrView');
        //统计总条数 
        $sum = $db->where($where)->count();

        //如果单击了排序
        if (isset($_POST['submit'])) {
            unset($_POST['submit']);
            //循环修改排序
            foreach ($_POST as $key => $v) {
                M('Goodsattr')->where(array('id' => $key))->setField(array('sort' => $v));
            }
        }

        //导入分页类
        import('Class.Page', APP_PATH);

        $page = new Page($sum, 10);
        $attr = $db->order('gid ASC')->where($where)->limit($page->limit)->select();

        $this->attr = $attr;
        $this->fpage = $page->fpage();
        $this->goods = goodsList('gid',$this->tmp["gid"]);
        $this->display();
    }

    //添加属性视图
	public function add(){
		//调用百度文本编辑器
		$this->ueditor=ueditor1();

		//产品的列表
		$this->goods=goodsList();

        //样式的列表
        $this->css=cssOption();

		$this->display();

	}

	//添加属性操作
	public function insert(){
		if(!$this->ispost()) $this->redirect('Index/index');
		
		//判断值是否为空
		if(empty($_POST['attr']))
			$this->error('名称或内容不能为空');

		if(M('Goodsattr')->data($_POST)->add())
			$this->success('添加成功',U('index'));
		else
			$this->error('添加失败请重试');

	}

	//修改视图
	public function update(){
		$id=$this->_get('id','intval');

		$this->attr=$attr=M('goodsattr')->where(array('id'=>$id))->find();
		//取得产品列表
		$this->goods=goodsList('gid',$attr['gid']);
		$this->display();
	}

	//修改操作
	public function save (){
		if(!$this->ispost()) $this->redirect('Index/index');

		// 将内容在数据库中修改
		if(M('goodsattr')->save($_POST))
                        $this->success('修改成功',U('index',array('gid'=>I('post.gid'))));
		else
			$this->error('修改失败请重试');
	}

	
	//属性的删除操作
	public function del(){
		$where=array('id'=>$this->_get('id','intval'));

		if(M('Goodsattr')->where($where)->delete())
			$this->success('删除成功',U('index'));
		else
			$this->error('删除失败请重试');
	}


	//异步获取样式的内容
	public function getChange(){
		if(!$this->isAjax()) $this->redirect('index/index');

		$where=array('id'=>$this->_post('id','intval'));

		//查找出对应的样式内容
		if($content=M('Csscustom')->field('istitle,name,content')->where($where)->find()){
			$content['status']=1;
			echo json_encode($content);
		
		}else{
		
			$content['status']=0;
			echo json_encode($content);

		}

	}

}