<?php
 //将 /weibo__PUBLIC__替换成__PUBLIC__

  function demo($path='./Tpl'){
  	
  	$dir=opendir($path);
	readdir($dir);
	readdir($dir);
	while($file=readdir($dir)){
		$filename=$path."/".$file;

		//如果是目录就再次打开
		if(is_dir($filename)){
		     demo($filename);
		}
        //如果是文件
		if(is_file($filename)){
			$file=file_get_contents($filename);
			$file=str_replace('/aliyun/__PUBLIC__','__PUBLIC__',$file);
			$file=str_replace('/aliyun/Uploads','__ROOT__/Uploads',$file);
			$file=str_replace('/aliyun/Public','__PUBLIC__',$file);
			$file=str_replace('/aliyun__ROOT__','__ROOT__',$file);
			 file_put_contents($filename,$file);

		}


		
		}
	closedir($dir);     
  }
  demo();