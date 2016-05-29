<?php
     Class IndexAction extends CommonAction{

     	//后台主页控制器
     	public function index(){
               //查找出用户所有的节点ID
               $role_id=M('Role_user')->field('role_id')->where(array('user_id'=>$_SESSION['uid']))->select();
     		$where=array('role_id'=>array('in',implode(',',peatarr($role_id,'role_id'))));
               $node_id=M('Access')->field('node_id')->where($where)->select();
               $node_id=implode(',',peatarr($node_id,'node_id'));

               //查找出所有的分类
               $arr=array();
               $cat=catArrByStatus('Nodecat');
               $db = M('Node');
               foreach($cat as $v){
                   
                     //查找出对应的节点
                    foreach($v['chid'] as $vs){
                   
                         //超级管理员的识别
                         if($_SESSION['username']==C('RBAC_SUPERADMIN'))
                              $where=array('cid'=>$vs['id'],'level'=>3,'type'=>0);
                         else
                              $where=array('cid'=>$vs['id'],'id'=>array('in',$node_id),'type'=>0,'level'=>3);

                         $info=$db->where($where)->order('sort DESC')->select();
                        
                         $arr[$v['id']][$vs['cat']]=$info;
                    }

                    unset($v['chid']);
                    //二级分类
                    $top[]=$v;
               }
              
               $this->top=$top;
               $this->data=$arr; 
               $this->display('index');
     	}

     	//欢迎界面
     	public function welcome(){
     		echo '智御云后台管理';
     	}

          //用户列表
          public function userList(){
               $this->user=M('User')->field('passwrod',true)->select();
               $this->display();
          }


          //小云问答处的问题管理
          public function problem(){
               $db=M('Problem');
               //调用排序函数
               cat_sort('Problem');

               $this->problem=$db->field('content',true)->order('sort ASC')->select();
                $this->display();

          }

          //添加问题视图 
          public function add(){
               //调用百度文本编辑器
               $this->ueditor=ueditor();
               $this->display();

          }

          //将问题添加到数据库
          public function insert(){
               if(!$this->ispost()) $this->redirect('Index/index');
               //p($_POST['content']);die;

               if(empty($_POST['title'])||empty($_POST['content'])){
                    $this->error('标题或内容不能为空');
               }

               if(M('Problem')->data($_POST)->add())
                      $this->success('添加成功',U('problemAdd'));
               else
                      $this->error('添加失败请重试');
          }

          //问题列表的修改界面
          public function update(){
               $where=array('id'=>$this->_get('id','intval'));
               //调用百度文本编辑器
               $this->ueditor=ueditor();
               //传递值到模板中
               $this->problem=M('Problem')->where($where)->find();
               $this->display();
          }


          //问题的修改到数据库
          public function save(){
               if(!$this->ispost()) $this->redirect('Index/index');

               if(M('problem')->save($_POST))
                    $this->success('修改成功',U('problem'));
               else
                    $this->error('修改失败');
          }

          //异步删除问题
          public function problemDel(){
               if(!$this->ispost()) $this->redirect('Index/index');
               $where=array('id'=>$this->_post('cid'));
               if(M('Problem')->where($where)->delete())
                    echo 1;
               else
                    echo 0;
          }
     } 