<?php
return array(
   'TMPL_TEMPLATE_SUFFIX'=>'.html',//更改模板文件后缀名

'TMPL_PARSE_STRING'=>array(           //添加自己的模板变量规则
	// '__PUBLIC__'=>'/aliyun2/Public',
	// '__ROOT__'  => '/aliyun2'
),
'APP_AUTOLOAD_PATH'=>'@.TagLib',//自定义标签的文件夹位置@代表当前应用
'TAGLIB_BUILD_IN'=>'Cx,Hd',//Cx代表核心标签库是必须加的，Hd代表你自定义标签的文件名	
'HTML_CACHE_ON'=>false,//开关静态模板缓存
'HTML_CACHE_RULES'=>array(
'Show:index'=>array('{:module}_{:action}__{id}',60),//键是要缓存的控制器
),
'LOG_RECORD' => true, // 开启日志记录
'LOG_LEVEL' =>'EMERG,ALERT,CRIT,ERR', // 只记录EMERG ALERT CRIT ERR 错误
'LOG_TYPE' =>  'File', // 日志记录类型 默认为文件方式
'SHOW_PAGE_TRACE'=>true,//页面Trace功能进行了增强，更加方便开发过程中的调试，并接管了一部分日志功能。
    
//设置S函数的缓存方式，默认是文件缓存 
// 'DATA_CACHE_TYPE'=>'Memcache',
// 'MEMCACHE_HOST'=>'127.0.0.1',
// 'MEMCACHE_PORT'=>11211,

'VAR_FILTERS'=>'stripslashes,strip_tags',//输入过滤
);

