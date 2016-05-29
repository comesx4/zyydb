<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrderlogViewModel
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 */
class DindanViewModel extends ViewModel {

    Protected $viewFields = array(
        'userdindan' => array(
            'id', 'danhao', 'userID', 'salt','regtime','moditime','status',
            '_type' => 'LEFT'
        ),
        'user' => array(
            'username',
            '_on' => 'userdindan.userID = user.id'
        )
    );
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'id desc',
        ),
    );

}
