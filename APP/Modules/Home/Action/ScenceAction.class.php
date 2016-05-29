<?php
/*
 @der  工单管理控制器
*/
Class ScenceAction extends CommonAction{

	/*
      @der 我的工单页面
	*/
    public function index(){
      $db=D('WoView');
      $where=array('uid'=>$_SESSION['id'],'del'=>0);

      //调用Model中的方法处理搜索
      list($where,$pwhere,$tmp)=$db->serach($where);
      
      //使用分页类
      $page=X($db,$where,10,$pwhere);
      //列出用户所有的工单
      $data=$db->where($where)->order('id DESC')->limit($page['0']->limit)->select();
      
      $this->data=$data;
      $this->fpage=$page['1'];
      $this->tmp=$tmp;
    	$this->display();
    }

    /*
     @der 工单详细页面
    */
    public function scenceInfo(){
        if(empty($_GET['id']))  redirect(__APP__);
        $db=D('Woreply');
        
        //调用Model中的方法获取详细信息
        $wo=$db->get_info($this);
       
        //如果不是该用户的工单，跳转到首页
        if($wo['uid']!=$_SESSION['id']) redirect(__APP__);

        //查找出客服回复的时间
        $this->replayTime=M('wo_record')->where(array('wid'=>$wo['id'],'type'=>2))->getField('time');
       
        if($wo['status']>1){
             //计算出工单实际所处理的时间
             $this->time=formatTime($wo['end']-$this->replayTime);
        }

        $this->display();
    }


    /*
      提交工单的选择问题类型页面
    */
    public function selectType(){

      $data=catArr('Scencecat');

      $this->data=$data;

    	$this->display();
    }

    /*
      @der 问题选择界面
    */
    public function scence(){
      if(empty($_GET['id'])) redirect(__APP__);

      $id=$this->_get('id','intval');

      //如果不是系统型工单，跳转回首页
      $scence=M('Scencecat')->field('type,cat')->where(array('id'=>$id))->find();
      if($scence['type']!=0)  redirect(__APP__);
      
      //查找出用户的手机
      $this->tele=M('Userinfo')->where(array('uid'=>$_SESSION['id']))->getField('telephone');      
      //查找出该分类的表单
      $this->form=$this->_reckon($id);
      
      $this->scenceCat=$scence['cat'];
      $data=catArr('Scencecat',$id);
      $this->data=$data;
    	$this->display();
    }


    /*
      @der 非系统型提交工单的页面
    */
    public function add(){
      if(empty($_GET['id'])) redirect(__APP__);
      
      $id=$this->_get('id','intval');
      //如果不是系统型工单，跳转回首页
      if(M('Scencecat')->where(array('id'=>$id))->getField('type')!=1)  redirect(__APP__);
      $this->_getInfo($id);
      
      
      $this->display();
    }

    /*
      @der 从服务提交过来的收费工单
    */
    public function service(){
       if(empty($_GET['per'])) redirect(__APP__);

       //通过解密函数解密出服务ID和工单类型
       list($id,$sid)=explode('||',mymd5($this->_get('per'),true));
       if(!preg_match('/^\d+$/',$id)||!preg_match('/^\d+$/',$sid)) redirect(__APP__);
       $_SESSION['sid']=$sid;
       
       $this->_getInfo($id);
      
      $this->display('add');
    }

    /*
      @der 查找分类添加信息的方法
      @parm int $id 分类ID
    */
    private function _getInfo($id){
      //查找出用户的手机
      $this->tele=M('Userinfo')->where(array('uid'=>$_SESSION['id']))->getField('telephone');      
      //查找出该分类下的问题
      $problem=M('Scencecat')->field('id,cat')->where(array('pid'=>$id))->select();
      $this->data=$problem;

      //查找出该分类的表单
      $this->form=$this->_reckon($id);
      $this->id=$id;  
    }



    /*
     @der 处理客户留言的方法
    */  
    public function words(){
       if(!$this->ispost()) redirect(__APP__);    

       $add=array(
           'wid'=>$this->_post('id','intval'),  //所属工单ID
           'time'=>time(),                      //留言时间
           'title'=>$this->_post('work'),      //留言内容     
        );

       if($id=M('Woreply')->add($add)){ 
          //调用文件上传方法
          $this->_uploadFile(M('Woreply_img'),$id,'rid');          

           //跳转回工单信息页面
           $this->redirect('scenceInfo',array('id'=>$_POST['id']));

       }else{
          $this->error('留言失败请重试');
       }

        
    }

    /*
      @der 提交工单的处理方法
    */
    public function scenceSubmit(){
      if(!$this->ispost()) redirect(__APP__);

      //添加到数据库的数据
      $add=array();
      $arr['uid']=$_SESSION['id'];  //所属用户
      $arr['time']=time();         //创建时间
      $arr['number']= work_order(7); //工单号
      $arr['title']=$this->_post('title'); //订单标题
      $arr['email']=$this->_post('email'); //联系邮箱
      $arr['cid']=$this->_post('cid','intval'); //所属类别
      $arr['phone']=$this->_post('phone'); //手机号码
      $arr['remark']='';

      //清除POST中的部分值
      unset_post('time','title','email','cid','phone');

      foreach($this->_post() as $key=>$v){
          //判断值是否为空 
          if(!empty($v)){
               //判断是否是复选框
               if(is_array($v)){
                    $arr['remark'] .="<span class='hei'>{$key}</span>：";
                    foreach($v as $vs){
                         $arr['remark'] .="{$vs},";
                    }
                    $arr['remark']=substr($arr['remark'],0,strlen($arr['remark'])-1);
                  
               }else{
                    $arr['remark'].="<span class='hei'>{$key}</span>：{$v}<br/>";
               }
          }
      }
      rtrim($arr['remark'],'<br/>');
      
      //判断提交过来的是否是收费工单
      if(M('Scencecat')->where(array('id'=>$arr['cid']))->getField('type')==2){
          if(empty($_SESSION['sid'])) redirect(__APP__);



          $arr['isCharge']=$_SESSION['sid'];
      }
      
      
     
      //生成工单信息
      if($id=M('Wo')->add($arr)){
          //调用文件上传方法
          $this->_uploadFile(M('Wo_img'),$id,'wid');
          
          //生成工单记录
          create_record(M('Wo_record'),'用户提交工单',array('wid'=>$id,'type'=>1));
          
          if(!empty($_SESSION['sid'])){             
              $db=M('User_service');
              $where=array('id'=>$_SESSION['sid']);              
              //修改服务状态为已提交过工单
              $db->where($where)->setField(array('isSubmit'=>$id));
              $_SESSION['sid']=null;

          }

         //跳转到工单信息页面
         $this->redirect('scenceInfo',array('id'=>$id));

      }else{
        
        $this->error('工单提交失败，请重试');
      }
    }


    /*
     改变工单状态的方法
    */
    public function change(){
        if(empty($_GET['sta'])||empty($_GET['id'])) redirect(__APP__);
        $id=$this->_get('id','intval');
        M('Wo')->where(array('id'=>$id))->setField(array('status'=>$this->_get('sta','intval')));

        if($_GET['sta']==3){
             //生成工单记录
            create_record(M('Wo_record'),'用户关闭了该工单',array('wid'=>$id));
        } elseif($_GET['sta']==1) {
            //生成工单记录
            create_record(M('Wo_record'),'用户问题还没解决，继续提问。',array('wid'=>$id));
        }

        $this->redirect('scenceInfo',array('id'=>$id));

    }

    /*
      用户的评价
    */
    public function appraise(){
        if(!$this->ispost()) redirect(__APP__);
        $db=M('Wo');
        $id=$this->_post('id','intval');
        $where=array('id'=>$id);
        $_POST['status']=3;

        if($db->where($where)->save($_POST)){
          
          if(!empty($_POST['advice'])){
              //添加用户建议
              $add=array(
                   'advice'=>$this->_post('advice'),
                   'wid'=>$id,
                   'time'=>time()
                );

              M('advice')->add($add);
          }

          //生成工单记录
          create_record(M('Wo_record'),'用户评价了该工单，工单已关闭',array('wid'=>$_POST['id']));
          
          //判断该订单是否是收费订单
          $sid=$db->where($where)->getField('isCharge');
          if($sid>0){           
              $stmt=M('User_service');
              $where=array('id'=>$sid);
             
              //判断该服务是否是单次服务
              if($stmt->where($where)->getField('endTime')==0){
                   //修改服务状态为已到期
                   $stmt->where($where)->setField(array('status'=>1));
              }
          }

          $this->redirect('scenceInfo',array('id'=>$id));
        }else{
          $this->error('评价失败，请重试');
        }
    }


    /*
      用户的投诉
    */
    public function complaint(){
        if(!$this->ispost()) redirect(__APP__);

        if(empty($_POST['title'])){
            $this->error('请输入投诉内容，谢谢!');
        }
        $id=$this->_post('id','intval');
       
       //修改该工单为投诉
       if(M('Wo')->where(array('id'=>$id))->setField(array('complaint'=>1))){
            
            $add=array(
               'title'=>'一键投诉：'.$this->_post('title'),
               'wid'=>$id,
               'time'=>time() 
            );

            //加入投诉内容
            M('Woreply')->where(array('wid'=>$id))->add($add);

            //生成工单记录
            create_record(M('Wo_record'),'用户投诉该工单',array('wid'=>$id));
            
            $this->redirect('scenceInfo',array('id'=>$id));
       }else{
            $this->error('投诉过程中遇到问题，请重试！');
       }
    }


    /*
      @der 工单放入回收站的操作
    */
    public function del(){
        $id=$this->_get('id','intval');
        
        if(M('Wo')->where(array('id'=>$id))->setField(array('del'=>1))){        

          $this->redirect('index');

        }else{
          $this->error('删除失败');
        }
    }


    /*
     异步获取工单信息的方法
    */
    public function getScence(){
      if(!$this->isAjax()) redirect(__APP__);
      
      $field=$_POST['type']?'id,title':'id,title,anwer';
      
      //获取工单答案
      if($_POST['type']==0){
           $data=M('Scence')->field($field)->where(array('id'=>$_POST['sid']))->find();

           $data['anwer']=htmlspecialchars_decode($data['anwer']);

           echo json_encode($data);
      
      //获取符合条件的所有工单列表
      }else{
           $data=M('Scence')->field($field)->where(array('cid'=>$_POST['sid']))->select();
           
           $str='';
           
           if(isset($_POST['has'])){

               foreach($data as $v){
                  $str .="<dd class='showForm' sid='{$v['id']}' >{$v['title']}</dd>";

               }
           }else{

               foreach($data as $v){
                  $str .="<li sid='{$v['id']}' >{$v['title']}</li>";

               }
           }

           echo $str;
      }

    }
  
    /*
      异步增加好评或差评的方法
    */
    public function orInc(){
       if(!$this->isAjax()) redirect(__APP__);

       M('Scence')->where(array('id'=>$this->_post('sid','intval')))->setInc($this->_post('type'));

    }

    /*
      异步获取问题类型下属性的方法 
    */
    public function get_attr(){
       if(!$this->isAjax()) redirect(__APP__);
       $cid=$this->_post('cid','intval');
       
       if(!$str=$this->_reckon($cid)) die(0);

       echo $str;
    }


    /*
      处理工单图片上传的方法
      @part object $db 数据库连接对象
      @part int    $id 回复或工单表的ID
      @part string $name [存放图片表对应的字段名]
    */
    private function _uploadFile($db,$id,$name='wid'){
        //如果上传了附件
       if($_FILES['img']['error']['0']==0){
          //调用图片上传方法
          $info=uploadimg('./Uploads/Scence/',true);

          if(is_array($info)){
              $add=array();
              foreach($info as $v){
                   $add[]=array(
                      $name=>$id,
                      'img'=>$v['savename']
                   );
              }
            
            //将图片路径添加到数据库中
            $db->addAll($add);
           

          }else{
             $this->error($info);
          }

       }

    }


    /*
      计算属性的方法
      @part int $cid 分类的ID
    */
    private function _reckon($cid){
        $attr=M('Scenceattr')->where(array('cid'=>$cid))->order('sort DESC')->select();

       if(empty($attr)) return false;
       
       $str='';
       $arr=array();
       //组合数据返回
       foreach($attr as $v){      

            $str .="<dl class='temporary'>";
            
            //判断是否是必填项
            if($v['must']==1)
                $str .="     <dt class='require'><span>*</span>{$v['title']}</dt>"; 
            else
                $str .="     <dt>{$v['title']}</dt>"; 

            $str .="     <dd>";      
            
            //判断属性的类型
            switch ($v['type']) {
              case '0'://文本框                       
                        $str .="<input type='text' class='text' name='{$v['title']}'/>";
                             
                  break;

              case '1'://复选框
                       $arr=explode('||',$v['name']);

                       foreach($arr as $key=>$vs){
                              $str .="<span class='checkbox'><input id='t{$key}' type='checkbox' name='{$v['title']}[]' value='{$vs}'/><label for='t{$key}'>{$vs}</label></span>";
                       }
                   
                   break;

              case '2'://标题
                      
                        $str .="{$v['name']}";
                   break;

              case '3'://单选按钮
                       $arr=explode('||',$v['name']);
                     
                       foreach($arr as $vs){
                              $str .="<span class='radio'><input id='t{$v['id']}' type='radio' value='{$vs}' name='{$v['title']}'/><label for='t{$v['id']}'>{$v['title']}</label></span>";
                       }
                   
                   break;
            }

            $str .="     </dd>";
            $str .="</dl>";
       }

       return $str;
    }
}