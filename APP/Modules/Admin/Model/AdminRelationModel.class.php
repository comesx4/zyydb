<?php
Class AdminRelationModel extends RelationModel{
	 //定义主表名称
	Protected $tableName = 'admin';
	protected $_link = array(
		'role'=>array(
			'mapping_type'=>MANY_TO_MANY,
			'foreign_key'=>'user_id',
			'relation_foreign_key'=>'role_id',
			'relation_table'=>'kz_role_user',			
			'mapping_fields'=>'name'
			),

	   
	);

}