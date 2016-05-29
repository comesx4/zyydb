<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of productinfo
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 * 产品信息结构定义
 */
return array(
    'ProductInfo1' => array(
        'name' => array('des' => '产品名称',
            'model' => 'input',
            'value' => ''
        ),
        'band' => array('des' => '带宽',
            'model' => 'input',
            'value' => ''
        ),
        'price' => array('des' => '价格',
            'viewtostr' => false, //转成字符串时隐藏
            'model' => 'input',
            'value' => ''
        ),
        'DefenseGrade' => array('des' => '防御等级',
            'model' => 'list',
            'modelreg' => 'DefenseGradeList',
            'value' => ''
        ),
        'LineType' => array('des' => '线路类型',
            'model' => 'list',
            'modelreg' => 'LineList',
            'value' => ''
        ),
        'number' => array('des' => '专线租用数量',
            'model' => 'input',
            'value' => ''
        ),
        'online' => array('des' => '上架',
            'model' => 'radio',
            'modelreg' => 'Online',
            'viewtostr' => false, //转成字符串时隐藏
            'value' => ''
        ),
        'sort' => array('des' => '排序',
            'model' => 'input',
            'value' => '',
            'viewtostr' => false, //转成字符串时隐藏
        ),
    ),
    //高防
    'ProductInfo19' => array(
        'name' => array('des' => '产品名称',
            'model' => 'input',
            'value' => ''
        ),
        'band' => array('des' => '带宽',
            'model' => 'input',
            'value' => ''
        ),
        'price' => array('des' => '价格',
            'model' => 'input',
            'viewtostr' => false, //转成字符串时隐藏
            'value' => ''
        ),
        'DefenseGrade' => array('des' => '防御等级',
            'model' => 'list',
            'modelreg' => 'DefenseGradeList',
            'value' => ''
        ),
        'LineType' => array('des' => '线路类型',
            'model' => 'list',
            'modelreg' => 'LineList',
            'value' => ''
        ),
        'number' => array('des' => '专线租用数量',
            'model' => 'input',
            'value' => ''
        ),
        'online' => array('des' => '上架',
            'model' => 'radio',
            'modelreg' => 'Online',
            'viewtostr' => false, //转成字符串时隐藏
            'value' => ''
        ),
        'sort' => array('des' => '排序',
            'model' => 'input',
            'value' => '',
            'viewtostr' => false, //转成字符串时隐藏
        ),
    ),
    //服务器租用
    'ProductInfo20' => array(
        'name' => array('des' => '产品名称',
            'model' => 'input',
            'value' => ''
        ),
        'zytype' => array('des' => '租用类型',
            'model' => 'list',
            'modelreg' => 'Zytype',
            'value' => ''
        ),
        'modeid' => array('des' => '设备型号',
            'model' => 'input',
            'value' => ''
        ),
        'modeid' => array('des' => '设备型号',
            'model' => 'input',
            'value' => ''
        ),
        'os' => array('des' => '操作系统',
            'model' => 'dblist', //从数据库中读取
            'table' => 'os', //表名
            'valuefield' => 'name', //名称字段
            'value' => ''
        ),
        'Unumber' => array('des' => 'U数',
            'model' => 'input',
            'value' => ''
        ),
        'LineType' => array('des' => '线路类型',
            'model' => 'list',
            'modelreg' => 'LineList',
            'value' => ''
        ),
        'band' => array('des' => '带宽',
            'model' => 'input',
            'value' => ''
        ),
        'cpu' => array('des' => 'CPU',
            'model' => 'input',
            'value' => ''
        ),
        'memory' => array('des' => '内存',
            'model' => 'input',
            'value' => ''
        ),
        'disk' => array('des' => '硬盘',
            'model' => 'input',
            'value' => ''
        ),
        'price' => array('des' => '价格',
            'viewtostr' => false, //转成字符串时隐藏
            'model' => 'input',
            'value' => ''
        ),
        'sort' => array('des' => '排序',
            'model' => 'input',
            'value' => '',
            'viewtostr' => false, //转成字符串时隐藏
        ),
        'online' => array('des' => '上架',
            'model' => 'radio',
            'modelreg' => 'Online',
            'viewtostr' => false, //转成字符串时隐藏
            'value' => ''
        ),
    ),
    //机柜租用
    'ProductInfo22' => array(
        'name' => array('des' => '机柜名称',
            'model' => 'input',
            'value' => ''
        ),
        'band' => array('des' => '带宽',
            'model' => 'input',
            'value' => ''
        ),
        'LineType' => array('des' => '线路类型',
            'model' => 'list',
            'modelreg' => 'LineList',
            'value' => ''
        ),
        'price' => array('des' => '价格',
            'model' => 'input',
            'viewtostr' => false, //转成字符串时隐藏
            'value' => ''
        ),
        'sort' => array('des' => '排序',
            'model' => 'input',
            'value' => '',
            'viewtostr' => false, //转成字符串时隐藏
        ),
        'online' => array('des' => '上架',
            'model' => 'radio',
            'modelreg' => 'Online',
            'viewtostr' => false, //转成字符串时隐藏
            'value' => ''
        ),
    ),
    //服务器托管
    'ProductInfo23' => array(
        'name' => array('des' => '托管服务器名称',
            'model' => 'input',
            'value' => ''
        ),
        'tggrade' => array('des' => '托管类型',
            'model' => 'list',
            'modelreg' => 'TgGrade',
            'value' => ''
        ),
        'Unumber' => array('des' => 'U数',
            'model' => 'input',
            'value' => ''
        ),
        'LineType' => array('des' => '线路类型',
            'model' => 'list',
            'modelreg' => 'LineList',
            'value' => ''
        ),
        'band' => array('des' => '带宽',
            'model' => 'input',
            'value' => ''
        ),
        'cabinet_size' => array('des' => '机柜大小',
            'model' => 'input',
            'value' => ''
        ),
        'price' => array('des' => '价格',
            'model' => 'input',
            'viewtostr' => false, //转成字符串时隐藏
            'value' => ''
        ),
        'sort' => array('des' => '排序',
            'model' => 'input',
            'value' => '',
            'viewtostr' => false, //转成字符串时隐藏
        ),
        'online' => array('des' => '上架',
            'model' => 'radio',
            'modelreg' => 'Online',
            'viewtostr' => false, //转成字符串时隐藏
            'value' => ''
        ),
    ),
    
    //用户订单
    'UserDinDan' => array(
        'danhao' => array('des' => '订单号',
            'model' => 'input',
            'value' => ''
        ),
        'userID' => array('des' => '用户ID',
           'model' => 'hidden',
            'value' => ''
        ),
          'salt' => array('des' => '校验',
            'model' => 'hidden',
            'value' => '',
            'viewtostr' => false, //转成字符串时隐藏
        ),
    ),
);

