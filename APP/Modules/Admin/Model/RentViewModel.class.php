<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of rentViewModel
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 *  主机租用托管套餐设置
 */

Class RentViewModel  extends ViewModel {
    
    	public $viewFields = array(
		'host_rent_package' => array(
			 'id', 'name', 'band', 'memory','cpu','price','sort','disk','os','LineType',
                    'modeid','Unumber','zytype','online',
			'_type' => 'LEFT'
			),
		'os' => array(
			'name'=>'system',
			'_on' => 'host_rent_package.os = os.id'
			),
	);
}
