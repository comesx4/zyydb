<?php
Class CommonAction extends Action{
	
	/*
		@der 前置方法
	*/
    public function _initialize() {
        
        if (ismobile()) {
            //设置默认默认主题为 Mobile
            C('DEFAULT_THEME','Mobile');
        }
        
        $this->actionname = ACTION_NAME;
    }
}
