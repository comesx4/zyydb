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

<!-- 搜索部分 -->
<form action='' method='get'>
    <div class='search'>
        <div class='search-header'>
            <h5 style='float:left;'>搜索其实很简单</h5>
            <div class='hide-search' style='float:right;cursor:pointer;margin-right:20px;line-height:40px;'><span style='font-size:11px;' class='glyphicon glyphicon-chevron-up '></span>隐藏</div>
        </div>
        
        <div class='search-inner'>
           <dl>
                <dt>选择控制器：</dt>
                <dd>
                   <?php echo ($ctrid); ?>
                </dd>
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

<form action='' method='post'>
	<table class="table table-condensed table-hover " >
	<thead>
            <tr class='active'>
                <td colspan="2" class="td-left"><a class="btn btn-success" href="<?php echo U('addNode');?>">添加应用</a>               
                </td>
                <td colspan="5" class="td-right"><input type='submit' name='submit' class='submit' value='排序'/>
                </td>
            </tr>
        </thead>

	<tr>
		<th>ID</th>
		<th>类型</th>
		<th>名称</th>
		<th>控制器全称</th>
		<th>所属类别</th>
		<th>排序</th>
		<th>操作</th>
	</tr>
	<?php if(is_array($node)): foreach($node as $key=>$v): ?><tr>
		    <td style='color:blue;'><?php echo ($v["id"]); ?></td>
		    <td style='color:blue;'>应用</td>
			<td style='color:blue;'><?php echo ($v["name"]); ?>[<?php echo ($v["remark"]); ?>]</td>
			<td style='color:blue;'>无</td>
			<td style='color:blue;'><?php echo ($v["cat"]); ?></td>
			<td style='color:blue;'><input type='text' class='sort length1' name='<?php echo ($v["id"]); ?>' value='<?php echo ($v["sort"]); ?>'/></td>
			<td style='color:blue;'>
				<a href="<?php echo U('addNode',array('pid'=>$v['id'],'level'=>2));?>">(添加控制器)</a>
				<a href="<?php echo U('updateNode',array('id'=>$v['id'],'pid'=>$v['pid'],'level'=>$v['level']));?>">修改</a>
				<a  onclick='return confirm("确定删除?");' href="<?php echo U('delNode',array('node_id'=>$v['id']));?>">删除</a>
			</td>
		
		</tr>
		

		<?php if(is_array($v["node"])): foreach($v["node"] as $key=>$data): ?><tr>
				<td style='color:red;'><?php echo ($data["id"]); ?></td>
				<td style='color:red;'>&nbsp;-控制器</td>
				<td style='color:red;'><?php echo ($data["name"]); ?>[<?php echo ($data["remark"]); ?>管理]</td>
				<td style='color:red;'>无</td>
				<td style='color:red;'><?php echo ($data["cat"]); ?></td>
				<td style='color:red;'><input class='sort length1' type='text' name='<?php echo ($data["id"]); ?>' value='<?php echo ($data["sort"]); ?>'/></td>
				<td style='color:red;'>
					<a href="<?php echo U('addNode',array('pid'=>$data['id'],'level'=>3));?>">(添加方法)</a>
					<a href="<?php echo U('updateNode',array('id'=>$data['id'],'pid'=>$data['pid'],'level'=>$data['level']));?>">修改</a>
					<a onclick='return confirm("确定删除?");' href="<?php echo U('delNode',array('node_id'=>$data['id']));?>">删除</a>					
				</td>
			</tr>
			<?php if(is_array($data["node"])): foreach($data["node"] as $key=>$values): ?><tr>
					<td ><?php echo ($values["id"]); ?></td>
					<td >&nbsp;&nbsp;&nbsp;&nbsp;---方法</td>
					<td><?php echo ($values["name"]); ?>[<?php echo ($values["remark"]); ?>]</td>
					<td ><?php echo ($values["title"]); ?></td>
					<td ><?php echo ($values["cat"]); ?></td>
					<td ><input type='text' class='sort length1' name='<?php echo ($values["id"]); ?>' value='<?php echo ($values["sort"]); ?>'/></td>
					<td >
						<a href="<?php echo U('updateNode',array('id'=>$values['id'],'pid'=>$values['pid'],'level'=>$values['level']));?>">修改</a>
						<a onclick='return confirm("确定删除?");' href="<?php echo U('delNode',array('node_id'=>$values['id']));?>">删除</a>					
					</td>
				</tr><?php endforeach; endif; endforeach; endif; endforeach; endif; ?>
		
	<tfoot>
            <tr class='active'>
            	<td colspan='1'>
            		<input type='submit' name='submit' class='submit' value='排序'/>
            	</td>
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