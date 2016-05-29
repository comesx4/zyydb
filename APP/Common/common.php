<?php

function p() {
    $args = func_get_args();  //获取多个参数
    if (count($args) < 1) {
        p($_POST);
    }

    echo '<div style="width:100%;text-align:left"><pre>';
    //多个参数循环输出
    foreach ($args as $arg) {
        if (is_array($arg)) {
            print_r($arg);
            echo '<br>';
        } else if (is_string($arg)) {
            echo $arg . '<br>';
        } else {
            var_dump($arg);
            echo '<br>';
        }
    }
    echo '</pre></div>';
}

/*
  @der 密码加盐
 */

function md5Salt($password, $salt) {
    return md5(sha1($password) . $salt);
}

/**
  @der 输出错误信息
 */
function echo_error() {
    switch ($_GET['code_type']) {
        case '1':
            $msg = '用户名或密码错误';
            break;
        case '2':
            $msg = '验证码错误';
            break;
        default:
            $msg = '未知错误';
            break;
    }

    echo $msg;
}

/*
  @der 生成随机数字
  @return string
 */

function randStr($length = 6) {
    $str = '0123456789';
    $number = '';
    for ($i = 0; $i < $length; $i++) {
        $number .= $str{rand(0, 9)};
    }
    return $number;
}

/*
  @der 对手机、用户名进行过滤
 */

function infoMd5($name) {
    //将其中4位替换为****
    return substr_replace($name, '****', 3, 5);
}

/*
  @der curl请求方法
  @param string $url 请求的URL
  @param string $type [请求类型] default GET 如果是POST则放入POST的数据 array格式
  @param string $requestType [请求协议类型] (http || https) default http

  @return result
 */

function curl($url = '', $type = 'GET', $requestType = 'http') {
    $ch = curl_init();
    //获取成功或失败的数字不返回（如果没有这段代码，返回成功还会带着1，失败0）
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($requestType == 'https') {
        // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($type != 'GET') {
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // post的变量
        if (is_array($type)) {
            $type = http_build_query($type);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $type);
    }

    $outpot = curl_exec($ch);
    curl_close($ch);

    return json_decode($outpot);
}

//加密函数
function mymd5($name, $sta = 0) {

    $var = md5('www.xuanwo.cn');
    //加密
    if (!$sta) {
        return str_replace('=', '', base64_encode($name ^ $var));
    }
    //解密
    return base64_decode($name) ^ $var;
}

/* 将二维数组组合成二维数组
 * $arr  要组合的数组
 * $name 组合的名字
 * */

function peatarr($arr, $name) {
    foreach ($arr as $key => $v) {
        $arr[$key] = $v[$name];
    }
    return $arr;
}

//替换邮箱和号码的一部分为***
function replace_email($str) {
    return preg_replace('/(\S{3})(\S{4})(\S{1,})/is', '\1****\3', $str);
}

//生成订单号
function getNumber($length = 15) {
    //订单号集合
    $str = '0123456789';
    $leng = strlen($str) - 1;
    $number = date('YmdHis', time());
    for ($i = 0; $i < $length; $i++) {
        $number .= $str{rand(0, $leng)};
    }
    return $number;
}

//字符串的连接
function str_connect() {
    $arr = func_get_args();
    $count = count($arr);
    $str = '';
    for ($i = 0; $i < $count; $i++) {
        $str .=$arr[$i];
    }
    return $str;
}

//处理图片上传
function uploadimg($path = './Uploads/Logo/', $sub = false) {
    //导入文件上传类
    import('ORG.Net.UploadFile');
    $upload = new UploadFile(); // 实例化上传类
    $upload->maxSize = 3145728; // 设置附件上传大小
    $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
    $upload->savePath = $path; // 设置附件上传目录
    //判断是否使用子目录方式上传
    if ($sub) {
        $upload->autoSub = true;
        $upload->subType = 'date';
        $upload->dateFormat = 'Ym';
    }

    if (!$upload->upload()) {// 上传错误提示错误信息
        return $upload->getErrorMsg();
    } else {// 上传成功 获取上传文件信息
        $info = $upload->getUploadFileInfo();

        return $info;
    }
}

