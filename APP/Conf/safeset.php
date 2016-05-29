<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of safeset
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 */
return array(
   
 'SAFE' => array(
        'INFOHAND' => 'userinfo',
     'NAMEHAND' => 'username',
     'IDHAND' => 'id',
     'TYPEHAND' => 'userType',
     'AUTH_GATEWAY'=>'/Login/index',
      'AUTH_GATEWAY_TIME'=>1,//跳转等待时间
      'AUTH_GATEWAY_MSG'=>'请先登录帐户……',//跳转提示信息
    ),
    //网站设置
    'WEBSITE_SET' => array(
        'SITE_DOMAIN' => 'test.com:8088/', //域名
        'SITE_NAME' => '智御网络', //网站名称
        'SITE_TITLE' => '智御网络', //网站标题前缀
        'SITE_TITLE' => '智御网络', //网站标题前缀
        'VERIFY_ENABLED' => false, //验证码启用
    ),
    //邮件配置
    'THINK_EMAIL' => array(
        'SMTP_HOST' => 'mail.wljn.site', //'smtp.test.com', //SMTP服务器
        'SMTP_PORT' => '25', //SMTP服务器端口
        'SMTP_USER' => 'admin@wljn.site', //'admin@test.com', //SMTP服务器用户名
        'SMTP_PASS' => '12345', //'12345', //SMTP服务器密码
        'FROM_EMAIL' => 'admin@wljn.site', //'admin@test.com', //发件人EMAIL
        'FROM_NAME' => '智御网络', //发件人名称
        'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
        'REPLY_NAME' => '', //回复名称（留空则为发件人名称）
    ),
    'CRCAKTYPE'=>array(
        'PHONE'=>1,
        'EMAIL'=>2,
        'PWD'=>3,
    )
);
