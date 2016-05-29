<?php
/*
	@der IP网关表的视图模型
*/
Class Ip_gatewayViewModel extends ViewModel{
	public $viewFields = array(
			'ip_gateway' => array(
				'id','lan_netmask','wan_netmask','lan_gateway','wan_gateway','status','remark','time',
				'_type' => 'LEFT',
				),
			'ip_line' => array(
				'line_name',
				'_on'   => 'ip_gateway.line_id = ip_line.id',
				'_type' => 'LEFT'
				),
			'mu_region' => array(
				'region_name',
				'_on' => 'ip_gateway.region_id = mu_region.id'
				)
		);
}