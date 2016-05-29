<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of testAction
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 */
class TestAction extends Action{    
   public function index(){
       
        $this->display('Public:test');
    }
}
