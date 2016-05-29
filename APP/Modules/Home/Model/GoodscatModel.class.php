<?php
Class GoodscatModel extends RelationModel{
	protected $_link = array(
		'goods'=>array(
				'mapping_type'=>HAS_MANY,
				'foreign_key'=>'cid',
				'parent_key'=>'id',
				'mapping_fields'=>'id,goods as name',
				),


		);

}