//百度文本编辑器
function ueditor($name = 'content', $width = '800', $height = '300') {
    $html = '';
    $html = "<script>
          window.UEDITOR_HOME_URL='/aliyun" . __ROOT__ . "/Data/ueditor3/';
          window.onload=function(){
           window.UEDITOR_CONFIG.initialFrameWidth={$width};
	   window.UEDITOR_CONFIG.initialFrameHeight={$height};	      
	       UE.getEditor('{$name}');
            }
		</script>";
    $html .='<script src="/aliyun' . __ROOT__ . '/Data/ueditor3/ueditor.config.js"></script>';
    $html .='<script src="/aliyun' . __ROOT__ . '/Data/ueditor3/ueditor.all.min.js"></script>';

    return $html;
}

/*
  产品的列表
  string $name 下拉菜单的name
  int $gid  传进来时候表示是要进修改界面

 */

function goodsList($name = 'gid', $gid = null) {
    $list = M('Goods')->order('cid ASC')->select();


    $str = "<select name='{$name}'>";
    foreach ($list as $v) {

        //判断有没有传入gid
        if ($gid != null) {
            if ($v['id'] == $gid)
                $selected = 'selected';
            else
                $selected = '';
        }

        $str .="<option {$selected} value='{$v['id']}'>{$v['goods']}</option>";
    }

    $str .='</select>';

    return $str;
}

//格式化时间函数
function format_time($time) {

    if ($time > 11)
        $time = ($time / 12) . '年';
    else
        $time = $time . '个月';

    return $time;
}

//获取城市名的方法
function get_city($name) {
    $city = C('city');
    return $city[$name];
}

/*
  判断POST里面的值是否有空
 */

function post_isnull() {
    $arr = func_get_args();

    foreach ($arr as $v) {
        if (!preg_match('/\S+$/is', $_POST[$v])) {
            return true;
        }
    }

    return false;
}

/*
  组合无限分类列表
  @$tb string 表名
 */

function mirroringCat($tb = 'Mirroringcat', $pid = 0, $level = 0) {
    //$cat=M($tb)->field('id,cat,pid,sort,status')->order('sort DESC')->select();
    $cat = M($tb)->order('sort DESC')->select();
    $arr = array();

    foreach ($cat as $v) {

        if ($v['pid'] == $pid) {

            //判断是否是1级分类
            if ($level == 0) {
                $v['two'] = 1;
            }

            $v['html'].=str_repeat('|---', $level);

            $arr[] = $v;

            $arr = array_merge($arr, mirroringCat($tb, $v['id'], $level + 1));
        }
    }

    return $arr;
}

/*
  无限分类下拉列表
  @$name string 下拉列表的name
  @pid int  传进来时表示要让你传进来的那个分类在最上面显示
  @$tb string 表名
 */

function mirroringSelect($name = 'pid', $pid = 0, $tb = 'Mirroringcat', $arrs = array()) {
    $arr = !empty($arrs) ? $arrs : Mirroringcat($tb);

    $selected = '';

    $str = "<select name={$name}>";
    if (empty($arrs))
        $str .='<option value="0">--根分类--</option>';

    foreach ($arr as $v) {
        if ($pid) {
            if ($pid == $v['id'])
                $selected = 'selected';
            else
                $selected = '';
        }
        $str .="<option {$selected} istwo='{$v['two']}' value='{$v['id']}'>{$v['html']}{$v['cat']}</option>";
    }
    $str .='</select>';

    return $str;
}

/*
  下拉列表
  @$tb   string 表名
  @$name string  下拉列表的name
  @$dname string 表名称字段名
  @$pid int 传进来时表示要让你传进来的那个分类在最上面显示
  @param string $field 要查询出来的字段   'id,usrename'
 */

function show_list($tb, $name, $dname, $pid = 0, $field = true, $where = '') {
    if ($field) {
        $filed = $dname . ',id';
    }

    $db = M($tb)->where($where)->field($field)->select();


    $str = "<select name={$name}>";

    foreach ($db as $v) {
        $selected = '';
        if ($pid) {
            if ($pid == $v['id'])
                $selected = 'selected';
            else
                $selected = '';
        }
        $str .="<option {$selected} value='{$v['id']}'>{$v[$dname]}</option>";
    }

    $str .='</select>';

    return $str;
}


/*
  复选框
  @$tb   string 表名
  @$name string  复选的name
  @$dname string 表名称字段名
  @$pid array 传进来要勾选的复选框的ID
 */
