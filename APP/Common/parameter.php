<?php

/**
 * Description of parameter
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 */

/**
 * 获取密码安全等级
 * @param string $password 密码
 * @return int 0:低,1:中,2:高
*/
function password_level($password) {
    if (preg_match('/^([0-9]{6,16})$/', $password)) {

        return 0;
    } else if (preg_match('/^[0-9 a-z]{6,16}$/', $password)) {

        return 1;
    } else if (preg_match('/^[0-9 a-z A-Z !@#$%^&*]{6,16}$/', $password)) {

        return 2;
    }
    return 0;
}

//侦测移动设备
function ismobile() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
        return true;

    //此条摘自TPM智能切换模板引擎，适合TPM开发
    if (isset($_SERVER['HTTP_CLIENT']) && 'PhoneClient' == $_SERVER['HTTP_CLIENT'])
        return true;
    //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA']))
    //找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
    //判断手机发送的客户端标志,兼容性有待提高
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array(
            'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile'
        );
        //从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    //协议法，因为有可能不准确，放到最后判断
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

//生成后台title内容
function makAdmTitle($a = '') {
    $title = C('WEBSITE_SET.SITE_NAME');
    if (!empty($a))
        $title.= ',' . $a;
    $title = str_replace(",", " - ", $title);
    return $title;
}

//生成网页title内容
function makwebtitle($a = '') {
    $title = C('WEBSITE_SET.SITE_NAME');
//     $title.= ',' . MODULE_NAME;
    switch (strtolower(MODULE_NAME)) {
        case 'index':
            break;
        case 'help':
            $title.= ',帮助中心';
            break;
        case 'cuxiao':
            $title.= ',促销活动';
            break;
        case 'notice':
            switch (ACTION_NAME) {
                case 'index':
                    $title.= ',公司公告';
                    break;
                case 'dynamicList':
                    //$title.= ',关于我们';
                    break;
            }
            break;
        case 'user':
            switch (ACTION_NAME) {
                case 'index':
                    $title.= ',用户中心';
                    break;
                case 'account':
                    $title.= ',帐户管理';
                    break;
                case 'userinfo':
                    $title.= ',用户信息';
                    break;
                default:
                    $title.= ',用户中心';
                    break;
            }
            break;
        case 'usergoods':
            $title.= ',我的产品';
            break;
        case 'userservice':
            $title.= ',我的服务';
            break;
        case 'order':
            $title.= ',我的订单';
            break;
        case 'login':
            $title.= ',网站登录';
            break;
        default :   
            $title.=',' . C(MODULE_NAME.'Name')[ACTION_NAME];
            break;
    }
    if (!empty($a))
        $title.= ',' . $a;
    $title = str_replace(",", " - ", $title);
    return $title;
}

function welcoming() {
    $h = date('G');
    if ($h < 11)
        $str = '早上好';
    else if ($h < 13)
        $str = '中午好';
    else if ($h < 17)
        $str = '下午好';
    else
        $str = '晚上好';
    return $str . '，欢迎使用' . C('WEBSITE_SET.SITE_NAME') . '!';
}

//返加数据键名
//@value 数组键值
//@$pname 数组名称
function getkeyname($value = 0, $pname = 'StatusList') {
    return array_keys(C($pname), $value)[0];
}

//获取当前时间
function getCurenTime(){
    return $_SERVER['REQUEST_TIME'];
}

//返回键值对序列
function array2str($arr, $mid = '=', $split = '|') {
    $str = '';
    foreach ($arr as $key => $v) {
        $str .=$key . $mid . $v . $split;
    }
    return $str;
}

//字符串形式返回产品信息
function productinfo2str($arr, $val, $mid = '=', $split = '|') {
    $info = C('ProductInfo' . $arr);
    $str = '';
    foreach ($info as $key => $v) {
        if (!isset($v['viewtostr']) || $v['viewtostr'])
            $str .=$v['des'] . $mid . $val[$key] . $split;
    }
    return $str;
}

//测试是否显示
function checkShow($val, $key = 'viewtostr') {

    if (is_null($val[$key]))
        return true;
    else {
        return $val[$key];
    }
}

//字符串形式返回产品信息
/*
 * @gid 产品ID
 * @data 产品数据集
 * @mid 连接符
 * @split分隔符
 */
function pp2str($gid, $data, $p, $mid = '=', $split = '|') {
    $info = C('ProductInfo' . $gid);
    $str = '';
    foreach ($info as $key => $v) {
        if (!isset($v['viewtostr']) || $v['viewtostr']) {
            $val = getconfigval($v, $data[$key]);
            $str .=$v['des'] . $mid . $val . $split;
            $p[$key] = $data[$key];
        }
    }
    return array($str, $p);
}

//获取列表值名称
function getconfigval($v, $val) {
    switch ($v['model']) {
        case 'input':
            $vl = $val;
            break;
        case 'list':
            $vl = getkeyname($val, $v['modelreg']); //从config中读取
            break;
        case 'radio':
            $vl = getkeyname($val, $v['modelreg']);
            break;
        case 'dblist':
            $db = M($v['table']);
            $vl = $db->where(array('id' => $val))->getField($v['valuefield']);
            break;
    }
    return $vl;
}

//根据结构生成表单
/*
 * @v 描述结构
 * @val 结构的值
 * @key 结构的键名
 */
function mkTableCell($v, $key, $val = NULL) {
     $hid=false;
    switch ($v['model']) {
        case 'hidden':
            $str = "<input type='hidden' value='{$val}' name='{$key}' />";
            $hid=true;
            $vl = $str;
            break;
         case 'input':
            $str = "<input type='text' value='{$val}' name='{$key}' />";
            $vl = $str;
            break;
        case 'list':
            $str = FLFParameter::getStatusList($key, $v['modelreg'], $val, 0);
            $vl = $str;
            break;
        case 'radio':     
            $str = FLFParameter::getStatusList($key, $v['modelreg'], $val, 1);
            $vl = $str;
            break;
        case 'dblist':
            $str = show_list($v['table'], $key, $v['valuefield'], $val);
            $vl = $str;
            break;
    }
    return array('ck' => $vl, 'des' => $v['des'], 'hid' => $hid);
}
//创建一个隐藏控件
function mkHiddenInput($name,$val)
{
    return "<input type='hidden' required value='{$val}' name='{$name}' />";
}
class FLFParameter {

    static function getStatusList($name, $pname = 'StatusList', $pid = 0, $mode = 0) {
        switch ($mode) {
            case 1;
                return FLFParameter::mk_rdlist(C($pname), $name, $pid);
            default :
                return FLFParameter::mk_list(C($pname), $name, $pid);
        }
    }

    static function mk_list($db, $name, $pid = 0) {
        $str = "<select name={$name}>";
        foreach ($db as $key => $v) {
            if ($pid == $v)
                $selected = 'selected';
            else
                $selected = '';
            $str .="<option {$selected} value='{$v}'>{$key}</option>";
        }
        $str .='</select>';
        return $str;
    }

    //生成 radio 列表
    static function mk_rdlist($db, $name, $pid = 0) {
        $str = "";
        foreach ($db as $key => $v) {
            if ($pid == $v)
                $checked = 'checked';
            else
                $checked = '';
            $str .="<input type='radio' {$checked} required value='{$v}' name='{$name}' />{$key}";
        }
        return $str;
    }

//生成一条日志    
    static function ADD_OrderLog($db, $pname, $pa = 'opdetail', $info = '') {
        if (!empty($info)) {
            $pname[$pa] = array2str($info);
        }
        M($db)->add($pname);
    }
}
