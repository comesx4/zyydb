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


<form action='<?php echo U('save');?>' method='post'>
    <table class="table">
        <thead>
            <tr>
                <td colspan="2"><a href="<?php echo U('index');?>">单机列表</a></td>
            </tr>
        </thead>
        
        <tbody>
            <tr>
                <td class='first'>主机名称：</td>
                <td>
                    <input placeholer='主机名称' type='text' name='name' value='<?php echo ($data["name"]); ?>'/>
                </td>
            </tr>
            <tr>
                <td class='first'>主机类型：</td>
                <td>
                    <input type='text' name='type' placehloder='主机类型' value='<?php echo ($data["type"]); ?>'/>
                </td>
            </tr>
            <tr>
                <td class='first'>防护能力：</td>
                <td>
                    <input type='text' name='ability' placehloder='防护能力' value='<?php echo ($data["ability"]); ?>'/>
                </td>
            </tr>
            <tr>
                <td class='first'>价钱：</td>
                <td>
                    <input type='text' name='price' placehloder='主机价格' value='<?php echo ($data["price"]); ?>'/>
                </td>
            </tr>
            <tr>
                <td class='first'>排序：</td>
                <td>
                    <input type='text' name='sort' placehloder='排序' value='<?php echo ($data["sort"]); ?>'/>
                </td>
            </tr>
        </tbody>
        
        <tfoot>
            <tr>
                <td class='first'></td>
                <td>
                    <input type='hidden' name='id' value='<?php echo ($data["id"]); ?>'/>
                	<input class="btn btn-save" type="submit" value="保 存"/>
                    <a class="btn btn-cancel" href="<?php echo U('index');?>">取 消</a>
               </td> 
           </tr>
        </tfoot>
    </table>
</form>
    <script src="__ROOT__/Public/Bootstrap/assets/js/vendor/holder.min.js"></script>
     <script src="__ROOT__/Public/Bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>