function show_checkbox($tb, $name, $dname, $pid = array()) {

    $db = M($tb)->order('sort DESC')->select();
    $str = '';
    $checked = '';
    foreach ($db as $v) {
        if (in_array($v['id'], $pid))
            $checked = 'checked';
        else
            $checked = '';

        $str .="<input {$checked} id='{$v[$dname]}' style='position:relative;top:3px;margin-right:5px;cursor:pointer;' type='checkbox' name=\"{$name}[{$v['id']}]\" value='{$v['id']}'/><label for='{$v[$dname]}' style='color:#00A2CA;cursor:pointer;'>{$v[$dname]}</label>&nbsp;&nbsp;";
    }
    return $str;
}


/*
  复选框
  @$tb   string 表名
  @$name string  ID名称
  @$dname string 表名称字段名
  @$classname string 样式名称
  @$pid array 传进来要勾选的复选框的ID
 */
function show_lista($tb, $name, $dname, $classname = '', $p =array('pid'=>'1') ) {

    $db = M($tb)->order('sort asc')->select();
    $str = '';
    $curent = '';
    $curentid = $p[$name];
    foreach ($db as $v) {
        $p[$name] = $v['id'];
        if ($v['id'] == $curentid)
            $curent = 'btn-warning';
        else
            $curent = 'btn-primary';
        $strp = U('', $p);
        $str .="<a class='{$curent} {$classname}' id='{$name}' href='{$strp}'>{$v[$dname]}</a>&nbsp;&nbsp;";
    }
    return $str;
}

/*
  放入分类ID查找出所有子分类ID
  @$id int 要放入的分类ID
  @$tb string [表名]
 */

function get_son($id, $tb = 'Mirroringcat') {
    $data = M($tb)->field('id,pid')->select();
    $arr = array();

    foreach ($data as $v) {
        if ($v['pid'] == $id) {
            $arr[] = $v['id'];
            $arr = array_merge($arr, get_son($v['id'], $tb));
        }
    }

    return $arr;
}

/*
  放入分类ID查找出所有子分类信息
  @$id int 要放入的分类ID
  @$tb string [表名]
 */

function get_chid($id, $tb = 'Helpcat') {
    $data = M($tb)->field('id,pid,cat')->select();
    $arr = array();

    foreach ($data as $v) {
        if ($v['pid'] == $id) {
            $arr[] = $v;
            $arr = array_merge($arr, get_son($v['id']));
        }
    }

    return $arr;
}

/*
  根据ID查找出该分类的所有顶级分类
  @part int $pid 依据的父级ID
  @part string $tb [表名]
 */

function get_parent($pid, $tb = 'Helpcat', $field = 'id,pid,cat', $stmt = '') {
    if (!$stmt) {
        $stmt = M($tb)->field($field)->order('sort DESC')->select();
    }
    $arr = array();

    foreach ($stmt as $v) {

        if ($v['id'] == $pid) {

            $arr[] = $v;
            $arr = array_merge($arr, get_parent($v['pid'], $tb, $field, $stmt));
        }
    }
    return $arr;
}

//对无限分类的排序
function mirr_sort($node, $pid = 0) {
    $arr = array();
    $conts = M($node)->select();

    foreach ($conts as $key => $v) {

        if ($v['pid'] == $pid) {
            $v['mirr'] = mirr_sort($node, $v['id']);
            $arr[] = $v;
        }
    }

    return $arr;
}

/*
  取出无线分类的某一段
  @parm string $name 要取出一段当中的顶级名称
  @parm int $type 1代表返回下拉列表，0返回数组
  @parm string $tb 表名
  @parm int $cid  修改时，该CID的分类会置顶
  @parm string $bname  下拉列表的name
 */

function getCat_one($name, $type = 1, $tb = 'Mirroringcat', $cid = 0, $bname = 'cid') {
    //取出镜像分类下拉列表
    $cat = mirroringCat($tb);
    $bool = false;
    $arr = array();

    foreach ($cat as $v) {

        if ($bool && $v['pid'] != 0) {
            $arr[] = $v;
        } else {
            $bool = false;
        }

        if ($v['cat'] == $name && $v['pid'] == 0) {
            // $arr[]=$v;
            $bool = true;
        }
    }
    if ($type)
        $arr = mirroringSelect($bname, $cid, $tb, $arr);

    return $arr;
}

/*
  组合无限分类的多维数组
  @$tb 表名称
  @return arr 返回无限分类的多维数组
 */

