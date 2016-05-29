<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PinfoAction
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 */
class PinfoAction  extends Action {

    //单机租用托管
    public function dj() {
        $this->actionname = ACTION_NAME;
        $db = M('single_machine');
        $city = M('city')->field('id,city,city_code')->order('sort ASC')->select();
        $data = $db->field(true)->order('sort DESC')->select();
        $this->data = $data;
        $this->city = $city;
        $this->display();
    }

    //整柜租用托管
    public function zg() {
        $this->actionname = ACTION_NAME;
        $db = M('cabinet_machine');
        $city = M('city')->field('id,city,city_code')->order('sort ASC')->select();
        $data = $db->field(true)->order('sort DESC')->select();
        $this->data = $data;
        $this->city = $city;
        $this->display();
    }

    //普防
    public function pf() {
        $this->actionname = ACTION_NAME;
        $db = M('normal_protect');
        $data = $db->field(true)->order('sort DESC')->select();
        $this->data = $data;
        $this->display();
    }

    //高防
    public function gf() {
        $this->actionname = ACTION_NAME;
        $db = M('high_protect');
        $data = $db->field(true)->order('sort DESC')->select();
        $this->data = $data;
        $this->display();
    }

    //服务协议
    public function agreement() {
        $this->actionname = ACTION_NAME;
        $this->display();
    }   
   
}
