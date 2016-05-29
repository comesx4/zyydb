<?php
return array(
   'TMPL_TEMPLATE_SUFFIX'=>'.html',//更改模板文件后缀名
   'DB_DSN2'=>'mysql://monitor:GErteww09d8@localhost:3306/monitor',//使用DSN方式配置数据库信息
	'TMPL_PARSE_STRING'=>array(           //添加自己的模板变量规则
		'__CSS__'=>__ROOT__.'/Public/Css',
		'__JS__'=>__ROOT__.'/Public/Js',
		'__PUBLIC__'=>__ROOT__.'/APP/Modules/Console/Public',
		'__ROOT__'=>__ROOT__,
	),
	'APP_AUTOLOAD_PATH'=>'@.TagLib',//自定义标签的文件夹位置@代表当前应用
	'TAGLIB_BUILD_IN'=>'Cx,Hd',//Cx代表核心标签库是必须加的，Hd代表你自定义标签的文件名	
	'HTML_CACHE_ON'=>false,//开关静态模板缓存
	'HTML_CACHE_RULES'=>array(
	'Show:index'=>array('{:module}_{:action}__{id}',60),//键是要缓存的控制器
	),
	//分页的数目
	'page_num' => 20,

	//设置S函数的缓存方式，默认是文件缓存 
	// 'DATA_CACHE_TYPE'=>'Memcache',
	// 'MEMCACHE_HOST'=>'127.0.0.1',
	// 'MEMCACHE_PORT'=>11211,
	//'LOAD_EXT_CONFIG'=>'metricSet',//扩展配置文件

);

