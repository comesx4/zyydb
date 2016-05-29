<?php
Class MirrViewModel extends ViewModel{
	public $viewFields=array(
		'mirroring'=>array(
            'id','name','new','gather',
            '_type'=>'LEFT'

			),
		'os'=>array(
             'name'=>'os',
             '_on'=>'mirroring.oid=os.id',
             '_type'=>'LEFT'
			),
		'source'=>array(
			'source',
			'_on'=>'mirroring.sid=source.id'
			),


		);
}