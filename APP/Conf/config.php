<?php
return array(
	//'配置项'=>'配置值'
'TMPL_L_DELIM'=>'<{', //修改左定界符
'TMPL_R_DELIM'=>'}>', //修改右定界符
'TAG_NESTED_LEVEL' =>5,
'DB_PREFIX'=>'kz_',  //设置表前缀
'DB_DSN'=>'mysql://root:LnAv1p2J14UoBVmn@localhost:3306/zyydb',//使用DSN方式配置数据库信息

//'SHOW_PAGE_TRACE'=>true,//开启页面Trace
'TMPL_TEMPLATE_SUFFIX'=>'.html',//更改模板文件后缀名
//'TMPL_FILE_DEPR'=>'_',//修改模板文件目录层次
//'TMPL_DETECT_THEME'=>true,//自动侦测模板主题
//'THEME_LIST'=>'your,my',//支持的模板主题列表
'TMPL_PARSE_STRING'=>array(           //添加自己的模板变量规则
	'__CSS__'=>__ROOT__.'/Public/Css',
	'__JS__'=>__ROOT__.'/Public/Js',
	'__PUBLIC__'=>__ROOT__.'/Public',
    '__APP__'=>__ROOT__
),
'ADMIN_DETAIL'   => 1,//后台记录开启
'RBAC_SUPERADMIN'=>'admin',//设置超级管理员名称
'ADMIN_AUTH_KEY' =>'superadmin',//超级管理员识别
'USER_AUTH_MODEL'=>'admin',//用户表名称
'USER_AUTH_ON'	 =>true,//是否开启验证
'USER_AUTH_TYPE' =>1,//验证类型(1、登陆时验证。2、实时验证)
'USER_AUTH_KEY'	 =>'uid',//用户认证SESSION中的识别号
'NOT_AUTH_MODULE'=>'index,login,common,UserSet',//不要验证的控制器
'NOT_AUTH_ACTION'=>'insert,code,level,update,save',//不要验证的方法
'RBAC_ROLE_TABLE'=>'kz_role',//角色(用户组)表名称
'RBAC_USER_TABLE'=>'kz_role_user',//角色和用户之间的中间表名称
'RBAC_ACCESS_TABLE'=>'kz_access',//权限表名称(角色对应的节点)
'RBAC_NODE_TABLE'=>'kz_node',//节点表名称
//'LAYOUT_ON'=>true,//开启模板渲染
'URL_CASE_INSENSITIVE'=>true,//url不区分大小写
'URL_HTML_SUFFIX'=>'html',//限制伪静态的后缀
'APP_GROUP_LIST' => 'Home,Admin,Console,Cornjob,Ctmsev', //项目分组设定
'DEFAULT_GROUP'  => 'Home', //默认分组
'TMPL_CACHE_ON'=> false,//关闭模板缓存
//'SESSION_TYPE'=>'memcache',//设置用memcache存储session
//'SESSION_AUTO_START'=>true,//默认不开启session
'SESSION_time'=>1500,//自定义的SESSION的生存时间
'APP_GROUP_MODE'=>1,//使用新分组，如果是0就是老分组
'APP_GROUP_PATH'=>'Modules',//设置独立分组的文件夹名称

'SHOW_PAGE_TRACE'=>false,
'URL_MODEL'=>2,//U方法换成rewrite模式，不用index.php了
//'URL_PATHINFO_DEPR'=>'-',//修改URL的分隔符
    
'LOAD_EXT_CONFIG'=>'thumb,water,image,logo,zhifubao,city,replayTime,submitTime,metricSet,safeset,productinfo',//扩展配置文件
"LOAD_EXT_FILE"=>"parameter",//加载公用函数

//默认错误跳转对应的模板文件
//'TMPL_ACTION_ERROR' => 'Public:error',
//'TMPL_ACTION_SUCCESS' => 'Public:success',
//'TMPL_EXCEPTION_FILE' =>'./APP/Tpl/Public/maintain.html', // 定义公共错误模板 

    
);

?>
