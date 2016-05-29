<?php
/*
  @der 节点的模型
*/
Class NodeViewModel extends ViewModel{
	public $viewFields=array(
            'node'=>array(
                'id','name','title','status','remark','cid','pid','sort','level',
                '_type'=>'LEFT'
            	),
            'nodecat'=>array(
                'class','cat',
                '_on'=>'node.cid=nodecat.id'
            	),
 
		);
}