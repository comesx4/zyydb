<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <title>操作成功</title>
   <script type="text/javascript" src="__PUBLIC__/Js/jquery-1.8.2.min.js"></script>
    <script type="text/javascript">
    	$(function(){
    		var i = 2;
    		setInterval(function(){
    			i--;
    			$('#time').html(i);
    			if (i<=0) {
    				window.location='<?php echo ($jumpUrl); ?>';
    			}
    		},1000);

    		$('#redir').click(function(){
    				window.location='<?php echo ($jumpUrl); ?>';
    		});
    	});
    </script>
    <style type="text/css">
		*{
			margin:0;
			padding: 0;
		}
		body{
			background: #fff;
			font-size: 13px;
		}
		a{
			color: #458FD2;
			text-decoration: none;
			margin:0 0.2em;
			cursor: pointer;
		}
		.success{
			color: #000;

		}
		span#time{
			color: red;
			margin:0 0.3em;
		}
		#main{
			overflow: hidden;
			background: url('/Public/Images/bj1.jpg') no-repeat -20px center;
			width: 500px;
			margin:0 auto;
			padding-top: 100px;
			padding-left: 100px;
			height:200px;

		}
		#main .image{
			float: left;
			margin-right: 20px;
		}
		#main .image img{
			
			width: 90%;
		}	
		#main dl{
			
			
		}
		#main dl dt{
			margin-bottom: 1em;
			font-weight: 700;
			font-size: 23px;
			color: #000;
		}
		#main dl dd{
			font-size: 1.1em;
			margin-bottom: 0.5em;
		}
		

    </style>

</head>
<body>
	
   <div id='main'>
   		<div class='image'>
   			<img src="__ROOT__/Public/Images/yes.png">
   		</div>

	    <dl>
	    	<dt><span class='success'><?php echo ($message); ?></span></dt>	    	
	    	<dd>页面将在<span id='time'>2</span>秒后跳转!</dd>
	    	<dd>如果页面没有跳转，单击<a id='redir'>这里</a>返回</dd>
	    </dl>
    </div>
</body>
</html>