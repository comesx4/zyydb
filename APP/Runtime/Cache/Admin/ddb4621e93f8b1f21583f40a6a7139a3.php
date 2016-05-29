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
 
    <script type="text/javascript" src='__PUBLIC__/Js/help.js'></script>
    <script type="text/javascript">
    	var get_info='<?php echo U('NodeCat/get_info');?>';
    </script>

<form action='<?php echo U('insertNode');?>' method='post' >
	 <table class="table table-condensed table-hover table-active">
        <thead>
            <tr class="active">
                <td colspan="2">
                    <a class="btn btn-info" href="<?php echo U('node');?>"><?php echo ($mess); ?>列表</a>
                </td>
                <td colspan="12"></td>
            </tr>
        </thead>
		<tr>
			<td class='first'>所属类别:</td>
			<td class='addCat'><?php echo ($cat); ?></td>
		</tr>
		<tr>
			<td class='first'><?php echo ($mess); ?>名称:</td>
			<td><input type='text' name='name' value='<?php echo ($data["name"]); ?>'/></td>
		</tr>
	<?php if($mess == '方法'): ?><tr>
			<td class='first'>方法全名:</td>
			<td><input type='text' name='title' value='<?php echo ($data["name"]); ?>/'/></td>
		</tr>
		<tr>
			<td class='first'>是否显示:</td>
			<td >
				<input type='radio' checked id='is0' name='type' value='0'/><label for='is0'>显示</label>
				<input type='radio' id='is1' name='type' value='1'/><label for='is1'>不显示</label>
			</td>
		</tr><?php endif; ?>
		<tr>
			<td class='first'><?php echo ($mess); ?>描述:</td>
			<td><input type='text' name='remark'/></td>
		</tr>
	<?php if($mess == '控制器'): ?><tr>
			<td class='first'>常用属性:</td>
			<td>
				<input type='checkbox' checked id='attr' name='attr' value='1'/><label for='attr'>添加常用方法</label>
			</td>
		</tr><?php endif; ?>
		<tr>
			<td class='first'>是否开启:</td>
			<td>
				<input type='radio' checked name='status' value='1'/>开启<input type='radio' name='status' value='0'/>关闭
			</td>
		</tr>
		<tr>
			<td class='first'>排序:</td>
			<td><input type='text'  name='sort' value='0'/></td>
			   <input type="hidden" name="pid" value="<?php echo ($pid); ?>"/>
               <input type="hidden" name="level" value="<?php echo ($level); ?>"/>
		</tr>
		
		<tfoot>
            <tr>
                <td class='first'></td>
                <td>
                	<input class="btn btn-success" type="submit" value="保 存"/>
                    <a class="btn btn-default" href="<?php echo U('node');?>">取 消</a>
               </td> 
           </tr>
        </tfoot>
    </table>
</form>
    <script src="__ROOT__/Public/Bootstrap/assets/js/vendor/holder.min.js"></script>
     <script src="__ROOT__/Public/Bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>