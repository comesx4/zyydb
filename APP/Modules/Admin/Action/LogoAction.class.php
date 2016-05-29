<?php
/*
    @der logo和产品推荐配置管理
*/
Class LogoAction extends CommonAction{
    //LOGO管理
    public function index(){
    	$this->display();
    }

    //图片导航管理
    public function pic(){
    	$this->display();
    }
    
    //上传LOGO图片
    public function uploadLogo(){
      
    	if(!$this->ispost()) $this->redirect('Index/index');

    	//导入文件上传函数
    	$info =  uploadimg();
    	 //判断上传有没有出错
    	if(!is_array($info)){
    		$this->error($info);
    	}

		//现将之前的LOGO删除
		unlink('./Uploads/Logo/'.C('logo'));
		//保存进去的LOGO路径
		$path['logo']=$info['0'][savename];
		//读取出配置文件
		$arr=include('./APP/Conf/logo.php');
        $arr=array_merge($arr,$path);
        //将内容写进配置文件中
        F('logo',$arr,'./APP/Conf/');

        $this->success('修改成功',U('index'));
        

    }

    //上传导航图片
    public function uploadPic(){
    	if(!$this->ispost()) $this->redirect('Index/index');

    	//导入文件上传函数
    
		$info =  uploadimg();
        //判断上传有没有出错
		if(!is_array($info)){
    		$this->error($info);
    	}
		$path=array();
		 
		 //遍历出上传的图片
		 foreach($info as $key=>$v){
		 	$path[$v['key']]=$info[$key]['savename'];

		 	//现将之前的LOGO删除
			unlink('./Uploads/Logo/'.C($v['key']));		 

         //读取出配置文件
		$arr=include('./APP/Conf/logo.php');
        $arr=array_merge($arr,$path);

        //将内容写进配置文件中
        F('logo',$arr,'./APP/Conf/');

        $this->success('修改成功',U('pic'));

        }
    }









}