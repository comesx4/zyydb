<?php
/*
  @der 产品续费参数的Model
*/
Class GoodsRenewViewModel extends ViewModel{
	public $viewFields=array(
            'goodsrenew_performace'=>array(
                	'name','id',
                	'_type'=>'LEFT'
             	),
            'goodscat'=>array(
           			'cat',
           			'_on'=>'goodsrenew_performace.cid=goodscat.id'
            	),

		);	


}