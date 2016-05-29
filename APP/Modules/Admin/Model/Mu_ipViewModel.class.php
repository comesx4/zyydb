<?php
/*
	@der IP地址模型
*/
Class Mu_ipViewModel extends ViewModel{
	public $viewFields = array(
		'mu_ip' => array(
			'id','region_id','ip_gateway_id','ip','mac','type','status','remark','time',
			'_type' => 'LEFT'
			),
		'ip_gateway' =>array(
			'wan_gateway' => 'gateway',
			'_on' => 'mu_ip.ip_gateway_id = ip_gateway.id'
			)
		);
}