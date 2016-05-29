<?php

// 产品控制器

Class GoodsAction extends CommonAction{

	//产品列表
	public function index(){
	 
                cat_sort('goods');
                
      	//判断检索的类型   
		if(isset($_POST['type'])&&$_POST['type']==0){
                    //按名称检索
			$name=$this->_post('retrieve');

			$where=array('goods'=>array('like',"{$name}%"));
		}elseif($_POST['type']==1){
			//按所属分类检索
			$name=$this->_post('retrieve');

			//去产品分类表查找出ID
			$id=M('Goodscat')->field('id')->where(array('cat'=>array('like',"{$name}%")))->select();
			
			$id=peatarr($id,'id');

			$where=array('cid'=>array('in',$id));
		}
		  
		$db=D('GoodsView');
		  //统计总条数 
        $sum=$db->where($where)->count();
      
        //导入分页类
        import('Class.Page',APP_PATH);
       $page=new Page($sum,C('PAGE_NUM'));
		$this->goods=$db->where($where)->scope('orderby')->limit($page->limit)->select();
		$this->fpage=$page->fpage();
		$this->display();

	}

	//添加产品视图
	public function add(){
		//调用函数取得分类
		$this->cat=goodsCat('cid');
		//取得样式
		$this->css=goodsCss();

		$this->display();

	}
    
    //插入产品到数据库
	public function insert(){
		if(!$this->ispost()) $this->redirect('Index/index');        
		
		if(empty($_POST['goods'])){
			$this->error('产品名称不能为空');
		}
		if ($_FILES['img']['error'] != 0 || $_FILES['introduction_img']['error'] != 0) {
			$this->error('必须上传产品图片');
		}
		//添加到产品表的数据
        $data=array(
        	'cid'		 => $this->_post('cid','intval'),
        	'goods'		 => $this->_post('goods'),
        	'goodstype'	 => $this->_post('goodstype','intval'),
        	'table_name' => I('post.table_name','')
        	);

		//处理LOGO上传
		$info=uploadimg('./Uploads/Goods/');        
        
        //如果上传成功了
		if(is_array($info)){
              $data['img']	 		    = $info['0']['savename'];
              $data['introduction_img'] = $info['1']['savename'];
		}else{
			$this->error($info);
		}
		
        //如果产品添加成功了
		if($id=M('Goods')->data($data)->add()){
			
			//查找出用户勾选了的样式
			$where=array('id'=>array('in',$_POST['css']));
			$css=M('Csscustom')->field('name as attr,content,sort,istitle')->where($where)->select();
       
			//在CSS数组中添加产品表的ID
			foreach($css as $key=>$v){
				$css[$key]['gid']=$id;				
			}			

			//将样式添加到产品的属性表中
			M('Goodsattr')->addAll($css);
			
			$this->success('添加成功',U('index'));
		}else{
			$this->error('添加失败');
		}


	}

	//修改产品视图
	public function update(){
		$id=$this->_get('id','intval');

		//产品的信息
		$goods=M('Goods')->where(array('id'=>$id))->find(); 
	
        $this->goods=$goods;

         //产品分类的下拉菜单
		$this->cat=goodsCat('cid',$goods['cid']);

		//查找出产品的所有属性
		$attr=M('Goodsattr')->where(array('gid'=>$id))->select();
		$this->attr=$attr;

		
		$this->display();
	}

	//修改操作
	public function save(){
		if(!$this->ispost()) $this->redirect('Index/index');

		$db=M('Goods');
        $count = 0;
        $_FILES['img']['error'] == 0 && $count++;
        $_FILES['introduction_img']['error'] == 0 && $count++;
        //如果用户修改了图片
		if($count != 0){

			if($count == 2) {
				$field = 'img,introduction_img';

			} elseif ($_FILES['img']['error'] == 0) {
				$field = 'img';
			} else {
				$field = 'introduction_img';
			}
			//先查找出图片
            $img=$db->field($field)->where(array('id'=>$this->_post('id','intval')))->find();          
            
            $info=uploadimg('./Uploads/Goods/');        

             //如果上传成功了
			if(is_array($info)){
				if (count($info) == 2) {
					$_POST['img'] 			   = $info['0']['savename'];
					$_POST['introduction_img'] = $info['1']['savename'];
				} else {
					$_POST[$field] = $info['0']['savename'];
				}
	            
			}else{
				  $this->error($info);
			}

		}
		
		if($db->save($this->_post())){
			
			//删除原来的图片
			foreach ($img as $v) {
				unlink('./Uploads/Goods/'.$v);
			}
			
			$this->success('修改成功',U('index'));

		}else{
			$this->error('修改失败，请重试');
		}

	}

	//产品的删除操作
	public function del(){
		$id=$this->_get('id','intval');
		$db = M('Goods');
		//先删除产品
		if($db->where(array('id'=>$id))->delete()){

			//再把产品的属性删除
			M('Goodsattr')->where(array('gid'=>$id))->delete();

			$img = $db->where(array('id'=>$id))->getField('img');  
			unlink('./Uploads/Goods/'.$img); 
			
			$this->success('删除成功',U('index'));
		
		}else{
			
			$this->error('删除失败，请重试');
		}
	}

	//修改产品属性的操作
	public function saveAttr(){
		if(!$this->ispost()) $this->redirect('Index/index');

		// 将内容在数据库中修改
		if(M('goodsattr')->save($_POST))
			$this->success('修改成功',redirect($_SERVER['HTTP_REFERER']));
		else
			$this->error('修改失败请重试');
	}

}