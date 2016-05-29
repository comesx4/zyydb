<?php
/*
	@der 主机租用/托管
*/
Class Host_rentViewModel extends ViewModel{
	public $viewFields = array(
		'host_rent' => array(
			'id','wan_ip','cpu','memory','disk','cabinet_size','cabinet_position','assets_code','band',
			'_type' => 'LEFT'
			),
		'living' => array(
			'status','start','end','gid',
			'_on' => 'host_rent.id = living.tid'
			),
		);
}