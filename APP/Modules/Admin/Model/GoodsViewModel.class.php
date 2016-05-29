<?php
Class GoodsViewModel extends ViewModel {

    public $viewFields = array(
        'goods' => array(
            'id', 'goods','sort',
            '_type' => 'LEFT'
        ),
        'goodscat' => array(
            'cat',
            '_on' => 'goods.cid=goodscat.id'
        ),
    );
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'sort asc',
        ),
    );

}
