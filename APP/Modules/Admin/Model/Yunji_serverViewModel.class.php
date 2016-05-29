 <?php
Class Yunji_serverViewModel extends ViewModel{
	public $viewFields = array(
		'yunji_server' => array(
			'id','name','password','lan_ip','wan_ip','cpu','memory','band','wan_download_bandwidth','lan_upload_bandwidth','lan_download_bandwidth',
			'_type'	=> 'LEFT'
			),

		'living' => array(
			'id' => 'living_id','status','start','end','remark',
			'_type' => 'LEFT',
			'_on'  => 'yunji_server.id = living.tid'
			),
		'mu_server' => array(
				'server_ip',
				'_on' => 'yunji_server.mu_server_id = mu_server.id',
				'_type' => 'LEFT'
			),
		'user' => array(
			'username',
			'_type' => 'LEFT',
			'_on'	=> 'living.uid = user.id'
			),
		'mirroring' => array(
			'default_port' => 'port',
			'_type' => 'LEFT',
			'_on'   => 'mirroring.id = yunji_server.osid'
			),
		'os' => array(
			'os_code',
			'_on' => 'mirroring.oid = os.id'
			),

		);
}