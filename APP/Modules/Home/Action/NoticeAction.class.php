<?php
/**
 * @der 公司动态和公司公告
 */
Class NoticeAction extends Action{

	/**
	 * @der 公司公告主页
	 */
	public function index(){
	    
	    $field = 'id,title,create_time,content';
		$data  = M('Notice')->field($field)->where($where)->order('sort DESC')->select();
		foreach ($data as $key=>$v) {
			$data[$key]['source']  = '中电云集';
		}

		$this->href = 'detail';
		$this->data = $data;
		$this->display();
	}

	/**
     * @der 公司公告详情页面   
	 */
	public function detail(){
		$where = array('id' => I('get.id',1,'intval'));
		$field = 'id,title,create_time,content';
		$data  = M('Notice')->field($field)->where($where)->order('sort DESC')->find();
	
		$data[$key]['source']  = '中电云集';
		$this->data = $data;
		$this->display();
	}

	/**
 	 * @der 公司动态列表页
	 */
	public function dynamicList() {
        $cid = I('get.pid', 1, 'intval');
        $where = array('cid' => $cid);
        $field = 'id,title,create_time,source,content';
        $data = M('Dynamic')->field($field)->where($where)->order('sort DESC')->select();
        $this->stitle = $cid == 1 ? '公司动态' : '新闻动态';
        $this->href = 'dynamicDetail';
        $this->data = $data;
        $this->display('index');
    }

    /**
 	 * @der 公司动态详情页面
	 */
	public function dynamicDetail(){
		$where = array('id' => I('get.id',1,'intval'));
		$field = 'id,title,create_time,source,content,uid';
		$data  = M('Dynamic')->field($field)->where($where)->order('sort DESC')->find();
                
                $field2 = 'id,username';
                $where2 = array('id' =>$data['uid']);
                $data2  = M('Admin')->field($field2)->where($where2)->order('id DESC')->find();
		//$data['source']  =$data2['username'];
                //$data['source']  ="ddddd";
                $this->data2 = $data2; 
		$this->data = $data;          
		$this->display('detail');
	}
}