function catArr($tb = 'Helpcat', $pid = 0) {

    $cat = M($tb)->order('sort DESC')->select();

    $arr = array();

    foreach ($cat as $v) {

        if ($v['pid'] == $pid) {
            $v['chid'] = catArr($tb, $v['id']);

            $arr[] = $v;
        }
    }
    return $arr;
}

/*
  组合无限分类的多维数组 status=1
  @$tb 表名称
  @return arr 返回无限分类的多维数组
 */

function catArrByStatus($tb = 'Helpcat', $pid = 0) {

    $where = array('status' => 1);
    $cat = M($tb)->where($where)->order('sort DESC')->select();

    $arr = array();

    foreach ($cat as $v) {

        if ($v['pid'] == $pid) {
            $v['chid'] = catArrByStatus($tb, $v['id']);

            $arr[] = $v;
        }
    }
    return $arr;
}

/*
  分页类的便捷调用
  @part $db object 数据库连接对象
  @part $where array 条件
  @part $sum int [分页的划分数]
  @part $pwhere string [要保留的where条件]
  return array 返回分页对象($arr['0'])和分页信息($arr['1'])
 */
function X($db, $where = '', $sum = 0, $pwhere = '') {
    if ($sum == 0)
        $sum = C('PAGE_NUM');
    import('Class.Page', APP_PATH);
    $arr = array();
    $num = $db->where($where)->count();
    $arr['0'] = new Page($num, $sum, $pwhere);
    if ($num > $sum) {
        $arr['1'] = $arr['0']->fpage();
    } else {
        $arr['1'] = '';
    }
    return $arr;
}

/*
  邮件发送的方法
  @param string $username 邮件接收人
  @param string $title 邮件标题
  @param string $content 邮件内容
 */

function send_mail($username, $title, $content) {
    require APP_PATH . 'class/PHPMailerAutoload.php';
    $config = C('THINK_EMAIL');
    $mail = new PHPMailer;
    $mail->isSMTP(); // Set mailer to use SMTP
    $mail->CharSet = "utf-8";
    $mail->SMTPDebug = 0;                     // 关闭SMTP调试功能
    // 1 = errors and messages
    // 2 = messages only
    $mail->SMTPAuth = true;                  // 启用 SMTP 验证功能
    $mail->SMTPSecure = '';                 // 使用安全协议
    $mail->WordWrap = 50; // Set word wrap to 50 characters  
    $mail->isHTML(true); // Set email format to HTML

    $mail->Host = $config['SMTP_HOST'];  // SMTP 服务器
    $mail->Port = $config['SMTP_PORT'];  // SMTP服务器的端口号
    $mail->Username = $config['SMTP_USER'];  // SMTP服务器用户名
    $mail->Password = $config['SMTP_PASS'];  // SMTP服务器密码
    $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
    $replyEmail = $config['REPLY_EMAIL'] ? $config['REPLY_EMAIL'] : $config['FROM_EMAIL'];
    $replyName = $config['REPLY_NAME'] ? $config['REPLY_NAME'] : $config['FROM_NAME'];
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->addAddress($username); // Name is optional     

    $mail->Subject = $title;
    $mail->Body = $content;
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    return $mail->Send() ? true : $mail->ErrorInfo;
}

/**
 * 系统邮件发送函数
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题 
 * @param string $body    邮件内容
 * @param string $attachment 附件列表
 * @return boolean 
 */
function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null) {
    $config = C('THINK_EMAIL');
    require APP_PATH . 'class/PHPMailerAutoload.php';
    $mail = new PHPMailer(); //PHPMailer对象
    $mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();  // 设定使用SMTP服务
    $mail->SMTPDebug = 0;                     // 关闭SMTP调试功能
    // 1 = errors and messages
    // 2 = messages only
    $mail->SMTPAuth = true;                  // 启用 SMTP 验证功能
    $mail->SMTPSecure = '';                 // 使用安全协议
    $mail->Host = $config['SMTP_HOST'];  // SMTP 服务器
    $mail->Port = $config['SMTP_PORT'];  // SMTP服务器的端口号
    $mail->Username = $config['SMTP_USER'];  // SMTP服务器用户名
    $mail->Password = $config['SMTP_PASS'];  // SMTP服务器密码
    $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
    $replyEmail = $config['REPLY_EMAIL'] ? $config['REPLY_EMAIL'] : $config['FROM_EMAIL'];
    $replyName = $config['REPLY_NAME'] ? $config['REPLY_NAME'] : $config['FROM_NAME'];
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($to, $name);
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}

