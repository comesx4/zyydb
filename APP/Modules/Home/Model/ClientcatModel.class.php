<?php
Class ClientcatModel extends RelationModel{
	  protected $_link = array(
       'client'  =>  array(
       'mapping_type'=>HAS_MANY,

       'foreign_key'=>'cid',
       'mapping_fields'=>'name,img,href'

       ),
    
   );

	

	
}