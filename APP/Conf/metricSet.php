<?php

return array(
    'yunecs' => 1, //云服务器ECS的ID
    'ddos_yun' => 19, //高防ID
    'rent_yun' => 20, //主机租用ID
    'storage_yun' => 22, //机柜租用ID
    'trusteeship_yun' => 23, //主机托管ID
    'ddos' => 'ddos_server_package', //高防表
    'rent' => 'host_rent_package', //主机租用表	
    'storage' => 'storage_server_package', //机柜租用表
    'trusteeship' => 'host_trusteeship_package', //主机托管表
    'goods19' => 'ddos_server',
    'goods20' => 'host_rent',
    'goods22' => 'storage_server',
    'goods23' => 'host_rent',
    
    //  产品表识别
    'pack_goods19' => 'ddos_server_package', //高防表
    'pack_goods20' => 'host_rent_package', //主机租用表
    'pack_goods22' => 'storage_server_package', //机柜租用表
    'pack_goods23' => 'host_trusteeship_package', //主机托管表
   
    // 产品视图识别
    'view_goods19' => 'ddos_server_package', //高防表
    'view_goods20' => 'RentView', //主机租用表
    'view_goods22' => 'storage_server_package', //机柜租用表
    'view_goods23' => 'host_trusteeship_package', //主机托管表
 
    // 产品左边导航的链接
    'href_goods1' => U('Console/Server/index', array('gid' => 1)),
    'href_goods19' => U('Console/Ddos/index', array('gid' => 19)), //高防云
    'href_goods20' => U('Console/Host/index', array('gid' => 20)), //主机租用
    'href_goods22' => U('Console/storage/index', array('gid' => 22)), //机柜租用
    'href_goods23' => U('Console/Host/trusteeship', array('gid' => 23)), //主机托管

    //服务状态列表 
    'StatusList' => array(
         '等待中…' => 0,
        '正在施工' => 1,
        '正常服务' => 2,
        '即将过期' => 3,
        '已过期' => 4,
    ),
    //线路类型列表 
    'LineList' => array(
        'ADSL' => 1,
        '光纤' => 2,
    ),
    //防御等级列表 
    'DefenseGradeList' => array(
        '100M' => 1,
        '300M' => 2,
        '500M' => 3,
        '1500M' => 4,
    ),
    //租用类型 
    'Zytype' => array(
        '整柜' => 1,
        '主机' => 2,
    ),
    'TgGrade' => array(
        '一级托管' => 1,
        '二级托管' => 2,
        '三级托管' => 3,
    ),
    'ZfSatatus' => array(
        '未支付' => 0,
        '支付成功' => 1,
        '支付中' => 2,
        '支付失败' => 3,
    ),
    'Online' => array(
        '上架' => 0,
        '下架' => 1,
    ),
    'OrderStatus' => array(
        '等待确认' => 0,
        '客户经理确认' => 80, //客户经理确认临时工单 
        '客服确认' => 110, //
        '订单核查完毕' => 120, //工单核发，下派临时工单
        '正在回访' => 130, //客户经理跟踪回访
        '正在试用' => 140, //回访完成
        '试用跟进' => 150, //试用跟进
        '客户付款' => 160, //已经付款，请求到帐确认
        '试用结束' => 180, //临时完毕工单
        '财务到帐' => 190, //
        '正在施工' => 200, //
        '施工完毕' => 220, //
        '正式订单' => 300, //
        '客服终止' => 410, //无客户经理确认，归档
        '临时终止（）' => 480, //归档
        '正式终止（）' => 490, //归档
    ),
    'AutoWo' => array(
        'WoTitle' => '新的工单', //工单标题
        'TecGroupID' => 28, //工单类型：环境配置
    ),
    'WoType' => array(
        'userwo' => 0, //工单标题
        'syswo' => 10, //系统自动生成
    ),
    'WoStatus' => array(//工单状态
        '待接手' => 0, //工单标题
        '处理中' => 1, //系统自动生成
        '待评价' => 2, //系统自动生成
        '已关闭' => 3, //系统自动生成
    ),
     'OrderType' => array(//定单类型
        '临时订单' => 0, //
        '正式订单' => 1, //
        '续费订单' => 2, //
        '升级订单' => 3, //
    ),

 'LogOpModel' => array(//操作日志类型
        '未知操作' => 0, //
     '创建新记录' => 1, //
        '修改状态' => 2, //
        '修改信息' => 3, //
        '删除' => 4, //
    ), 
 'MonthSelect' => array(//操作日志类型
        '1个月' => 1, //
        '2个月' => 2, //
        '3个月' => 3, //
        '4个月' => 4, //
        '5个月' => 5, //
        '6个月' => 6, //
        '7个月' => 7, //
        '8个月' => 8, //
        '9个月' => 9, //
        '10个月' => 10, //
        '11个月' => 11, //
        '1年' => 12, //
    ),
    
     'AboutName' => array(//操作日志类型
     'index'=>'公司介绍',
         'statement'=>'法律声明',
         'agreement'=>'服务协议',
         'contact'=>'联系我们',
         'friendlink'=>'友情链接',
         'product'=>'产品介绍',
         'anli'=>'成功案例',
         'jifang'=>'机房介绍',        
    ),
    
    'PinfoName' => array(//操作日志类型
     'dj'=>'单机租用托管',
         'zg'=>'整柜租用托管',
         'pf'=>'普防',
         'gf'=>'高防',           
    ),
    
    'UserDindanType' => array(
        '等待确认' => 0,
        '客服确认，等待付款' => 1,
        '已付款' => 2,
        '正在施工' => 3,
        '已完成' => 4,
    ),
);