/*
  @der 生成工单号
 */

function work_order($length = 7) {

    $str = '';
    $luan = '0123456789zxcvbnmlkjhgfdsaqwertyuip';
    $count = strlen($luan);

    for ($i = 0; $i < $length; $i++) {
        $str .= $luan{mt_rand(0, $count - 1)};
    }

    return $str;
}

/*
  清除POST中值的方法
 */

function unset_post() {
    $arr = func_get_args();

    foreach ($arr as $v) {
        unset($_POST[$v]);
    }
}

/*
  生成记录的方法
  @part object $db 数据库连接
  @part string $mess 要记录的消息
  @part array $arr 根据键（对应数据库字段）传入值（对应字段的值）

 */

function create_record($db, $mess, $arr) {
    $add = array(
        'time' => $_SERVER['REQUEST_TIME'],
        'record' => $mess,
    );

    foreach ($arr as $key => $v) {
        $add[$key] = $v;
    }

    if ($db->add($add))
        return true;
    else
        return false;
}

/*
  计算工单平均时间的方法
  $type
 */

function get_avg($type = 3, $addTime = 0) {
    $fileName = $type == 2 ? 'replayTime' : 'submitTime';
    $arrName = $type == 2 ? 'replay' : 'submit';
    $db = M('Wo_record');

    //查询出工单的所有分类
    $allCid = M('Scencecat')->field('id')->select();

    //存到文件中的数组
    $arr = array();

    foreach ($allCid as $cid) {

        //去工单表查找出该分类下所有的工单ID
        if (!$wid = M('Wo')->field('id,time')->where(array('cid' => $cid['id']))->select()) {
            //如果该分类下面没有工单，退出当层循环。
            continue;
        }

        $sum = 0;   //个数    
        $vtime = 0;  //所用的时间

        foreach ($wid as $v) {
            $where = array('wid' => $v['id'], 'type' => $type);

            //如果是求客服回复时间
            if ($type == 2) {
                //判断该工单是否有客服回复了
                if ($time = $db->where($where)->getField('time')) {
                    $vtime +=$time - $v['time'];
                    $sum++;
                }

                //反之就是求工单到处理结束所需的时间
            } else {
                //判断该工单是否结束了
                if ($time = $db->where($where)->getField('time')) {
                    $where = array('wid' => $v['id'], 'type' => 2);
                    //获取客服第一次回复的时间
                    $replyTime = $db->where($where)->getField('time');
                    $vtime +=$time - $replyTime;
                    $sum++;
                }
            }
        }

        $avg = floor($vtime / $sum); //平均所用的时间(秒)
        //$avg=date('Y-m-d H:i:s',time()+$avg); //计算出来的给用户看的客服回复时间

        $arr[$arrName . $cid['id']] = $avg + $addTime;
    }


    //将数组写入文件中 
    if (F($fileName, $arr, './APP/Conf/')) {
        return true;
    } else {
        return false;
    }
}

/*
  格式化时间
  @param int $time 要格式化的时间戳
  return 返回格式化后的时间
 */

function formatTime($time) {

    $btime = '';

    switch ($time) {
        case 0:
            $btime = '1秒';
            break;
        case $time < 60:
            $btime = $time . '秒';
            break;
        case $time < 3600:
            $btime = floor($time / 60) . '分钟';
            break;
        case $time < 86400:
            $btime = floor($time / 3600) . '小时' . floor(($time % 3600) / 60) . '分钟';
            break;
        default:
            $btime = floor($time / 86400) . '天' . floor(($time % 86400) / 3600) . '小时' . floor((($time % 86400) % 3600) / 60) . '分钟';
            break;
    }

    return $btime;
}

/*
  @der 过滤数组中的所有元素
  return array 过滤后的数组
 */

function arr_filter($arr) {
    foreach ($arr as $key => $v) {
        if (is_array($v)) {
            arr_filter($v);
        } else {
            $arr[$key] = htmlspecialchars($arr[$key]);
        }
    }

    return $arr;
}

/**
  @der 产品的状态码
  @param int $type
  0 云服务器状态码, 1腾讯云状态码 , 2阿里云状态码
  @param int $status 状态
  @param boolean||mixed $name 是否返回select标签 default false
  @return string
 */
