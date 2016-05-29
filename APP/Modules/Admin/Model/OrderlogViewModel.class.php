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
class OrderlogViewModel extends ViewModel {

    Protected $viewFields = array(
        'orderlog' => array(
          'ID', 'userOp', 'opdetail', 'optime', 'opIP',
            '_type' => 'LEFT'
        ),
        'admin' => array(
            'username' => 'adminname',
            '_on' => 'orderlog.adminID = admin.id',
            '_type' => 'LEFT'
        ),
        'user' => array(
            'username',
            '_on' => 'orderlog.userID = user.id'
        )
    );
    
     protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'id desc',
        ),
    );

}
