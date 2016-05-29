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

    <script type="text/javascript" src='__PUBLIC__/Js/group.js'></script>
<!-- 搜索部分 -->
<form action='' method='get'>
    <div class='search'>
        <div class='search-header'>
            <h5 style='float:left;'>搜索其实很简单</h5>
            <div class='hide-search' style='float:right;cursor:pointer;margin-right:20px;line-height:40px;'><span style='font-size:11px;' class='glyphicon glyphicon-chevron-up '></span>隐藏</div>
        </div>
        
        <div class='search-inner'>
           <dl>
                <dt>用户名称：</dt>
                <dd>
                    <input style='width:60px;' placeholder='用户名' type='text' value='<?php echo ($tmp["username"]); ?>' name='username' />
                </dd>
            </dl>
            <dl>
                <dt>时间范围：</dt>
                <dd>
                    <input id="min_date" placeholder='开始时间' value='<?php echo ($tmp["min-date"]); ?>' class="laydate-icon length2" type='text' name='min-date' />
                    -<input id='max_date' placeholder='结束' class="laydate-icon length2" value='<?php echo ($tmp["max-date"]); ?>' type='text' name='max-date' />
                </dd>
            </dl>
            
            <dl>
                <dt>地址：</dt>
                <dd><input type='text' name='name' value='<?php echo ($tmp["name"]); ?>' /></dd>
            </dl>
        </div>

        <div class='search-bottom'>
            <p style='float:right;margin-right:10px;'>
                <a class='btn btn-danger' href="<?php echo U();?>">清空</a>
                <input type='submit' name='search' class='btn btn-primary' value='搜索' />
            </p>
        </div>
    </div>
</form>

    <table class="table table-condensed table-hover " >
        <thead>
            <tr class='active'>
                <td  colspan="2" >
                    <a class="btn btn-success" href="<?php echo U('addGroup');?>">添加用户组</a>
                </td>
                <td colspan="8">
                    <div class="page-code"><?php echo ($fpage); ?></div>
                </td>
            </tr>
        </thead>

		<tr>
			<th>ID</th>
			<th>名称</th>
		    <th>描述</th>
			<th>状态</th>			
			<th>操作</th>
		</tr>
	<?php if(is_array($role)): foreach($role as $key=>$v): ?><tr>
			<td><?php echo ($v["id"]); ?></td>
			<td><?php echo ($v["name"]); ?></td>
			<td><?php echo ($v["remark"]); ?></td>
			<td><?php if($v['status']): ?>开启中<?php else: ?>关闭中<?php endif; ?></td>
				
			<td><a href="<?php echo U('level',array('rid'=>$v['id']));?>">配置权限</a><a href="<?php echo U('updateGroup',array('id'=>$v['id']));?>">[修改]</a><a onclick="return confirm('确定删除?');"href="<?php echo U('delGroup',array('id'=>$v['id']));?>">[删除]</a></td>
			
		</tr><?php endforeach; endif; ?>

	<tfoot>
            <tr class='active'>
                <td colspan="10" class="td-right">
                    <div class="page">
                        <div class="page-info"><?php echo ($pageInfo); ?></div>
                        <div class="page-code"><?php echo ($fpage); ?></div>
                   </div>
                </td>
            </tr>
        </tfoot>
    </table>

    <script src="__ROOT__/Public/Bootstrap/assets/js/vendor/holder.min.js"></script>
     <script src="__ROOT__/Public/Bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>