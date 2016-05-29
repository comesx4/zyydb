<?php

//镜像市场控制器
Class ImageproductAction extends Action{

	//主页
	public function index(){
		$demo=mirr_sort('Mirroringcat');
		// p($_SERVER);
		// echo $_SERVER['SERVER_ADDR'].$_SERVER['PHP_SELF'];die;

		$this->display();
	}

	//镜像分类页
	public function mirroring(){
        if(empty($this->_get('id','intval'))) redirect(__APP__);
        $id=$this->_get('id','intval');

        //根据分类ID查找出所有子分类
        $pid=get_son($id);
        $pid[]=$id;
        $pid=implode(',',$pid);

        //导入分页类
        import('Class.Page',APP_PATH);
        $num=M('Mirroring')->where(array('cid'=>array('in',$pid),'type'=>1))->count();
        $page=new Page($num,5);
      
        $where=array('mirroring.cid'=>array('in',$pid),'mirroring.type'=>'1');

		$mirr=D('MirroringView')->where($where)->order('id DESC')->limit($page->limit)->select();			
	
		$this->mirr=$mirr;	
        
        if($num>5)
		      $this->fpage=$page->fpage();
		

        $this->display();
	}


	//镜像详细信息
	public function mirrInfo(){
		if(empty($id=$this->_get('id','intval'))) redirect(__APP__);

		//查找出该镜像的所有产品ID跟ID
		$gid=M('Mirr_goods')->field('id,goods_id AS gid')->where(array('mirr_id'=>$id))->select();

		//查找出产品LOGO
		$where=array('id'=>array('IN',implode(',',peatarr($gid,'gid'))));		
		$goods=M('Goods')->field('id,img,goods')->where($where)->select();
		
		//根据ID找出对应的多个或单个地域
		foreach($gid as $key=>$v){

			//查找对应的城市表ID
			$cid=M('Mirr_city')->field('cid')->where(array('sid'=>$v['id']))->select();
			$where=array('id'=>array('IN',implode(',',peatarr($cid,'cid'))));

			$gid[$key]['city']=M('City')->field('city,id')->where($where)->select();

		}
		
		//产品的地域
		$this->gcity=$gid;

		//所有产品
		$this->goods=$goods;


		//查找出该镜像的详细信息
		$mirr=D('MirroringView')->where(array('id'=>$id))->find();

		//查找出该镜像的可用地域
		$city=M('Mirr_city')->field('cid')->where(array('mid'=>$id))->select();
		$city=implode(',',peatarr($city,'cid'));
		$where=array('id'=>array('in',$city));

		$city=M('City')->field('city')->where($where)->select();
        $str='';
		foreach($city as $v){
			$str.=$v['city'].',';
		}
		$city=rtrim($str,',');	

		//查找出该镜像的信息
	    $this->content=M('Mirroringinfo')->where(array('mid'=>$id))->getField('content');	

	    //套餐
	    $this->groom=include './Data/groom.php';

		$this->city=$city;

		$this->mirr=$mirr;
		

		$this->display();

	}


	//计算价钱的方法
	public function get_price(){
        if(!$this->ispost()) redirect(__APP__);


        //获取套餐数组
        $arr=include './Data/groom.php';        
        $arr=$arr[$_POST['type']];       	

        $price=array(
        	'cpu'=>$arr['cpu'],
        	'memory'=>$arr['memory'],
        	'daikuans'=>$arr['daikuans'],
        	'hard'=>$arr['hard'],
        	'region'=>$_POST['city'],
        	'time'=>$_POST['time'],
        	'goodsId'=>$_POST['gid'],
        	'num'=>1
        );
        
        //调用计算价钱函数
        countPrice($price);


	}

}