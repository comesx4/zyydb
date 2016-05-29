	<?php  
  Class TagLibHd extends TagLib{
   protected $tags=array(
	   'userinfo'=>array('attr'=>'','close'=>1),
	   'friend'=>array('attr'=>'limit','close'=>1),
	   'problem'=>array('attr'=>'limit','close'=>1),
	   'nav'=>array('attr'=>'limit','close'=>1),
     'mleft'=>array('attr'=>'','close'=>1),
     'helpNav'=>array('attr'=>'','close'=>1),
     'helpAll'=>array('attr'=>'','close'=>1), //帮助中心的所有分类
	   'home_nav'=>array('attr'=>'limit','close'=>1), //帮助中心的所有分类
     'notice_cat' => array('attr'=>'limit','close'=>1), //公司动态的分类
   
   );
   
   

   //用户个人信息
   public function _userinfo($attr,$content){
	   $attr=$this->parseXmlAttr($attr);
	   $str=<<<str
<?php
	   \$field=array('face','uid');
	   \$user=M('Userinfo')->field(\$field)->where(array('uid'=>\$_SESSION['id']))->find();
	   \$user['money']=M('Money')->where(array('uid'=>\$_SESSION['id']))->getField('money');
?>
str;
	    $str .=$content;
	    return $str;
   }



   //小云的问题
   public function _problem($attr,$content){
   	   $attr=$this->parseXmlAttr($attr);
   	   $str=<<<str
<?php
   	   \$stmt=M('Problem')->field('title,content')->limit(\$attr['limit'])->select();
   	   foreach(\$stmt as \$v):
?>

str;
       $str .=$content;
       $str .='<?php endforeach; ?>';
       return $str;

   }

   //首页导航栏的产品信息
   public function _nav($attr,$content){
   	   $attr=$this->parseXmlAttr($attr);
   	   $str=<<<str
 <?php
       //查找出所有的顶级分类
       \$cat=M('Goodscat')->field('id,cat')->limit({$attr['limit']})->select();
       \$db=M('Goods');
       //按顺序求出每个分类下的产品
       foreach(\$cat as \$key=>\$v){
       	   \$where=array('cid'=>\$v['id']);       	   
       	   \$cat[\$key]['goods']=\$db->where(\$where)->order('sort asc')->field('id,goods')->select();
       }

       foreach(\$cat as \$v):


 ?>
str;
      $str .=$content;
      $str .='<?php  endforeach; ?>';

      return $str;

   }

   //镜像市场的左边公共部分
   public function _mleft($attr,$content){
       $attr=$this->parseXmlAttr($attr);
       $str=<<<str
  <?php
       //取出出所有分类信息
       \$cat=mirr_sort('Mirroringcat');

       foreach(\$cat as \$v):
  ?>
str;
       $str .=$content;
       $str .='<?php endforeach; ?>';

       return $str;

   }


   //帮助中心左半边公用部分
   public function _helpNav($attr,$content){
       $attr=$this->parseXmlAttr($attr);
       $str=<<<str
  <?php
       \$pid=\$_GET['pid'];
        
       \$chid=M('Helpcat')->field('id,pid,cat')->where(array('id'=>\$pid))->select();
    
       \$chid['0']['chid']=catArr('Helpcat',\$pid);

       foreach(\$chid as \$v):

       ?>
str;
       $str .=$content;
      
       $str .='<?php endforeach; ?>';

       return $str;
   }


   //帮助中心的所有分类
   public function _helpAll($attr,$content){
       $attr=$this->parseXmlAttr($attr);
       $str=<<<str
<?php
       \$all=catArr();

       foreach(\$all as \$v):
     
     ?>
str;
      $str .=$content;
      $str .='<?php endforeach; ?>';
       
       return $str;

   }

   /*
      @der 首页头部导航
   */
  public function _home_nav($attr , $content){
       $attr=$this->parseXmlAttr($attr);

      // <<< html输出  str 标记前后配对
       $str=<<<str
    <?php
       \$goods = M('Goods')->field('goods,id')->order('sort ASC')->limit({$attr['limit']})->select();
       foreach (\$goods as \$value):
    ?>
str;
		$str .= $content;
		$str .= '<?php endforeach; ?>';
                  trace($attr['limit']);
		return $str;
  }

     /*
      @der 公告页面左半服务的分类
   */
  public function _notice_cat($attr , $content){
       $attr=$this->parseXmlAttr($attr);
       $str=<<<str
    <?php
       \$cat = M('Dynamic_cat')->field('cat_name,id')->order('sort DESC')->limit({$attr['limit']})->select();
      
       foreach (\$cat as \$v):
    ?>
str;
    $str .= $content;
    $str .= '<?php endforeach; ?>';
    return $str;
  }




  
  
  
  
  
  
  
  
  }
            