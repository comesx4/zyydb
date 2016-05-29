<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminAction
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 */
class AdminAction extends Action {
  	/*
		@der 前置方法
	*/
    public function _initialize() {
        $this->actionname = ACTION_NAME;      
    }
}
