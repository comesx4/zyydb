<?php
/*
  @der 用户产品控制器
*/
Class UserGoodsAction extends CommonAction{

	/*
      @der 用户已购买的产品列表
	*/
    public function index(){
        
    	$db=M('Living');
        $where=array('uid'=>$_SESSION['id']);
		 //导入分页类
        import('Class.Page',APP_PATH);

        $sum=$db->where($where)->count();

        $page=new Page($sum,10);
		//查找出该用户的所有实例
		//$goods=$db->query("SELECT l.id as `id`,`tid`,`start`,`end`,`status`,`remark`,g.goods as `name` FROM kz_living l LEFT JOIN kz_goods g ON l.gid=g.id WHERE l.uid={$_SESSION['id']} ORDER BY l.id DESC LIMIT {$page->limit}");
		$where = array('uid' => $_SESSION['id']);
        $goods = D('LivingView')->where($where)->limit($page->limit)->order('id DESC')->select();
 
        $this->goods=$goods;		
		$this->fpage=$page->fpage();
	    $this->goodsList=goodsList('goods');
		
    	$this->display();  
    }

    /*
        @der 用户产品详细信息
    */
    public function goodsInfo(){
        $where = array('id' => I('get.id',0,'intval'));

        $goods = D('LivingView')->where($where)->find();      
        $table = $goods['table_name'];
        //$table = str_replace('kz_','',$goods['table_name']);
        //$field = 'cpu,memory,disk,band,city,osid';
        $data = M($table)->field(true)->where(array('id'=>$goods['tid']))->find();
     
        $data = array_merge($goods,$data);
       
        $this->goods=$data;
        $this->display();
    }

    
}