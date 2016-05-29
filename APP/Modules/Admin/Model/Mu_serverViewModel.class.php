<?php
/*
	@der 母服务器管理模型
*/
Class Mu_serverViewModel extends ViewModel{
	public $viewFields = array(
		'mu_server' => array(
			'id','region_id','server_ip','server_port','status','version','server_sum','secret_key','server_can_sum','remark','time','memory','cpu',
			'_type' => 'LEFT'
			),
		'mu_region' =>array(
			'region_name',
			'_on' => 'mu_server.region_id = mu_region.id'
			)
		);
}