<?php

//镜像控制器
Class MirroringAction extends CommonAction{

	//镜像列表
	public function index(){

		list($mirr,$tmp,$page) = D('MirroringView')->search();

		//查找每个镜像对应的多个产品
		// foreach($mirr as $key=>$v){

		// 	//根据镜像ID查找出对应的产品ID
		// 	$gid=M('Mirr_goods')->field('goods_id AS gid')->where(array('mirr_id' => $v['id']))->select();
			
		// 	$gid=implode(',',peatarr($gid,'gid'));			
  //           //查找出产品名称
		// 	$where=array('id'=>array('IN',$gid));

		// 	$goods=M('Goods')->field('goods')->where($where)->select();
		// 	$goods=implode(',',peatarr($goods,'goods'));

		// 	$mirr[$key]['goods']=$goods;
		// }
		
		$this->tmp   = $tmp;
		$this->fpage = $page['1'];
		$this->data  = $mirr;
		$this->display();
	}

	//添加镜像视图
	public function add(){  

	   //查找出所有以上架的产品
		$goods=M('Goods')->field('id,goods')->where(array('grounding'=>1))->select();        

		//找出该产品下所有的地域
		foreach($goods as $key=>$v){
			$where=array('gid'=>$v['id']);
			$city=M('Goodsprice')->field('cid')->where($where)->select();
			$city=peatarr($city,'cid');
			$city=implode(',',array_unique($city));
			$where=array('id'=>array('in',$city));
			$goods[$key]['city']=M('City')->field('id,city')->where($where)->order('sort DESC')->select();
		}

		//组合HTML内容
		$str='<ul>';
		foreach($goods as $v){
			$str .="<li>";
			$str .="<span><input type='checkbox' class='chiden' id='{$v['goods']}' name='goods[{$v['id']}]' /><label for='{$v['goods']}' >{$v['goods']}</label></span>";
            $str .="<div style='display:none;'>";
           		    $str .="<h1>选择地域:{$v['goods']}<em class='close'></em></h1>";
              foreach($v['city'] as $vs){

				$str .='<dl>';
					$str.="<dt><input type='checkbox' id='{$v['id']}{$vs['id']}' name='goods[{$v['id']}][]' value='{$vs['id']}'/></dt>";
					$str.="<dd><label for='{$v['id']}{$vs['id']}'>{$vs['city']}</label></dd>";
				$str.='</dl>';		

				}
			$str .="</div>";

		    $str .="</li>";
		}	
		$str .='</ul>';

		$this->goods=$str;


		//取出镜像分类下拉列表
	
		$cat=getCat_one('镜像');

		$this->cat=$cat;
		
		//取出操作系统下拉列表
		$this->os=show_list('Os','oid','name');

		//取出服务商下拉列表
		$this->source=show_list('Source','sid','source');		
       
        $this->display();
	}

	//添加镜像操作
	public function insert(){
		if(!$this->ispost()) redirect(__APP__);	
     	
		//判断值是否为空
		if(post_isnull('name','goods','recom','gather','cid','sid','oid'))
			$this->error('请全部填写完整');

		$info=uploadimg('./Uploads/Mirroring/');

      	//判断上传有没有出错
      	if(!is_array($info))
      	  	$this->error($info);
            
        $_POST['img']=$info['0']['savename'];

        if($id=M('Mirroring')->add($_POST)){

        	//处理镜像对应产品和地域的插入数据库操作
        	$add=array();            	
            $db=M('Mirr_goods');
            $db2=M('Mirr_city');
            foreach($_POST['goods'] as $key=>$v){
            	$add=array('mirr_id'=>$id,'goods_id'=>$key);
                 
                //如果镜像和产品中间表的数据添加成功
            	if($sid=$db->add($add)){    
            	    
            	    $data=array();

                    //组合添加到地域表的数据
            	    foreach($v as $vs){            		
                    	$data[]=array('mid'=>$id,'cid'=>$vs,'sid'=>$sid);
               		}

               		$db2->addAll($data);
            	}else{
            		$this->error('镜像产品添加失败了');
            	}
            }

             $this->success('添加成功');
        
        }else{
        	$this->error('添加失败');

        }

	}

	/*
		@der 镜像修改视图 
	*/
	public function update(){
		$id = I('get.id','','intval');

		//镜像信息
		$data = M('Mirroring')->where(array('id'=>$id))->find();
		
		$this->data = $data;
		//取出镜像分类下拉列表	
		$cat=getCat_one('镜像',1,'Mirroringcat',$data['cid']);
		$this->cat=$cat;		
		//取出操作系统下拉列表
		$this->os=show_list('Os','oid','name',$data['oid']);
		//取出服务商下拉列表
		$this->source=show_list('Source','sid','source',$data['sid']);		
		$this->display();
	}

	/*
		@der 镜像修改操作
	*/
	public function save(){
		if(!$this->ispost()) redirect(__APP___);
		$db = M('Mirroring');
		//判断值是否为空
		if(post_isnull('id','name','recom','gather','cid','sid','oid')) {
			$this->error('请全部填写完整');
		}
		/*上传了图片的情况*/
		if ($_FILES['img']['error'] == 0) {
			//查找出之前的图片
			$file = $db->where(array('id'=>I('post.id')))->getField('img');
			$info = uploadimg('./Uploads/Mirroring/');
			if ( !is_array($info) ) {
				$this->error($info);die;
			}
			$_POST['img'] = $info['0']['savename'];
			unlink("./Uploads/Mirroring/{$file}");
		}

		if ($db->save(arr_filter($_POST))) {
			$this->success('修改成功',U('index'));
		} else {
			$this->error('修改失败');
		}
	}

	/*
		@der 系统修改操作
	*/
	public function saveOs(){
		if(!$this->ispost()) redirect(__APP___);

		$save = array(
			'oid'		      =>$this->_post('oid','intval'),
			'type'	          =>$this->_post('type','intval'),
			'image_code'      => I('post.image_code'),
			'image_pool'      => I('post.image_pool'),
			'default_port'    => I('post.default_port'),
			'image_snap_code' => I('post.image_snap_code'),
			'default_size'	  => I('post.default_size'),
			'status'		  => I('post.status',1,'intval')		
			);
		$where = array('id' => I('post.id','','intval'));
		if (M('mirroring')->where($where)->save($save)) {
			$this->success('修改成功',U('index'));
		} else {
			$this->error('修改失败');
		}
	}


	//添加操作系统操作
	public function insertOs(){
		if(!$this->ispost()) redirect(__APP___);
		
		$data=array(
			'oid'		      =>$this->_post('oid','intval'),
			'type'	          =>$this->_post('type','intval'),
			'image_code'      => I('post.image_code'),
			'image_pool'      => I('post.image_pool'),
			'default_port'    => I('post.default_port'),
			'image_snap_code' => I('post.image_snap_code'),
			'default_size'	  => I('post.default_size'),
			'status'		  => I('post.status',1,'intval'),
			'time'			  => $_SERVER['REQUEST_TIME']
	    );

	    if(M('Mirroring')->add($data)){
             $this->success('添加成功',U('index'));
	    }else{
             $this->error('添加失败');
	    }
	}


	//镜像的删除操作
	public function del(){
		$id=$this->_get('id','intval');

		//先删除镜像表的内容
		if(M('Mirroring')->where(array('id'=>$id))->delete()){

			//删除镜像地域表的内容
			M('Mirr_city')->where(array('mid'=>$id))->delete();

			$this->success('删除成功',U('index'));

		}else{
            $this->error('删除失败');
		}
	}

	/**
		@der 自定义镜像的删除
	*/
	public function deleteCustomImage(){
		
		import('Class.Server',APP_PATH);
		$result = json_decode(Server::deleteImage(I('get.id',0,'intval')),true);

		if ($result['code'] == 0) {
			$this->success('删除成功',U('index'));
		} else {
			$this->error($result['error']);
		}
	}


}
