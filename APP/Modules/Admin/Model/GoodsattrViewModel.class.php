<?php
Class GoodsattrViewModel extends ViewModel{
	public $viewFields=array(
		'goodsattr'=>array(
			'attr','id','sort',
			'_type'=>'LEFT'

		),
		'goods'=>array(
			'goods',
			'_on'=>'goodsattr.gid=goods.id'

		)

		);
}