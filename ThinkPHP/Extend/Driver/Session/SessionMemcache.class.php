<?php
Class SessionMemcache{
	private $mem;
	private $time;
	public function execute(){
		//session_start();
		   session_set_save_handler(
			  //如果是静态类就要用__class__而不能用&$this 
		 array(&$this,'open'),
		 array(&$this,'close'),
		 array(&$this,'read'),
		 array(&$this,'write'),
		 array(&$this,'destroy'),
		 array(&$this,'gc')
	 );
		 
	   
	   }
	   //在开启session时执行
	public  function open($sessionlujin,$sessionname){
		//如果配置文件设置的session的生存期限就用它，没有的话就设置为1140秒
		$this->time=!empty(C("SESSION_TIME"))?C("SESSION_TIME"):1140;
		  $this->mem= new memcache();
                 
                   return  $this->mem -> addserver("localhost",11211);
       }
       //所有操作完成后执行
       public  function close(){
	       return true;
       }
       //读取内容，在session_start（）时执行
       public  function read($sessionid){
	       $hehe = $this->mem ->get($sessionid);
	       if(empty($hehe)){
	       return "";
	       }
	       return $hehe;

       
       
       }
       //写入数据，在给session赋值时和脚本结束时执行
       public  function write($sessionid,$data){
	       //这里不用add是因为add不会覆盖数据，如果session更新了内容它也存不进去
	       $this->mem ->set($sessionid,$data,MEMCACHE_COMPRESSED,$this->time);
	       
	      return true;
	      
                   
       
       }
       //在运行	session_destroy();时执行
       public  function destroy($sessionid){
	       $this->mem ->delete($sessionid,0);

	    
       }
       //垃圾回收程序启动进行时执行
       public  function gc($ptime){
	       //因为存进memcache的时候就设置了生存时间，所以没必要用垃圾回收机制
              return true;
       }
   
   
   
   
   
   }
