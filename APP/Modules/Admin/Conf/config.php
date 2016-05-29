<?php

return array(
    //分页的数目
    'PAGE_NUM' => 10,
    'APP_AUTOLOAD_PATH' => '@.TagLib', //自定义标签的文件夹位置@代表当前应用
    'TAGLIB_BUILD_IN' => 'Cx,Hd', //Cx代表核心标签库是必须加的，Hd代表你自定义标签的文件名
    //
     'ADMIN_DETAIL' => 1, //后台记录开启
    'RBAC_SUPERADMIN' => 'admin', //设置超级管理员名称
    'ADMIN_AUTH_KEY' => 'superadmin', //超级管理员识别
    'USER_AUTH_MODEL' => 'admin', //用户表名称
    'USER_AUTH_ON' => true, //是否开启验证
    'USER_AUTH_TYPE' => 2, //验证类型(1、登陆时验证。2、实时验证)
    'USER_AUTH_KEY' => 'uid', //用户认证SESSION中的识别号
    'NOT_AUTH_MODULE' => 'index,login,common,UserSet', //不要验证的控制器
    'NOT_AUTH_ACTION' => 'insert,code,level,update,save', //不要验证的方法
    'RBAC_ROLE_TABLE' => 'kz_role', //角色(用户组)表名称
    'RBAC_USER_TABLE' => 'kz_role_user', //角色和用户之间的中间表名称
    'RBAC_ACCESS_TABLE' => 'kz_access', //权限表名称(角色对应的节点)
    'RBAC_NODE_TABLE' => 'kz_node', //节点表名称
    
    'USER_AUTH_GATEWAY' => '/Login/index', //默认的认证网关
    //
    //自定义模版替换
    'TMPL_PARSE_STRING' => array(
        '__PUBLIC__' => __ROOT__ . '/APP/Modules/Admin/Public',
    ),
    'SHOW_PAGE_TRACE' => true, //页面Trace功能进行了增强，更加方便开发过程中的调试，并接管了一部分日志功能。
);
?>
