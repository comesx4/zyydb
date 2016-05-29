<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OperatorAction
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 */
class OperatorAction extends AdminAction {
    //关于我们
    public function index() {
        L('demo','测试');
        $this->display();
    }
    
    public function users() {

//        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {// 自动侦测浏览器语言
//            trace($_SERVER['HTTP_ACCEPT_LANGUAGE']);
//            preg_match('/^([a-z\d\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
//            trace($matches);
//            $langSet = $matches[1];
//        }

        $settings = M('chatconfig')->select();
        $this->showpopup = $settings['enablepopupnotification'] == '1' ? "1" : "0";
        $this->title=L('clients_title');
        $this->display();
    }
    
    public function getajax($t = 2) {        
      $list=  D('UserView')->select();
        $data['status'] = 1;
        $data['info'] = $list;     
        log::write($list);
        $this->ajaxReturn($data, 'JSON');
        
    }
}
