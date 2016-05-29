<?php
/*
	@der 机房服务器管理
*/
Class Room_serverViewModel extends ViewModel{
	public $viewFields = array(
		'room_server' => array(
			'id','server_name','type','status','server_code','position','cpu','memory','disk','owner','time','express_code','network',
			'_type' => 'LEFT'
			),
		'mu_region' => array(
			'region_name',
			'_on'	=> 'room_server.region_id = mu_region.id'
			),
		);
}