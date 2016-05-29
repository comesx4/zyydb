<?php

Class GoodsorderViewModel extends ViewModel {

    public $viewFields = array(
        'goodsorder' => array(
            'id', 'number', 'gid', 'quantity', 'info', 'status', 'uid', 'price', 'createtime',
            'paytime', 'time', 'type', 'remark', 'orderType', 'pid', 'roomid', 'cabinetID', 'IP', 'startday',
            'endday', 'svstatus', 'orderstatus', 'aid',
            '_type' => 'LEFT'
        ),
        'goods' => array(
            'goods',
            '_on' => 'goodsorder.gid=goods.id',
            '_type' => 'LEFT'
        ),
        'admin' => array(
            'username' => 'adminname',
            '_on' => 'goodsorder.aid=admin.id',
            '_type' => 'LEFT'
        ),
        'city' => array(
            'city',
            '_type' => 'LEFT',
            '_on' => 'goodsorder.roomid=city.id'
        ),
        'user' => array(
            'username',
            '_on' => 'goodsorder.uid=user.id'
        ),
    );
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'id DESC',
        ),
    );

}
