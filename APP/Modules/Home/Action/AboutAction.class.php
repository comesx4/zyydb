<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AboutAction
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 */
class AboutAction extends Action {

    //关于我们
    public function index() {
        $this->actionname = ACTION_NAME;
        $this->display();
    }

    //法律声明
    public function statement() {
        $this->actionname = ACTION_NAME;
        $this->display();
    }

    //联系我们
    public function contact() {
        $this->actionname = ACTION_NAME;
        $this->display();
    }

    //友情链接
    public function friendlink() {
        $this->actionname = ACTION_NAME;
        $this->display();
    }

    //服务协议
    public function agreement() {
        $this->actionname = ACTION_NAME;
        $this->display();
    }
    
      //产品介绍
    public function product() {
        $this->actionname = ACTION_NAME;
        $this->display();
    }
    
      //成功案例
    public function anli() {
        $this->actionname = ACTION_NAME;
        $this->display();
    }
    
      //机房介绍
    public function jifang() {
        $this->actionname = ACTION_NAME;
        $this->display();
    }

}
