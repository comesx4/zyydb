<?php

//读取管理员ID
function getAdminID(){
    return I('session.' . C('USER_AUTH_KEY'),'intval');
}

//客户的分类
function clientCat($name = 'cid', $cid = '') {
    $cat = M('Clientcat')->field('id,cat')->select();
    $str = '';
    $str .='<select name="' . $name . '">';
    $selected = '';
    foreach ($cat as $v) {
        if ($cid != '') {
            if ($cid == $v['id'])
                $selected = 'selected';
            else
                $selected = '';
        }
        $str .="<option {$selected} value='{$v['id']}'>{$v['cat']}</option>";
    }
    $str .='</select>';
    return $str;
}

/**
  为查询分页提供参数支持
 */
function getUrlParameter($parameter) {
    unset($parameter['page']); //移除原有的page元素
    return $parameter;
}

//对节点的排序
function node_sort($node, $che = null, $pid = 0) {
    $arr = array();
    foreach ($node as $key => $v) {
        //判断是不是配置权限
        if (!is_null($che)) {
            if (in_array($v['id'], $che))
                $v['checked'] = 'checked';
        }
        if ($v['pid'] == $pid) {
            $v['node'] = node_sort($node, $che, $v['id']);
            $arr[] = $v;
        }
    }

    return $arr;
}

//用户组的列表
function groupList($sum = null) {
    $group = M('Role')->field('pid,status', true)->select();
    //判断是不是修改
    if (is_null($sum)) {
        $str = "<select name='role[]'>";

        foreach ($group as $v) {
            $str .="<option value='{$v['id']}'>{$v['name']}[{$v['remark']}]</option>";
        }
        $str .='</select>';
    } else {

        $length = count($sum);
        $str = array();
        $selected = '';

        for ($i = 0; $i < $length; $i++) {
            $str[$i].="<select name='role[]'>";

            foreach ($group as $v) {

                if ($v['id'] == $sum[$i])
                    $selected = 'selected';
                else
                    $selected = '';

                $str[$i] .="<option {$selected} value='{$v['id']}'>{$v['name']}[{$v['remark']}]</option>";
            }

            $str[$i].='</select>';
        }
    }
    return $str;
}

/*
  产品的分类
  string $name 下拉菜单的name
  int $cid  传进来时候表示是要进修改界面
 */

function goodsCat($name = 'cat', $cid = null) {
    $cat = M('Goodscat')->field('sort', true)->select();
    $selected = '';

    $str = "<select name='{$name}'>";
    foreach ($cat as $v) {

        //判断有没有传入cid
        if ($cid != null) {
            if ($v['id'] == $cid)
                $selected = 'selected';
            else
                $selected = '';
        }

        $str .="<option {$selected} value='{$v['id']}'>{$v['cat']}</option>";
    }
    $str .='</select>';
    return $str;
}

//百度文本编辑器
function ueditor1($name = 'content', $width = '900', $height = '250') {
    $html = '';
    $html = "<script>
	          window.UEDITOR_HOME_URL='" . __ROOT__ . "/Data/ueditor1/';
	          window.onload=function(){
	           window.UEDITOR_CONFIG.initialFrameWidth={$width};
			   window.UEDITOR_CONFIG.initialFrameHeight={$height};
			   window.UEDITOR_CONFIG.savePath = ['uploads'];
			    window.UEDITOR_CONFIG.imageUrl='" . U(GROUP_NAME . '/Help/uploads') . "';
			   window.UEDITOR_CONFIG.imagePath ='" . __ROOT__ . "/Uploads/Tmp/';
			   UE.getEditor('{$name}');
	            }
				</script>";
    $html .='<script src="' . __ROOT__ . '/Data/ueditor1/ueditor.config.js"></script>';
    $html .='<script src="' . __ROOT__ . '/Data/ueditor1/ueditor.all.min.js"></script>';

    return $html;
}

/*
  产品样式的复选按钮
  string $name 复选框的name
 */

function goodsCss($name = 'css') {
    $stmt = M('Csscustom')->field('id,name')->select();
    $str = '';
    foreach ($stmt as $v) {
        $str .="<input id='{$v['id']}' style='position:relative;top:4px;margin-right:3px;' type='checkbox' name='{$name}[]' value='{$v['id']}'/><label for='{$v['id']}' style='color:#00A2CA;'>{$v['name']}</label>&nbsp;";
    }
    return $str;
}

/*
  产品样式的下拉列表
  string $name 下拉列表的name
 */
function cssOption($name = 'css') {
    $stmt = M('Csscustom')->field('id,name')->select();

    $str = "<select id='css' name='{$name}'>";

    foreach ($stmt as $v) {
        $str .="<option value='{$v['id']}'>{$v['name']}</option>";
    }

    $str .='</select>';

    return $str;
}

/*
  产品价格的城市下拉列表
  string $name 下拉列表的name
 */
function city($name = 'cid') {
    $stmt = M('city')->field('id,city')->order('sort ASC')->select();


    $str = "<select  name='{$name}'>";

    foreach ($stmt as $v) {
        $str .="<option value='{$v['id']}'>{$v['city']}</option>";
    }

    $str .='</select>';
    return $str;
}

