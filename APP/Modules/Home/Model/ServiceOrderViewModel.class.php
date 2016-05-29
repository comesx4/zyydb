<?php

Class GoodsorderViewModel extends ViewModel{
	public $viewFields=array(
		'goodsorder'=>array(
			'id','number','gid','status','createtime','paytime','price','quantity','info','time','remark',
			'_type'=>'LEFT'
		),
		'service'=>array(
			'title'=>'goods',
			'_on'=>'goodsorder.gid=service.id'

		),



		);
}