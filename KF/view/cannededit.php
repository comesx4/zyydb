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

$page['title'] = $page['key'] ? getlocal("cannededit.title") : getlocal("cannednew.title");

function tpl_content() { global $page, $webimroot, $errors;
?>

	<?php if( $page['saved'] ) { ?>
	<?php echo getlocal("cannededit.done") ?>

	<script><!--
		if(window.opener && window.opener.location) {
			window.opener.location.reload();
		} 
		setTimeout( (function() { window.close(); }), 500 );
	//--></script>
<?php } ?>
<?php if( !$page['saved'] ) { ?>

<?php echo $page['key'] ? getlocal("cannededit.descr") : getlocal("cannednew.descr") ?>
<br/>
<br/>
<?php 
require_once('inc_errors.php');
?>

<form name="cannedForm" method="post" action="<?php echo $webimroot ?>/operator/cannededit.php">
<input type="hidden" name="key" value="<?php echo $page['key'] ?>"/>
<?php if(!$page['key']) { ?>
<input type="hidden" name="lang" value="<?php echo $page['locale'] ?>"/>
<input type="hidden" name="group" value="<?php echo $page['groupid'] ?>"/>
<?php } ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal("cannededit.message") ?></div>
			<div class="fvaluenodesc">
				<textarea name="message" cols="20" rows="5" class="wide"><?php echo form_value('message') ?></textarea>
			</div>
		</div>
	
		<div class="fbutton">
			<input type="image" name="save" value="" src='<?php echo $webimroot.getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
		</div>
	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</form>

<?php } ?>

<?php 
} /* content */

require_once('inc_main.php');
?>