function goods_status($type = 1, $status = 0, $name = false) {
    switch ($type) {
        case 1:
            $arr = array(
                '0' => '审核中',
                '1' => '创建失败',
                '2' => '运行中',
                '3' => '创建中',
                '4' => '已关机',
                '5' => '已退还',
                '6' => '退还中',
                '7' => '重启中...',
                '8' => '开机中...',
                '9' => '关机中...',
                '10' => '密码重置中...',
                '11' => '正在回滚...',
                '12' => '镜像制作中',
                '13' => '带宽设置中',
                '14' => '重装系统中',
                '19' => '升级中',
                '22' => '删除中...',
                '23' => '已删除',
                '24' => '故障',
            );
            break;

        case 19:
            $arr = array(
                '0' => '审核中',
                '1' => '故障',
                '2' => '运行中',
                '3' => '创建中',
                '4' => '已关机',
                '5' => '已退还',
                '6' => '退还中',
                '7' => '重启中...',
                '8' => '开机中',
                '9' => '关机中',
                '10' => '密码重置中',
                '11' => '格式化中',
                '12' => '镜像制作中',
                '13' => '带宽设置中',
                '14' => '重装系统中',
                '15' => '域名绑定中',
                '16' => '域名解析中',
                '17' => '负载均衡绑定中',
                '18' => '负载均衡解绑中',
                '19' => '升级中',
                '20' => '秘钥下发中',
                '21' => '热迁移中'
            );

        case 20:
            $arr = array(
                '0' => '审核中',
                '1' => '上架',
                '2' => '下架',
                '3' => '断网',
                '4' => '封IP',
                '5' => '封80',
                '6' => '其他',
            );
            break;
        case 22:
            $arr = array(
                '0' => '审核中',
                '1' => '故障',
                '2' => '运行中',
                '3' => '创建中',
                '4' => '已关机',
                '5' => '已退还',
                '6' => '退还中',
                '7' => '重启中...',
                '8' => '开机中',
                '9' => '关机中',
                '10' => '密码重置中',
                '11' => '格式化中',
                '12' => '镜像制作中',
                '13' => '带宽设置中',
                '14' => '重装系统中',
                '15' => '域名绑定中',
                '16' => '域名解析中',
                '17' => '负载均衡绑定中',
                '18' => '负载均衡解绑中',
                '19' => '升级中',
                '20' => '秘钥下发中',
                '21' => '热迁移中'
            );
            break;

        case 23:
            $arr = array(
                '0' => '审核中',
                '1' => '上架',
                '2' => '下架',
                '3' => '断网',
                '4' => '封IP',
                '5' => '封80',
                '6' => '其他',
            );
            break;

        case 'logs':
            $arr = array(
                // '0' => '删除云服务器',
                '1' => '创建云服务器',
                '2' => '升级云服务器',
                '3' => '续费云服务器',
                '4' => '云服务器关机',
                '5' => '云服务器开机',
                '6' => '云服务器重启',
                '7' => '云服务器改名',
                '8' => '云服务器重装系统',
                '9' => '云服务器重置密码',
                '10' => '创建云磁盘',
                '11' => '云磁盘改名',
                '12' => '卸载云磁盘',
                '13' => '克隆云磁盘',
                '14' => '删除云磁盘',
                '15' => '创建快照',
                '16' => '快照改名',
                '17' => '回滚快照',
                '18' => '保护快照',
                '19' => '解除快照保护',
                '20' => '删除快照',
                '21' => '创建镜像',
                '22' => '镜像改名',
                '23' => '删除镜像',
                '24' => '挂载云磁盘',
                '25' => '设置带宽',
                '26' => '设置内存',
                '27' => '设置CPU',
            );
            break;
    }
    //状态数组

    if (empty($arr[$status]) && !$name) {
        return '未知状态';
    } elseif (!$name) {
        return $arr[$status];
    }

    if ($status == -1) {
        $se = 'selected';
    } else {
        $se = '';
    }

    $html = "<select name='{$name}'>";
    $html.= '<option ' . $se . ' value="-1">--请选择--</option>';
    foreach ($arr as $key => $v) {
        if ($key == $status) {
            $selected = 'selected';
        } else {
            $selected = '';
        }
        $html .= "<option {$selected} value='{$key}'>{$v}</option>";
    }
    $html .= '</select>';

    echo $html;
}

