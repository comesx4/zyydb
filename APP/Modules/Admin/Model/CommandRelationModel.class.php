<?php
/*
    @der 命令表和标签表的之间的关联模型
*/
class CommandRelationModel extends RelationModel {

    protected $tableName = 'command';
    
    protected $_link = array(
            'command_tag' => array(
                    'mapping_type'          => MANY_TO_MANY, 
                    'mapping_name'          =>'tag',
                    'mapping_fields'        =>'id,type,name,color,status,remark',
                    'mapping_order'         =>'type asc,id asc',
                    // 'condition'              =>'status=1',
                    'foreign_key'           =>'command_id',     
                    'relation_foreign_key'  =>'command_tag_id',     
                    'relation_table'        =>'kz_command_and_tag'
                    ),
            'command'=> array(  
                    'mapping_type'=>HAS_MANY,               //一对多一个command表对应command_and_tag的多列
                    'class_name'=>'command_and_tag',       //关联的表
                    'foreign_key'=>'command_id',          //command表在command_and_tag表的中间字段
                    'mapping_name'=>'articles',          //返回数组的键名
                    'mapping_fields' => 'command_tag_id' //field
                    //'mapping_order'=>'create_time desc',
                     
       ),
            );

}//CommandRelationModel
?>