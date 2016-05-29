<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserdindanModel
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 */
class UserdindanModel extends Model{

    protected $_validate = array(
        array('danhao', 'require', '单号必须填写！'), //默认情况下用正则进行验证
        array('danhao', '', '单号已经存在！', 0, 'unique', 1), // 在新增的时候验证name字段是否唯一
    );
    
    protected $patchValidate=true;//学段进行批量验证
}
