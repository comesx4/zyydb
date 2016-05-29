<?php
/*
 * 您正在使用的是SK在线客服系统。
 *
 * http://www.dns-w.cn
 * 您可以完全免费使用，但不得修改版权信息！
 */
define("APP_ROOT",dirname(dirname(__FILE__)));
/*
 *  
 */
$webimroot = "";   /*这是您的系统安装目录*/

/*
 *  Internal encoding
 */
$webim_encoding = "utf-8";

/*
 *  这是您的数据库配置信息
 */
$mysqlhost = "localhost";
$mysqldb = "weixinls";
$mysqllogin = "root";
$mysqlpass = "";

$dbencoding = "utf8";
$force_charset_in_connection = true;

/*
 *  Mailbox
 */
$webim_mailbox = "fanlifeng@supwall.com";
$mail_encoding = "utf-8";

/*
 *  Locales
 */
$home_locale = "zh";						/* native name will be used in this locale */
$default_locale = "zh";						/* if user does not provide known lang */


?>
