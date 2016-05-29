<?php
/*
	@der 扩展标签库
*/
Class TagLibHd extends TagLib{
	protected $tags = array(
		//云服务器(用户已购买产品)
		'listGoods' =>array('attr'=>'','close'=>1)
		);

	/*
		@der 云服务器(用户已购买产品)
	*/
	public function _listGoods($attr,$content){
		$attr = $this->parseXmlAttr($attr);
		$str = <<<str
	<?php
		\$gid 	   = M('Living')->field('gid')->where(array('uid'=>\$_SESSION['id'] , 'status' => array('NEQ',0)))->group('gid')->select();
	    \$where    = array('id'=>array( 'in',implode(',',peatarr(\$gid,'gid')) ));	
		\$left_nav = M('Goods')->field('id,goods')->where(\$where)->select();
		foreach (\$left_nav as \$v):
	?>
str;
		$str .= $content;
		$str .= '<?php endforeach; ?>';
		return $str;
	}
}