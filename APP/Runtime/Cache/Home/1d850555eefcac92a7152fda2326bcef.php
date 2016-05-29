<?php if (!defined('THINK_PATH')) exit();?>  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <title><?php echo (makwebtitle($stitle)); ?></title>
 
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<link rel="stylesheet" href="__PUBLIC__/Css/com-header.css" />
<link rel="stylesheet" href="__PUBLIC__/Css/bottom.css" />    
<link href="__PUBLIC__/Bootstrap/css/bootstrap.min.css" rel="stylesheet">

<script type="text/javascript" src="__PUBLIC__/Bootstrap/js/jquery-1.12.2.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/Bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/Js/home-header.js"></script>
<script type="text/javascript" src="__PUBLIC__/Js/bottom.js"></script>
  <link rel="stylesheet" href="__PUBLIC__/Css/index_1.css" /> 
  <link rel="stylesheet" href="__PUBLIC__/Css/index.css" /> 
    <link rel="stylesheet" href="__PUBLIC__/Css/index/hzdw.css" /> 
</head>
<body>
   <div id='body'>
  <!-- 顶部公共部分 -->
    <!--顶部头-->
	<div class="ay-global-topbar hidden">
		<div class="y-topbar-row">
			<div class='left'>
                <?php if($_SESSION['id']== null): ?>欢迎上云，<a href="<?php echo U('Login/index');?>">请登录&nbsp;|&nbsp;</a><a href="<?php echo U('Login/register');?>">注册</a>
                <?php else: ?>
                <span class='name showdiv'><?php echo (session('username')); ?>
                    <?php
 $field=array('face','uid'); $user=M('Userinfo')->field($field)->where(array('uid'=>$_SESSION['id']))->find(); $user['money']=M('Money')->where(array('uid'=>$_SESSION['id']))->getField('money'); ?><div class='userinfo div'>
                        <dl>
                            <dt>
                                <a href="<?php echo U('User/index');?>">
                            <?php if($user['face'] != ''): ?><img width='40' src='__ROOT__/Uploads/Face/<?php echo ($user["face"]); ?>'/>
                            <?php else: ?>
                              <img src='__PUBLIC__/Images/face.png'/><?php endif; ?>
                              </a>
                            </dt>
                            <dd class='qqq' style='color:#FFCC00;'>￥<?php echo ($user["money"]); ?></dd>
                            <dd><a href="<?php echo U('User/recharge');?>">充值</a></dd>
                        </dl>
                        <ul>                          
                            <li><a href="<?php echo U('Login/loginOut');?>">退出</a></li>
                        </ul>

                    </div>

                </span><?php endif; ?>                        
            </div>			
                    <div class='right'>
                        <ul>
                            <li> | <a href="javascript:window.external.AddFavorite('http://http://zyy.yjcom.com.cn/','<?php echo C('WEBSITE_SET.SITE_NAME');?>')">收藏本站</a></li>                                
                            <li>|&nbsp;<a href="<?php echo U('Help/index');?>">帮助中心</a></li>
                            <?php if($_SESSION['id']!= null): ?><li>|&nbsp;<a href="<?php echo U('Login/loginOut');?>">退出</a> | </li>
                               
                                <li>&nbsp;<a href="<?php echo U('User/index');?>">用户中心</a></li>
                                <!--<li>|&nbsp;<a target='_blank' href="<?php echo U('console/Server/index');?>">管理控制台</a></li>--><?php endif; ?>
                            <li><?php echo welcoming();?></li>
                        </ul>
                    </div>
		</div>
	</div>
	<!--顶部头结束-->

	<!--顶部导航栏-->
        <div id='header-wrap' class="y-row">	
		<div class='header-inner'>
			<div class='left'>
			    <img height='30' src='__PUBLIC__/default/Images/logo2.jpg'/>                     
      
                            <div id="navbar" class="navbar-collapse collapse">
                                <ul class='nav navbar-nav'>
                                    <li <?php if(MODULE_NAME == 'Index'): ?>class='active'<?php endif; ?>><a href="<?php echo U('Index/index');?>">首页</a></li>
                                    <li class="dropdown <?php if(MODULE_NAME == 'About'): ?>active<?php endif; ?>">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">公司介绍 <span class="caret"></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="<?php echo U('About/index');?>">公司介绍</a></li>
                                            <li><a href="<?php echo U('About/product');?>">产品介绍</a></li>
                                            <li><a href="<?php echo U('About/anli');?>">成功案例</a></li>
                                            <li><a href="<?php echo U('About/jifang');?>">机房介绍</a></li>                 
                                        </ul>                                        
                                    </li>                                  
                                    <li <?php if(MODULE_NAME == 'Pinfo' && ACTION_NAME == 'dj'): ?>class='active'<?php endif; ?>><a href="<?php echo U('Pinfo/dj');?>">单机租用托管</a></li>
                                 <li <?php if(MODULE_NAME == 'Pinfo' && ACTION_NAME == 'zg'): ?>class='active'<?php endif; ?>><a href="<?php echo U('Pinfo/zg');?>">整柜租用托管</a></li>
                                <li <?php if(MODULE_NAME == 'Pinfo' && ACTION_NAME == 'pf'): ?>class='active'<?php endif; ?>><a href="<?php echo U('Pinfo/pf');?>">普防</a></li>
                             <li <?php if(MODULE_NAME == 'Pinfo' && ACTION_NAME == 'gf'): ?>class='active'<?php endif; ?>><a href="<?php echo U('Pinfo/gf');?>">高防</a></li>                                 
                                    <li  <?php if(MODULE_NAME == 'Cuxiao'): ?>class='active'<?php endif; ?>><a href="<?php echo U('Cuxiao/index');?>">促俏活动</a></li> 
                                       <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">会员中心<span class="caret"></span></a>
                                        <ul class="dropdown-menu">
                                            <?php if($_SESSION['id']== null): ?><li><a href="<?php echo U('Login/index');?>">会员登录</a></li>
                                                <li><a href="<?php echo U('Login/register');?>">用户注册</a></li>
                                                <li><a href="#">立刻订购</a></li> 
                                                <?php else: ?>
                                                <li><a href="<?php echo U('User/index');?>">用户中心</a></li> 
                                                 <li><a href="<?php echo U('User/dindan');?>">立刻订购</a></li> 
                                                <li><a href="<?php echo U('Login/loginOut');?>">退出</a></li><?php endif; ?>
                                        </ul>                                        
                                    </li>
                                </ul>

                            </div>
                        </div>		   
		</div>
	</div>
        
        
	<!--导航栏结束-->
    <!-- 顶部公共结束 -->

  <!--图片轮播-->
  <div id="myCarousel" class="carousel slide carousel-fade "  data-ride="carousel" data-interval="2000">
   <!-- 轮播（Carousel）指标 -->
   <ol class="carousel-indicators">
      <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
      <li data-target="#myCarousel" data-slide-to="1"></li>
      <li data-target="#myCarousel" data-slide-to="2"></li>
      <li data-target="#myCarousel" data-slide-to="3"></li>
      <li data-target="#myCarousel" data-slide-to="4"></li>
      <li data-target="#myCarousel" data-slide-to="5"></li>
      <li data-target="#myCarousel" data-slide-to="6"></li>
   </ol>   
   <!-- 轮播（Carousel）项目 -->
   <div class="carousel-inner">
      
      <div class="item active"  style="background:#201f2d">
          <div class="bannerImg" style="background:url('__PUBLIC__/Images/home/banner_img1.jpg') no-repeat center center"> 
              <div class="carousel-caption"></div>                  
          </div>
      </div>
      <div class="item"  style="background:#201f2d">
          <div class="bannerImg" style="background:url('__PUBLIC__/Images/home/banner_img2.jpg') no-repeat center center"> 
              <div class="carousel-caption"></div>                  
          </div>
      </div>
    <div class="item"  style="background:#201f2d">
          <div class="bannerImg" style="background:url('__PUBLIC__/Images/home/banner_img3.jpg') no-repeat center center"> 
              <div class="carousel-caption"></div>                  
          </div>
      </div>
       <div class="item"  style="background:#201f2d">
          <div class="bannerImg" style="background:url('__PUBLIC__/Images/home/banner_img4.jpg') no-repeat center center"> 
              <div class="carousel-caption"></div>                  
          </div>
      </div>
        <div class="item"  style="background:#201f2d">
          <div class="bannerImg" style="background:url('__PUBLIC__/Images/home/banner_img5.jpg') no-repeat center center"> 
              <div class="carousel-caption"></div>                  
          </div>
      </div>
        <div class="item"  style="background:#201f2d">
          <div class="bannerImg" style="background:url('__PUBLIC__/Images/home/banner_img6.jpg') no-repeat center center"> 
              <div class="carousel-caption"></div>                  
          </div>
      </div>
        <div class="item"  style="background:#201f2d">
          <div class="bannerImg" style="background:url('__PUBLIC__/Images/home/banner_img7.jpg') no-repeat center center"> 
              <div class="carousel-caption"></div>                  
          </div>
      </div>
   </div>
   <!-- 轮播（Carousel）导航 -->
   <a class="carousel-control left" href="#myCarousel" 
      data-slide="prev"> </a>
   <a class="carousel-control right" href="#myCarousel" 
      data-slide="next"> </a>
