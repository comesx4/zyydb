<?php

return array(
//'配置项'=>'配置值'
    'DB_PREFIX' => 'kf_', //设置表前缀
    'DB_DSN' => 'mysql://root:@localhost:3306/zykf', //使用DSN方式配置数据库信息
    'TMPL_TEMPLATE_SUFFIX' => '.html', //更改模板文件后缀名
    'TMPL_PARSE_STRING' => array(//添加自己的模板变量规则
        '__BOOTSTRAP_JS__' => __ROOT__ . '/APP/Modules/Ctmsev/Public/bootstrap/js',
        '__JS__' => __ROOT__ . '/APP/Modules/Ctmsev/Public/Js',
        '__PUBLIC__' => __ROOT__ . '/APP/Modules/Ctmsev/Public',
    ),
    //分页的数目
    'PAGE_NUM' => 10,
    'USER_AUTH_GATEWAY' => '/Operator/index', //默认的认证网关  
    'SHOW_PAGE_TRACE' => true, //页面Trace功能进行了增强，更加方便开发过程中的调试，并接管了一部分日志功能。
    
     'DEFAULT_THEME'  => 'default',
    'TMPL_DETECT_THEME' => true, // 自动侦测模板主题
    
    'LANG_SWITCH_ON' => true,
    'LANG_AUTO_DETECT' => false, // 自动侦测语言 开启多语言功能后有效
    'DEFAULT_LANG' => 'zh-cn', // 默认语言
    'LANG_LIST' => 'zh-cn,en-us', // 允许切换的语言列表 用逗号分隔
    'VAR_LANGUAGE' => 'l', // 默认语言切换变量
);
?>
