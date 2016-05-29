<?php
Class GoodsorderViewModel extends ViewModel{
	public $viewFields=array(
		'goodsorder'=>array(
			'id','number','gid','status','createtime','paytime','price','quantity','info','time','remark','orderType',
			'_type'=>'LEFT'
		),
		'goods'=>array(
			'goods',
			'_on'=>'goodsorder.gid=goods.id',
			'_type'=>'LEFT',
		),
		'service'=>array(
			'title',
			'_on'=>'goodsorder.gid=service.id'

		),


		);
}