/**
  @der 获取产品的表名称
 */
function get_table_name($key = 'goods1') {
    $arr = array(
        'goods1' => 'yunji_server',
        'goods17' => 'tenxun_server',
        'goods18' => 'ali_server'
    );

    return $arr[$key];
}

/**
  @der 类型: （废弃，使用goods_status方法代替）
  0、删除服务器，1、创建云服务器，2、升级云服务器，
  3、续费云服务器，4、云服务器关机，5云服务器开机，
  6、云服务器重启，7、服务器改名，8、服务器重装系统，
  9、服务器重置密码，10、创建磁盘，11、磁盘改名，12卸载磁盘，
  13、克隆磁盘，14、删除磁盘，15、创建快照，16、快照改名，17回滚快照，
  18、保护快照，19、解除保护、20、删除快照，21、创建镜像，22、镜像改名，23、删除镜像
 */
function getLogsType($key) {
    $arr = array(
        '0' => '删除云服务器',
        '1' => '创建云服务器',
        '2' => '升级云服务器',
        '3' => '续费云服务器',
        '4' => '云服务器关机',
        '5' => '云服务器开机',
        '6' => '云服务器重启',
        '7' => '云服务器改名',
        '8' => '云服务器重装系统',
        '9' => '云服务器重置密码',
        '10' => '创建云磁盘',
        '11' => '云磁盘改名',
        '12' => '卸载云磁盘',
        '13' => '克隆云磁盘',
        '14' => '删除云磁盘',
        '15' => '创建快照',
        '16' => '快照改名',
        '17' => '回滚快照',
        '18' => '保护快照',
        '19' => '解除快照保护',
        '20' => '删除快照',
        '21' => '创建镜像',
        '22' => '镜像改名',
        '23' => '删除镜像'
    );

    return $arr[$key];
}

function remove_xss($val) {
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <java\0script>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=@avascript:alert('XSS')>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
        // @ @ search for the hex values
        $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(&#0{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }
    return $val;
}

/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 * @author Anyon Zou <zoujingli@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-10-10 10:10
 * @return String
 */
function encode($string = '', $skey = '429710096@qq.com') {
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key].=$value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}

/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 * @author Anyon Zou <zoujingli@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-10-10 10:10
 * @return String
 */
function decode($string = '', $skey = '429710096@qq.com') {
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}

/* *
 * 支付宝接口公用函数
 * 详细：该类是请求、通知返回两个文件所调用的公用函数核心处理文件
 * 版本：3.3
 * 日期：2012-07-19
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 */

/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param $para 需要拼接的数组
 * return 拼接完成以后的字符串
 */
function createLinkstring($para) {
    $arg = "";
    while (list ($key, $val) = each($para)) {
        $arg.=$key . "=" . $val . "&";
    }
    //去掉最后一个&字符
    $arg = substr($arg, 0, count($arg) - 2);

    //如果存在转义字符，那么去掉转义
    if (get_magic_quotes_gpc()) {
        $arg = stripslashes($arg);
    }

    return $arg;
}

/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
 * @param $para 需要拼接的数组
 * return 拼接完成以后的字符串
 */
function createLinkstringUrlencode($para) {
    $arg = "";
    while (list ($key, $val) = each($para)) {
        $arg.=$key . "=" . urlencode($val) . "&";
    }
    //去掉最后一个&字符
    $arg = substr($arg, 0, count($arg) - 2);

    //如果存在转义字符，那么去掉转义
    if (get_magic_quotes_gpc()) {
        $arg = stripslashes($arg);
    }

    return $arg;
}

/**
 * 除去数组中的空值和签名参数
 * @param $para 签名参数组
 * return 去掉空值与签名参数后的新签名参数组
 */
function paraFilter($para) {
    $para_filter = array();
    while (list ($key, $val) = each($para)) {
        if ($key == "sign" || $key == "sign_type" || $val == "")
            continue;
        else
            $para_filter[$key] = $para[$key];
    }
    return $para_filter;
}

/**
 * 对数组排序
 * @param $para 排序前的数组
 * return 排序后的数组
 */
function argSort($para) {
    ksort($para);
    reset($para);
    return $para;
}

