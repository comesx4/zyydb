<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CuxiaoAction
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 */
class CuxiaoAction extends Action {
    
    //关于我们
    public function index() {
        $this->actionname = ACTION_NAME;
        $this->display();
    }
}