//通用排序操作
function cat_sort($name = 'Attrcat') {
    //如果单击了排序
    if (isset($_POST['submit'])) {
        unset($_POST['submit']);
        $db = M($name);
        //循环修改排序
        foreach ($_POST as $key => $v) {
            $where = array('id' => $key);
            $db->where($where)->setField(array('sort' => $v));
        }
    }
}

//删除目录下的所有文件
function unlinks($path = "./Public/Uploads/Tmp") {
    $dir = opendir($path);
    readdir($dir);
    readdir($dir);

    while ($file = readdir($dir)) {

        $filename = $path . "/" . $file;
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
    closedir($dir);
}

//删除图片
function undir($id, $path = "./Uploads/Help/") {

    //多个删除
    if (!empty($id) && is_array($id)) {

        foreach ($id as $v) {

            //先删除目录下的图片
            unlinks($path . $v);

            //再删除目录
            rmdir($path . $v);
        }

        //单个删除		
    } elseif (!empty($id)) {
        unlinks($path . $id);
        rmdir($path . $id);
    }
}

//百度编辑器的上传
function upload() {
    /**
     * 向浏览器返回数据json数据
     * {
     *   'url'      :'a.jpg',   //保存后的文件路径
     *   'title'    :'hello',   //文件描述，对图片来说在前端会添加到title属性上
     *   'original' :'b.jpg',   //原始文件名
     *   'state'    :'SUCCESS'  //上传状态，成功时返回SUCCESS,其他任何值将原样返回至图片上传框中
     * }
     */
    import('ORG.Net.UploadFile');
    $upload = new UploadFile();

    if ($upload->upload('./Uploads/Tmp/')) {
        //导入处理图片类
        import('Class.Image', APP_PATH);
        $info = $upload->getUploadFileInfo();
        $path = $info[0]['savepath'] . $info[0]['savename'];

        //缩放
        //Image::thumb($path);
        //加图片水印
        // Image::water($path,'./Public/yifei.jpg');  
        //将每个上传后的名称给session
        $_SESSION['img'][] = $info[0]['savename'];
        echo json_encode(array(
            'url' => $info[0]['savename'],
            'title' => $title = htmlspecialchars($_POST['pictitle'], ENT_QUOTES),
            'original' => $info[0]['name'],
            'state' => 'SUCCESS'
                )
        );
    } else {
        echo json_encode(array('state' => $upload->getErrorMsg()));
    }
}

/*
  取无限分类中的一类
  @part int $pid [类别标识]
  @part int $id [这条信息会在最上面显示]
  @part string $tb [表名]
  return 返回无限分类中的一类
 */
function get_catOne($pid = 0, $id = false, $tb = 'Helpcat') {

    $data = M($tb)->field('id,pid,cat')->where(array('pid' => $pid))->order('sort DESC')->select();

    if (empty($data))
        return false;

    $str = "<select name='cid'>";
    $str.="<option value='0'>--请选择--</option>";
    foreach ($data as $v) {

        if ($id == $v['id'])
            $selected = 'selected';
        else
            $selected = '';

        $str .="<option {$selected} value='{$v['id']}'>{$v['cat']}</option>";
    }

    $str .='</select>';

    return $str;
}

/*
  @der 无限分类一段一段格式的显示(一般用于修改页面)
  @parm int $cid 分类ID
  @parm string $tb [表名]
 */
function get_catAll($cid, $tb = 'Helpcat') {
    //查找出该分类的所有顶级分类
    $parent = get_parent($cid, $tb);
    $parent = array_reverse($parent);
    $cat = '';
    foreach ($parent as $v) {
        $cat .=get_catOne($v['pid'], $v['id'], $tb);
    }
    return $cat;
}

/*
  @der (该函数暂未完工，请勿使用)取出指定级别无限分类的所有分类
  @param $db object $db 数据库连接对象
  @param int $level 指定的级别
 */
function get_appoint_cat($db, $level = 2, $pid = 0, $i = 0) {

    $arr = array();
    $tmp = array();

    $cat = $db->field('id,cat,pid')->select();

    foreach ($cat as $v) {

        if ($v['pid'] == $pid) {

            if ($i < $level) {
                //echo 112;
                if ($i + 1 == $level) {
                    $arr[] = $v;
                } else {
                    if ($i + 2 == $level) {
                        $arr = get_appoint_cat($db, $level, $v['id'], $i + 1);
                    } else
                        get_appoint_cat($db, $level, $v['id'], $i + 1);
                }
            }
        }
    }

    return $arr;
}

//随机生成mac地址
function creatMac() {
    $chars = "ABCDEFabcdef1234567890";
    $len = strlen($chars) - 1;
    for ($i = 0; $i < 3; $i++) {
        $hash .= ':' . $chars[mt_rand(0, $len)] . $chars[mt_rand(0, $len)];
    }
    $mac = '00:16:3e' . $hash;
    return $mac;
}