/**
 * 写日志，方便测试（看网站需求，也可以改成把记录存入数据库）
 * 注意：服务器需要开通fopen配置
 * @param $word 要写入日志里的文本内容 默认值：空值
 */
function logResult($word = '') {
    $fp = fopen("log.txt", "a");
    flock($fp, LOCK_EX);
    fwrite($fp, "执行日期：" . strftime("%Y%m%d%H%M%S", time()) . "\n" . $word . "\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}

/**
 * 远程获取数据，POST模式
 * 注意：
 * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
 * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
 * @param $url 指定URL完整路径地址
 * @param $cacert_url 指定当前工作目录绝对路径
 * @param $para 请求的数据
 * @param $input_charset 编码格式。默认值：空值
 * return 远程输出的数据
 */
function getHttpResponsePOST($url, $cacert_url, $para, $input_charset = '') {

    if (trim($input_charset) != '') {
        $url = $url . "_input_charset=" . $input_charset;
    }
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); //SSL证书认证
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); //严格认证
    curl_setopt($curl, CURLOPT_CAINFO, $cacert_url); //证书地址
    curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 显示输出结果
    curl_setopt($curl, CURLOPT_POST, true); // post传输数据
    curl_setopt($curl, CURLOPT_POSTFIELDS, $para); // post传输数据
    $responseText = curl_exec($curl);
    //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($curl);

    return $responseText;
}

/**
 * 远程获取数据，GET模式
 * 注意：
 * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
 * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
 * @param $url 指定URL完整路径地址
 * @param $cacert_url 指定当前工作目录绝对路径
 * return 远程输出的数据
 */
function getHttpResponseGET($url, $cacert_url) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 显示输出结果
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); //SSL证书认证
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); //严格认证
    curl_setopt($curl, CURLOPT_CAINFO, $cacert_url); //证书地址
    $responseText = curl_exec($curl);
    //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($curl);

    return $responseText;
}

/**
 * 实现多种字符编码方式
 * @param $input 需要编码的字符串
 * @param $_output_charset 输出的编码格式
 * @param $_input_charset 输入的编码格式
 * return 编码后的字符串
 */
function charsetEncode($input, $_output_charset, $_input_charset) {
    $output = "";
    if (!isset($_output_charset))
        $_output_charset = $_input_charset;
    if ($_input_charset == $_output_charset || $input == null) {
        $output = $input;
    } elseif (function_exists("mb_convert_encoding")) {
        $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
    } elseif (function_exists("iconv")) {
        $output = iconv($_input_charset, $_output_charset, $input);
    } else
        die("sorry, you have no libs support for charset change.");
    return $output;
}

/**
 * 实现多种字符解码方式
 * @param $input 需要解码的字符串
 * @param $_output_charset 输出的解码格式
 * @param $_input_charset 输入的解码格式
 * return 解码后的字符串
 */
function charsetDecode($input, $_input_charset, $_output_charset) {
    $output = "";
    if (!isset($_input_charset))
        $_input_charset = $_input_charset;
    if ($_input_charset == $_output_charset || $input == null) {
        $output = $input;
    } elseif (function_exists("mb_convert_encoding")) {
        $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
    } elseif (function_exists("iconv")) {
        $output = iconv($_input_charset, $_output_charset, $input);
    } else
        die("sorry, you have no libs support for charset changes.");
    return $output;
}

/* *
 * MD5
 * 详细：MD5加密
 * 版本：3.3
 * 日期：2012-07-19
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 */

/**
 * 签名字符串
 * @param $prestr 需要签名的字符串
 * @param $key 私钥
 * return 签名结果
 */
function md5Sign($prestr, $key) {
    $prestr = $prestr . $key;
    return md5($prestr);
}

/**
 * 验证签名
 * @param $prestr 需要签名的字符串
 * @param $sign 签名结果
 * @param $key 私钥
 * return 签名结果
 */
function md5Verify($prestr, $sign, $key) {
    $prestr = $prestr . $key;
    $mysgin = md5($prestr);

    if ($mysgin == $sign) {
        return true;
    } else {
        return false;
    }
}

function get_city_name($cid)
{
    return M('city')->where(array('id'=> $cid))->field('city')->select()[0]['city'];
}

function auto_warp($str)
{
    $arr= explode("\n", $str);
    $html = '';
    foreach ($arr as $a)
    {
        $html = $html.'<dd class="comment">'.$a.'</dd>';
    }

    return $html;
}