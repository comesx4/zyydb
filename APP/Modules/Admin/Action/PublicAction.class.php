<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PublicAction
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 */
class PublicAction extends Action{

    public function msg(){
        $this->mssg=I('get.msg','网站出错了！');
        $this->display();
    }
}
