<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TrusteeshipViewModel
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 * 主机租用托管套餐设置
 */
class TrusteeshipViewModel  extends ViewModel {
    
    	public $viewFields = array(
		'host_trusteeship_package' => array(
			 'id', 'name', 'band', 'cabinet_size', 'price', 'sort', 'type', 'LineType', 'Unumber', 'zytype', 'tggrade','online'
//			'_type' => 'LEFT'
			),	
	);
}
