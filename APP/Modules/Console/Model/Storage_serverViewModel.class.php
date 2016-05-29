<?php
/*
	@der 存储云
*/
Class Storage_serverViewModel extends ViewModel{
	public $viewFields = array(
		'storage_server' => array(
			'id','wan_ip','cpu','memory','band',
			'_type' => 'LEFT'
			),
		'living' => array(
			'start','end','status','gid',
			'_on' => 'storage_server.id = living.tid'
			),
		);
}