</div>
  <!--图片轮播结束-->

  <!--公告区域-->
  <div class="home-notice y-row">
    <?php if(is_array($notice)): foreach($notice as $key=>$v): ?><div class="y-span3 ">                
                <span class="icon icon-bullhorn">&nbsp;</span>
                <span>·</span>                
            <a href="<?php echo U('Notice/detail',array('id' => $v['id']));?>" target="" class="home-grey">
                <span class="date">[<?php echo (date('Y-m-d',$v["create_time"])); ?>]</span>
                <?php echo ($v["title"]); ?>
            </a>
    </div><?php endforeach; endif; ?>     
        <div class="y-span3 ">
            <img width="260" style="max-width:100%;position:relative;top:10px" src="http://gtms04.alicdn.com/tps/i4/TB153fzFVXXXXc.XpXXGdSuUVXX-520-24.png">
        </div>
  </div>
  <!--公告区域结束-->
   <!--服务开通-->
  <div class="row y-row">
        <div class="col-lg-4">
          <h2>单机租用托管</h2>
          <p class="text-danger">与优秀IDC服务商合作，打造高性能DDOS防御云清洗平台云架构智能调度中心，
              可快速跨机房调度防御资源灵活API扩展更多合作渠道，防御资源不断增加
          <p><a class="btn btn-default" href="<?php echo U('Pinfo/dj');?>" role="button">查看详情 &raquo;</a></p>
        </div>
        <div class="col-lg-4">
          <h2>整柜租用托管</h2>
          <p>针对网络7层协议全方位层级防御；防御策略设置全面、灵活、精准；
                  专业的攻击分析，可有效防御未知种类攻击；高级体验，客户可灵活自调整防御策略</p>
          <p><a class="btn btn-default" href="<?php echo U('Pinfo/zg');?>" role="button">查看详情 &raquo;</a></p>
       </div>
        <div class="col-lg-4">
          <h2>普防</h2>
          <p>DDoS防御最前沿技术人员和多年防御运维经验是服务最宝贵的核心力量；
                  源站监控告警服务，时刻关心客户资源异常；7*24小时全心全意全方位服务
          </p>
          <p><a class="btn btn-default" href="<?php echo U('Pinfo/pf');?>" role="button">查看详情 &raquo;</a></p>
        </div>
     
        <div class="col-lg-4">
          <h2>高防</h2>
          <p >与优秀IDC服务商合作，打造高性能DDOS防御云清洗平台云架构智能调度中心，
              可快速跨机房调度防御资源灵活API扩展更多合作渠道，防御资源不断增加
          <p><a class="btn btn-default" href="<?php echo U('Pinfo/gf');?>" role="button">查看详情 &raquo;</a></p>
        </div>  
        <div class="col-lg-4">
          <h2>促俏活动</h2>
          <p>DDoS防御最前沿技术人员和多年防御运维经验是服务最宝贵的核心力量；
                  源站监控告警服务，时刻关心客户资源异常；7*24小时全心全意全方位服务
          </p>
          <p><a class="btn btn-default" href="<?php echo U('cuxiao/index');?>" role="button" target="_blank">查看详情 &raquo;</a></p>
        </div>
      </div>
    <!--服务开通结束-->
  
     <!--合作单位-->
     <div class="" style="background: #ccc;">
    <div class="container">
        <div class="row-fluid">
            <div class="span12">
                <div class="carousel slide" id="myCarouse2">
                    <div class="carousel-inner">
                        <div class="item active">
                            <ul class="thumbnails">
                                <li class="span3">
                                    <div class="thumbnail">
                                        <a href="#" ><img src="__PUBLIC__/default/images/cnert.jpg" alt=""></a>
                                    </div>
                                    <div class="caption">
                                        <h3>国家互联网应急中心</h3>
                                        <p>CNCERT是国内第一批具有一级风险评估资质的单位之一。自2008年对外提供风险评估服务以来，共承担了229个风险评估项目，项目遍及全国31个省市自治区，积累了丰富的经验。同时，CNCERT拥有一支国内顶尖的渗透测试团队，可针对各种架构的web系统、通信网络、信息系统提供渗透测试服务。
                                        </p>
                                        <span class="pull-left"></span>
                                        <a class="pull-right " href="#"><i class="glyphicon glyphicon-heart glyphicon2"></i></a>
                                    </div>
                                </li>
                                <li class="span3">
                                    <div class="thumbnail">
                                        <a href="#"><img src="__PUBLIC__/default/images/ca.png"  alt=""></a>
                                    </div>
                                    <div class="caption">
                                        <h3>长安通信科技有限责任公司</h3>
                                        <p>长安通信科技有限责任公司（简称“长安通信”）成立于2002年，是国家互联网应急中心全资公司。长安通信具有定向保密资质、软件企业认定证书、软件产品登记证书、高新技术企业证书、通信信息网络系统集成企业资质证书等资质，是工业和信息化部、国家互联网应急中心及各省分中心、各省通信管理局、专用通信局、电信运营商、网络服务商以及专网客户等单位在网络信息安全方面的重要技术支撑单位。
                                        </p>
                                    <span class="pull-left"></span>
                                        <a class="pull-right " href="#"><i class="glyphicon glyphicon-heart glyphicon2"></i></a>   
                                    </div>
                                </li>
                                <li class="span3">
                                    <div class="thumbnail">
                                        <a href="#"><img src="__PUBLIC__/default/images/hzjc.jpg"  alt=""></a>
                                    </div>
                                    <div class="caption">
                                        <h3>杭州市基础信息安全测评认证中心</h3>
                                        <p>杭州市基础信息安全测评认证中心（简称HZTEC）是浙江省内第一家专业从事信息安全测试、评估业务的第三方权威测评机构。中心具有中国合格评定国家认可委员会（CNAS）颁发的“A类检查机构”的认可资质，可出具各类信息安全评估报告。
                                        </p>
                                         <span class="pull-left"></span>
                                        <a class="pull-right " href="#"><i class="glyphicon glyphicon-heart glyphicon2"></i></a>   
                                    </div>
                                </li>
                                <li class="span3">
                                    <div class="thumbnail">
                                        <a href="#" ><img src="__PUBLIC__/default/images/cnert.jpg" alt=""></a>
                                    </div>
                                    <div class="caption">
                                        <h3>国家互联网应急中心</h3>
                                        <p>CNCERT是国内第一批具有一级风险评估资质的单位之一。自2008年对外提供风险评估服务以来，共承担了229个风险评估项目，项目遍及全国31个省市自治区，积累了丰富的经验。同时，CNCERT拥有一支国内顶尖的渗透测试团队，可针对各种架构的web系统、通信网络、信息系统提供渗透测试服务。
                                        </p>
                                        <span class="pull-left"></span>
                                        <a class="pull-right " href="#"><i class="glyphicon glyphicon-heart glyphicon2"></i></a>
                                    </div>
                                </li>
                              </ul>
                        </div><!-- /Slide1 -->
                        <div class="item">
                             <ul class="thumbnails">
                                <li class="span3">
                                    <div class="thumbnail">
                                        <a href="#" ><img src="__PUBLIC__/default/images/cnert.jpg" alt=""></a>
                                    </div>
                                    <div class="caption">
                                        <h3>国家互联网应急中心</h3>
                                        <p>CNCERT是国内第一批具有一级风险评估资质的单位之一。自2008年对外提供风险评估服务以来，共承担了229个风险评估项目，项目遍及全国31个省市自治区，积累了丰富的经验。同时，CNCERT拥有一支国内顶尖的渗透测试团队，可针对各种架构的web系统、通信网络、信息系统提供渗透测试服务。
                                        </p>
                                        <span class="pull-left"></span>
                                        <a class="pull-right " href="#"><i class="glyphicon glyphicon-heart glyphicon2"></i></a>
                                    </div>
                                </li>
                                <li class="span3">
                                    <div class="thumbnail">
                                        <a href="#"><img src="__PUBLIC__/default/images/ca.png"  alt=""></a>
                                    </div>
                                    <div class="caption">
                                        <h3>长安通信科技有限责任公司</h3>
                                        <p>长安通信科技有限责任公司（简称“长安通信”）成立于2002年，是国家互联网应急中心全资公司。长安通信具有定向保密资质、软件企业认定证书、软件产品登记证书、高新技术企业证书、通信信息网络系统集成企业资质证书等资质，是工业和信息化部、国家互联网应急中心及各省分中心、各省通信管理局、专用通信局、电信运营商、网络服务商以及专网客户等单位在网络信息安全方面的重要技术支撑单位。
                                        </p>
                                    <span class="pull-left"></span>
                                        <a class="pull-right " href="#"><i class="glyphicon glyphicon-heart glyphicon2"></i></a>   
                                    </div>
                                </li>
                                <li class="span3">
                                    <div class="thumbnail">
                                        <a href="#"><img src="__PUBLIC__/default/images/hzjc.jpg"  alt=""></a>
                                    </div>
                                    <div class="caption">
                                        <h3>杭州市基础信息安全测评认证中心</h3>
                                        <p>杭州市基础信息安全测评认证中心（简称HZTEC）是浙江省内第一家专业从事信息安全测试、评估业务的第三方权威测评机构。中心具有中国合格评定国家认可委员会（CNAS）颁发的“A类检查机构”的认可资质，可出具各类信息安全评估报告。
                                        </p>
                                         <span class="pull-left"></span>
                                        <a class="pull-right " href="#"><i class="glyphicon glyphicon-heart glyphicon2"></i></a>   
                                    </div>
                                </li>
                                <li class="span3">
                                    <div class="thumbnail">
                                        <a href="#" ><img src="__PUBLIC__/default/images/cnert.jpg" alt=""></a>
                                    </div>
                                    <div class="caption">
                                        <h3>国家互联网应急中心</h3>
                                        <p>CNCERT是国内第一批具有一级风险评估资质的单位之一。自2008年对外提供风险评估服务以来，共承担了229个风险评估项目，项目遍及全国31个省市自治区，积累了丰富的经验。同时，CNCERT拥有一支国内顶尖的渗透测试团队，可针对各种架构的web系统、通信网络、信息系统提供渗透测试服务。
                                        </p>
                                        <span class="pull-left"></span>
                                        <a class="pull-right " href="#"><i class="glyphicon glyphicon-heart glyphicon2"></i></a>
                                    </div>
                                </li>
                              </ul>
                        </div><!-- /Slide2 -->
                        
                    </div>
                        <a data-slide="prev" href="#myCarouse2" class="left carousel-control left-bj hidden-xs" id="sever-prev">
                            <span id="prev-nav" class="glyphicon glyphicon-chevron-left glyphicon-triangle-left" aria-hidden="true"></span>
                        </a>
                        <a data-slide="next" href="#myCarouse2" class="right carousel-control left-bj hidden-xs" id="sever-next">
                            <span id="next-nav" class="glyphicon glyphicon-chevron-right glyphicon-triangle-right" aria-hidden="true"></span>
                        </a>
                   <!-- /.control-box -->
                </div><!-- /#myCarouse2 -->

            </div><!-- /.span12 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
    </div>
    <!--合作单位--> 

    
    <!--底部版权开始-->
    <!--底部版权开始-->
  <div class='bottom'>
    <div class='firstbottom y-row'>
        <p class='big'>
            <a href="<?php echo U('About/index');?>" target="_blank">公司介绍</a>
              <a href="<?php echo U('About/statement');?>" target="_blank">法律声明</a>
                <a href="<?php echo U('About/contact');?>" target="_blank">联系我们</a>
                  <a href="<?php echo U('About/friendlink');?>" target="_blank">友情链接</a>
  
        </p>
      
        <p>
           <a>Copyright © 2015-2018 智御云 版权所有</a> 浙ICP备14023229号
        </p>
        <p>
            <a href='' class='img1'></a>
            <a href='' class='img2'></a> 
        </p>

    </div>
  </div>

    <!--底部版权结束-->

    <!--有问题点我-->
    <div id='float-tool'>
       <dl>
          <dt><span class='one issue'>&nbsp;</span><span class='two issue'>&nbsp;</span></dt>
        
      <dt>
          <!-- webim button -->
          <!--
          <a href="/kf/client.php?locale=zh" target="_blank" onclick="if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('/kf/client.php?locale=zh&amp;url='+escape(document.location.href)+'&amp;referrer='+escape(document.referrer), 'webim', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src="/button.php?image=webim&amp;lang=zh" border="0" width="142" height="78" alt=""/></a>
          -->
          <!-- / webim button -->
        </dt>
        
        <dd style ='display:none;' class='autotop'></dd>
       </dl>
       <div>&nbsp;</div>

    
    </div>

    <!--有问题点我结束-->

    <div id='cloud-helper-box'>
      <div class='clude-relative'>
      
        <div style='display:none;'  class='cloud-top cloud-top2 '>
             <div class='clude-header'>
                 <h4>小云解答</h4>
                 <div class='right'>
                 <span class='clude-redirect2 clude-redirect'>&nbsp;</span>
                 <span class='close'>&nbsp;</span>
                 </div>
            </div>
            <div class='content xiaoyuncontent'>
              
               
            </div>
             <h4>问问智慧的小云<span class='unlink'>清空记录</span></h4>
            <div class='cloud-issue'><input type='text' name='issue' /><button class='tiwen str'>提问</button></div>
          
        </div>
        <div  class='cloud-top cloud-top1'>
            <div class='clude-header'>
                 <h4>您可能遇到的问题</h4>
                 <div class='right'>
                 <span class='clude-redirect1 clude-redirect'>&nbsp;</span>
                 <span class='close'>&nbsp;</span>
                 </div>
            </div>
            <!-- 从数据库中遍历出问题 -->
            <ul class='clude-list'>
              <?php
 $stmt=M('Problem')->field('title,content')->limit($attr['limit'])->select(); foreach($stmt as $v): ?>
<li><a><?php echo ($v["title"]); ?></a>
                <div>
                  <?php echo ($v["content"]); ?>
                </div>

              </li><?php endforeach; ?>
              
            </ul>
            <h4>问问智慧的小云</h4>
            <div class='cloud-issue'><input type='text' name='issue' /><button class='tiwen'>提问</button></div>
        </div>

        <div class='cloud-bottom'>
            <dl>
              <dt>备案:</dt>
              <dd>4006008500转3</dd>
              
            </dl>

            <dl>
              <dt> 帮助:</dt>
              <dd>智御云帮助中心</dd>
              
            </dl>

              <dl>
              <dt>售前:</dt>
              <dd>预约服务</dd>
              
            </dl>

              <dl>
              <dt>售后:</dt>
              <dd>工单0571-85025885</dd>
              
            </dl>

              </dl>

              <dl>
              <dt>论坛:</dt>
              <dd>去提问</dd>
              
            </dl>
           

        </div>


      </div>


    </div>
    <script type="text/javascript">
     var xiaoyun='<?php echo U('Index/xiaoyun');?>';


    </script>

    <!--底部版权结束-->

  </div>
    
    
</body>
</html>