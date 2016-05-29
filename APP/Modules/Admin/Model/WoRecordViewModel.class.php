<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WoRecordViewModel
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 */
class WoRecordViewModel  extends ViewModel {

	Protected $viewFields = array(
		'Wo_record' => array(
			'id', 'record', 'time', 'wid', 'type', 'aid',
			'_type' => 'LEFT'
			),
		'admin' => array(
			'username'=>'adminname', 
			'_on' => 'Wo_record.aid = admin.id '
			)
		);
}


