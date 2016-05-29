<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="zh" dir="ltr">
<head>
    <title><?php echo (makadmtitle($mssg)); ?></title>
	<meta charset="utf-8" />
	<link href="__ROOT__/Public/Bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="__PUBLIC__/Css/common.css" />	
	<!--[if lt IE 9]>
	    <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      	<script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->   
      <!--  <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script> -->    
        <script type="text/javascript" src='__PUBLIC__/Js/jquery-1.8.2.min.js'></script> 
	<script src="__ROOT__/Public/Bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="__ROOT__/Data/Date/Js/laydate.js"></script>
	<script type="text/javascript" src='__PUBLIC__/Js/common.js'></script>
	<script type="text/javascript">
		<?php if(isset($tmp['search']) || isset($tmp['submit'])): ?>var is_search = true;
		<?php else: ?>
			var is_search = false;<?php endif; ?>
	</script>
</head>
<body>

	<div class='top-bar-main'>
		
		<?php if(is_array($now_node)): foreach($now_node as $key=>$v): switch($key): case "0": ?><a href="<?php echo U('Index/index');?>"><?php echo ($v["remark"]); ?></a>
					<span style='font-size:11px;' class='glyphicon glyphicon-menu-right'></span><?php break;?>

				<?php case "1": ?><a href="<?php echo U('index');?>"><?php echo ($v["remark"]); ?>管理</a>
					<span style='font-size:11px;' class='glyphicon glyphicon-menu-right'></span><?php break;?>

				<?php case "2": ?><a href=""><?php echo ($v["remark"]); ?></a><?php break; endswitch; endforeach; endif; ?>

		<div class='right'>    
			<a class="show-search"><span class="glyphicon glyphicon-search <?php if(isset($tmp['search']) || isset($tmp['submit'])): ?>show_true<?php endif; ?>"></span>搜索</a>
			<a href=''><span class='glyphicon glyphicon-refresh'></span>刷新</a>
			<a href='<?php echo ($_SERVER['HTTP_REFERER']); ?>'><span class='glyphicon glyphicon-circle-arrow-left'></span>返回</a>
		</div>
	</div>


<form action='<?php echo U('insert');?>' method='post'>
    <table class="table">
        <thead>
            <tr>
                <td colspan="2"><a href="<?php echo U('index');?>">单机租用托管</a></td>
            </tr>
        </thead>
        
        <tbody>
            <tr>
                <td class='first'>主机名称：</td>
                <td>
                    <input placeholer='主机名称' type='text' name='name' value=''/>
                </td>
            </tr>
		    <tr>
                <td class='first'>CPU型号：</td>
                <td>
					<input placeholer='CPU型号' type='text' name='cpu' value=''/>
				</td>
            </tr>
            <tr>
                <td class='first'>内存：</td>
                <td>
					<input placeholer='内存' type='text' name='memory' value=''/>
				</td>
            </tr>
            <tr>
                <td class='first'>硬盘：</td>
                <td>
					<input placeholer='内存' type='text' name='disk' value=''/>
				</td>
            </tr>
            <tr>
                <td class='first'>规格：</td>
                <td>
					<input placeholer='内存' type='text' name='size' value=''/>
				</td>
            </tr>
            <tr>
                <td class='first'>带宽：</td>
                <td>
					<input placeholer='带宽' type='text' name='band' value=''/>
				</td>
            </tr>
            <tr>
                <td class='first'>BGP端口流量费：</td>
                <td>
					<input placeholer='BGP端口流量费' type='text' name='flow_bgp' value=''/>
				</td>
            </tr>
            <tr>
                <td class='first'>静态端口流量费：</td>
                <td>
					<input placeholer='静态端口流量费' type='text' name='flow_static' value=''/>
				</td>
            </tr>
            <tr>
                <td class='first'>地点：</td>
                <td>
                    <?php echo ($city); ?>
				</td>
            </tr>
            <tr>
                <td class='first'>每增配1个IP地址价格：</td>
                <td>
                    <input type='text' name='ip_price' placehloder='每增配1个IP地址价格' value=''/>
                </td>
            </tr>
            <tr>
                <td class='first'>价格：</td>
                <td>
                    <input type='text' name='price' placehloder='价格' value=''/>
                </td>
            </tr>
            <tr>
                <td class='first'>说明：</td>
                <td>
					<textarea name='comment'></textarea>
				</td>
            </tr>
            <tr>
                <td class='first'>排序：</td>
                <td>
                    <input type='text' name='sort' placehloder='排序' value='1'/>
                </td>
            </tr>
            <tr> 
                <td class='first'>上架状态：</td>
                <td><?php echo ($online); ?></td>
            </tr>
        </tbody>
        
        <tfoot>
            <tr>
                <td class='first'></td>
                <td>
                	<input class="btn btn-save" type="submit" value="保 存"/>
                    <a class="btn btn-cancel" href="/cloud_server/index.php/Admin/MuIp/index">取 消</a>
               </td> 
           </tr>
        </tfoot>
    </table>
</form>
    <script src="__ROOT__/Public/Bootstrap/assets/js/vendor/holder.min.js"></script>
     <script src="__ROOT__/Public/Bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>