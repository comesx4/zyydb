<?php
//城市控制器
Class CityAction extends CommonAction{

	//城市列表
	public function index(){
            
                cat_sort('City');
		$this->city=M('City')->select();
//                $this->pem=  FLFParameter::getStatusList("xxd", 2);
		$this->display();

	}

	//添加城市视图
	public function add(){
		$this->display();
	}

	//添加城市操作
	public function insert(){
		if(!$this->ispost()) $this->redirect('Index/index');
		$db=M('City');

		if(empty($_POST['city']))
			 $this->error('城市名称不能为空');

		if($db->data($this->_post())->add()){
            //生成城市缓存文件
			$mem=$db->field('id,city')->select();
			$arr=array();
			foreach($mem as $v){
				$arr['city'][$v['id']]=$v['city'];
			}
			
			F('city',$arr,'./APP/Conf/');

			$this->success('添加成功',U('index'));
		}
		else{
			$this->error('添加失败请重试');
		}

	}

	//删除城市操作
	public function del(){
		$where=array('id'=>$this->_get('id','intval'));

		if(M('City')->where($where)->delete())
			$this->success('删除成功',U('index'));
		else
			$this->error('删除失败请重试');
	}

	//城市修改视图
	public function update(){
		$where = array('id' => I('get.id',0,'intval'));
		$this->data = M('City')->where($where)->find();
		
		$this->display();
	}

	//城市修改操作
	public function save(){
		if(!$this->ispost()) $this->redirect('Index/index');

		if (M('City')->save($this->_post())) {
			$this->success('修改成功',U('index'));
		} else {			
			$this->error('修改失败请重试');
		}
	}

} 
