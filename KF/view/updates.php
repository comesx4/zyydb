<?php
/*
 * This file is part of Mibew Messenger project.
 *
 * Copyright (c) 2005-2009 Mibew Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.skcms.net
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once("inc_menu.php");
$page['title'] = getlocal("updates.title");
$page['menuid'] = "updates";

function tpl_header() { global $page, $webimroot;
?>	
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/update.js"></script>
<?php
}

function tpl_content() { global $page, $webimroot;
?>

<?php echo getlocal("updates.intro") ?>
<br />
<br />
<div>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">
	
		<?php echo getlocal("updates.news")?><br/>
		<div ><script language='javascript' src='http://www.skcms.net/plus/JS.asp?LID=38&ClassID={$ClassID}&ModeID={$ModeID}&ID={$ID}'></script>
		</div>
		
		<?php echo getlocal("updates.current")?><br/>
			<div id="cver">www.skcms.net</div>
			
		<br/>

		<?php echo getlocal("updates.latest")?>
			<div id="lver"></div>
		
		<br/>
			
		<?php echo getlocal("updates.installed_locales")?><br/>
			<?php foreach( $page['localizations'] as $loc ) { ?>
				<?php echo $loc ?>
			<?php } ?>
		
		<br/><br/>	
		
		<?php echo getlocal("updates.env")?><br/>
			PHP <?php echo $page['phpVersion'] ?>

	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</div>		

<?php 
} /* content */

require_once('inc_main.php');
?>
