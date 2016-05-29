 <?php
Class Yunji_serverViewModel extends ViewModel{
	public $viewFields = array(
		'yunji_server' => array(
			'id','name','lan_ip','wan_ip','cpu','memory','band','server_alias','wan_download_bandwidth','lan_download_bandwidth','lan_upload_bandwidth',
			'_type'	=> 'LEFT'
			),
		'city' => array(
				'city' => 'city_name',
				'_on' => 'yunji_server.city = city.id',
				'_type' => 'LEFT'
			),
		'living' => array(
			'status','start','end','remark','gid',
			'_type' => 'LEFT',
			'_on'  => 'yunji_server.id = living.tid'
			),
		'mirroring' => array(
			'name' => 'image_name','default_port' => 'port',
			'_type' => 'LEFT',
			'_on'   => 'mirroring.id = yunji_server.osid'
			),
		'os' => array(
			'os_code',
			'_on' => 'mirroring.oid = os.id'
			),

		);
}