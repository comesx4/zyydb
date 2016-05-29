<?php
  Class HotWidget extends Widget{
	  public function render($data){		 
		  $data['group']=M('Group')->where(array('uid'=>$_SESSION['id']))->select();
		    
    return $this->renderFile('',$data);
     
     
     }
  
  
  
  
  
  
  
  
  
  
  }
