<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Goodsorder_20ViewModel
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 */
class Goodsorder_20ViewModel  extends GoodsorderViewModel{
   
     public $viewFields = array(
        'goodsorder' => array(
            'id', 'number', 'quantity', 'info', 'status', 'price', 'createtime',
            'paytime', 'time', 'remark','pid','gid','orderstatus',
            '_type' => 'LEFT'
        ),
        'host_rent_package' => array(
            'name'=>'productname',
            '_on' => 'goodsorder.pid=host_rent_package.id',
            '_type' => 'LEFT'
        ),
          'admin' => array(
            'username'=>'adminname',
            '_on' => 'goodsorder.aid=admin.id',
            '_type' => 'LEFT'
        ),
        'user' => array(
            'username',
            '_on' => 'goodsorder.uid=user.id'
        ),
    );
}
