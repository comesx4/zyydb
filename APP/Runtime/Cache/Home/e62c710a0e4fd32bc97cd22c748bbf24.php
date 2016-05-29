<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
    <link rel="stylesheet" href="__PUBLIC__/Css/about.css"/>
    <link rel="stylesheet" href="__PUBLIC__/Css/global.css"/>
    <link rel="stylesheet" href="__PUBLIC__/Css/head.css"/>
    <link rel="stylesheet" href="__PUBLIC__/Css/domain.css"/>
    <link rel="stylesheet" href="__PUBLIC__/Css/host.css"/>
    <link rel="stylesheet" href="__PUBLIC__/Css/cloudsite.css"/>
    <link rel="stylesheet" href="__PUBLIC__/Css/server.css"/>
    <link rel="stylesheet" href="__PUBLIC__/Css/cloudesc.css"/>
    <style type="text/css">
        .htype a {
            width: 162px;
        }

        .site_list ul {
            height: 652px;
            margin-top: -1px;
        }

        .site_list ul li {
            width: 285px;
            height: 623px
        }
    </style>

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

    <div id='slides'>
        <div style="background-color:#fff6dd;padding:0;text-align: center;" class="tab-pannel">
            <img src="__ROOT__/Uploads/Logo/<?php echo (C("img2")); ?>">
        </div>
    </div>


    <div class='body-content'>

        <div class="y-row content-wrapper">
            <div class='content-left'>
              <div class='conts'>
                <span style='font-size: 13px;'>主机租用/托管</span>
              
                <dl class='content-list'>
                    <dt>服务器租用</dt>                
                        <dd class='on'><a <?php if($actionname == 'dj'): ?>class='down'<?php endif; ?> href="<?php echo U('pinfo/dj');?>">单机租用托管</a></dd>
                        <dd class='on'><a <?php if($actionname == 'zg'): ?>class='down'<?php endif; ?> href="<?php echo U('pinfo/zg');?>">整柜租用托管</a></dd>  
                         <dd class='on'><a <?php if($actionname == 'pf'): ?>class='down'<?php endif; ?> href="<?php echo U('pinfo/pf');?>">普防</a></dd>  
                        <dd class='on'><a <?php if($actionname == 'gf'): ?>class='down'<?php endif; ?> href="<?php echo U('pinfo/gf');?>">高防</a></dd>
                </dl>
                 <dl class='content-list'>
                    <dt>联系我们</dt> 
                        <dd class='on'><a <?php if($actionname == 'statement'): ?>class='down'<?php endif; ?> href="<?php echo U('about/statement');?>">法律声明</a></dd>  
                         <dd class='on'><a <?php if($actionname == 'agreement'): ?>class='down'<?php endif; ?> href="<?php echo U('about/agreement');?>">服务协议</a></dd>  
                        <dd class='on'><a <?php if($actionname == 'contact'): ?>class='down'<?php endif; ?> href="<?php echo U('about/contact');?>">联系我们</a></dd> 
                            <dd class='on'><a <?php if($actionname == 'friendlink'): ?>class='down'<?php endif; ?> href="<?php echo U('about/friendlink');?>">友情链接</a></dd> 
                            
                </dl>
    
              </div>
            </div>
 
            <div class='content-right'>
                <div class='nav-wei'>
                    <ul>
                        <li><a target='_self' href="<?php echo U('/index/index');?>">首页</a></li>
                        <li><a target='_self' href="<?php echo U('/pinfo/pf');?>">高防服务器</a></li>
                    </ul>
                </div>
                <div class='right-main'>
                    <div class="fixed">
                        <div class="inner">
                            <td valign="top">
                                <div class="body_right">
                                    <div class="domainfw">
                                        <a id="buy" class="product-anchor"> </a>
                                        <h2>网络高防服务器</h2>
                                        <p class="js">服务器提供99.95%服务可用性，99.999%数据可靠性的稳定服务</p>
                                        <div class="cloud">
                                            <table border="0">
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        <h3>全冗余网络结构，杜绝单点故障</h3>
                                                        <p>
                                                            完整的网络安全产品，包括ISS安全漏洞扫描、DDOS攻击入侵检测防护系统，安氏病毒防范系统。同时实现网络集中监控、故障和告警管理、流量性能监控管理等网络管理功能。通过双物理路由连接至本地其他核心节点，能够提供622M/155M/100M/2M等各种速率传输电路
                                                        </p>

                                                    </td>
                                                    <td style="text-align:right">
                                                        <img src="__PUBLIC__/Images/pinfo/gf.png">
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tjgm">
                                        <div class="tit"><span>推荐<font color="#FF6D4E">购买</font><em>Recommmended to
                                            buy</em></span></div>
                                        <div class="cont">
                                            <table border="1">
                                                <tbody>
                                                <tr>
                                                    <th></th>
                                                    <th>主机类型</th>
                                                    <th>防护能力</th>
                                                    <th>价格</th>
                                                </tr>
                                                <?php if(is_array($data)): foreach($data as $key=>$d): ?><tr>
                                                        <th><?php echo ($d["name"]); ?></th>
                                                        <td><?php echo ($d["type"]); ?></td>
                                                        <td><?php echo ($d["ability"]); ?></td>
                                                        <td>￥<font color="#FF6544"><?php echo ($d["price"]); ?></font>元/月起</td>
                                                    </tr><?php endforeach; endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
</body>
</html>