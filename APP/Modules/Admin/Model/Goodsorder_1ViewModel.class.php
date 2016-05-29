<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Goodsorder1ViewModel
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 */
class Goodsorder_1ViewModel extends GoodsorderViewModel  {
    
    public $viewFields = array(
        'goodsorder' => array(
            'id', 'number', 'quantity', 'info', 'status', 'price', 'createtime',
            'paytime', 'time', 'remark','pid','orderstatus',
            '_type' => 'LEFT'
        ),
        'goods' => array(
            'goods',
            '_on' => 'goodsorder.gid=goods.id',
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
