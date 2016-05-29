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

    <script type="text/javascript" src='__PUBLIC__/Js/user_index.js'></script>
    <script type="text/javascript">
     var userLock='<?php echo U("Member/memberLock");?>';
    </script>

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
               <dd><select name='searchtype'>
                       <option <?php if($tmp["searchtype"] == 0): ?>selected<?php endif; ?> value='0'>邮箱账户</option>
                       <option <?php if($tmp["searchtype"] == 1): ?>selected<?php endif; ?> value='1'>用户昵称</option>
                       <option <?php if($tmp["searchtype"] == 2): ?>selected<?php endif; ?> value='2'>真实姓名</option>
                   </select></dd>
                <dd>
                    <input style='width:100px;' placeholder='' type='text' value='<?php echo ($tmp["username"]); ?>' name='username' />
                </dd>
            </dl>
            <dl>
                <dt>最后登录时间：</dt>
                <dd>
                    <input id="min_date" placeholder='开始时间' value='<?php echo ($tmp["min-date"]); ?>' class="laydate-icon length2" type='text' name='min-date' />
                    -<input id='max_date' placeholder='结束' class="laydate-icon length2" value='<?php echo ($tmp["max-date"]); ?>' type='text' name='max-date' />
                </dd>
            </dl> 
        </div>

        <div class='search-bottom'>
            <p style='float:right;margin-right:10px;'>
                <input type='reset' name='clear' class='btn btn-danger' value='清空' />                
               <!-- <a class='btn btn-danger' href="<?php echo U();?>">清空</a> -->
               <input type='submit' name='search' class='btn btn-primary' value='搜索' />
            </p>
        </div>
    </div>
</form>

	<table class="table table-condensed table-hover " >

		<tr>
			<th>ID</th>
			<th>邮箱账户</th>
                        <th>用户昵称</th>
                        <th>真实姓名</th>
			<th>上次登录时间</th>
			<th>上次登录IP</th>
			<th>状态</th>
			<th>注册时间</th>
			<th>操作</th>
		</tr>
	<?php if(is_array($Member)): foreach($Member as $key=>$v): ?><tr>
			<td><?php echo ($v["id"]); ?></td>
			<td><?php echo ($v["username"]); ?></td>
                        <td><?php echo ($v["uname"]); ?></td>
                        <td><?php echo ($v["trueName"]); ?></td>
			<td><?php echo (date('Y-m-d H:i',$v["logintime"])); ?></td>
                        <td><?php echo ($v['loginip']); ?></td>
			<td class='static'><?php if($v["lock"] == 1 ): ?>锁定<?php else: ?>正常<?php endif; ?></td>
			<td><?php echo (date('Y-m-d H:i',$v['register'])); ?> </td>
			<td>
				<?php if($v["lock"] == 1 ): ?><span style='cursor:pointer;' uid='<?php echo ($v["id"]); ?>' class='jie'>[解锁]</span>
				<?php else: ?>
					<span style='cursor:pointer;' uid='<?php echo ($v["id"]); ?>' class='suo'>[锁定]</span><?php endif; ?>
                        <a href="<?php echo U('mdetail',array('id'=>$v['id']));?>" target="_blank">[详细资料]</a>				
			</td>
		</tr>
	</if><?php endforeach; endif; ?>
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