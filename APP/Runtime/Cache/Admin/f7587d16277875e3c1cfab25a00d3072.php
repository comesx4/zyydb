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

<form action='' method='post'>
    <table class="table" <?php if($isSort): ?>data-sort="true"<?php endif; ?>>
        <thead>
            <tr>
                <td colspan="2" class="td-left">
                <authority>
                	<a class="btn btn-success" href="<?php echo U('add');?>">添加服务器</a>
                </authority>
                </td>
                <td colspan="6" class="td-right">

                </td>
            </tr>
        </thead>

        <tbody>
            <tr>

                <th data-sort="true" data-field="id">ID</th>
                <th>主机名称</th>
                <th>主机类型</th>
                <th>防护能力</th>
                <th data-sort="true" data-field="time">价格</th>
                <th>排序</th>
                <th>操作</th>
            </tr>

            <?php if(is_array($data)): foreach($data as $key=>$row): ?><tr>
                <td><?php echo ($row["id"]); ?></td>
                <td><?php echo ($row["name"]); ?></td>
                <td><?php echo ($row["type"]); ?></td>
                <td><?php echo ($row["ability"]); ?></td>
                <td><?php echo ($row["price"]); ?></td>
                <td><?php echo ($row["sort"]); ?></td>
                <td>
                    <authority>
                        <a class="btn-icon" href="<?php echo U('update',array('id'=>$row['id']));?>"><i class="fa fa-edit"></i>编辑</a>
                    </authority>
                    <authority>
                        <a class="btn-icon del" href="<?php echo U('delete',array('id'=>$row['id']));?>">删除</a>
                    </authority>
                </td>
            </tr><?php endforeach; endif; ?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="8" class="td-right">
                	<div class="page">
                        <div class="page-info"><input type='submit' class='submit' name='submit' value='排序'/></div>
                        <div class="page-code"><?php echo ($fpage); ?></div>
                   </div>
                </td>
            </tr>
        </tfoot>
    </table>
</form>
    <script src="__ROOT__/Public/Bootstrap/assets/js/vendor/holder.min.js"></script>
     <script src="__ROOT__/Public/Bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>