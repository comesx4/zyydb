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
$page['title'] = getlocal("page.preview.title");
$page['menuid'] = "settings";

function tpl_content() { global $page, $webimroot;
?>

<?php echo getlocal("page.preview.intro") ?>
<br />
<br />

<form name="preview" method="get" action="<?php echo $webimroot ?>/operator/themes.php">
	<div>
<?php if($page['tabs']) { ?>
	<ul class="tabs">

<?php foreach($page['tabs'] as $k => $v) { if($v) { ?>
	<li><a href="<?php echo $v ?>"><?php echo $k ?></a></li>
<?php } else { ?>
	<li class="active"><a href="#"><?php echo $k ?></a></li><?php }} ?>
	</ul>
<?php } ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal("page.preview.choose") ?></div>
			<div class="fvaluenodesc">
				<select name="preview" onchange="this.form.submit();"><?php foreach($page['availablePreviews'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("preview") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
			</div>
		</div>
		<div class="field">
			<div class="flabel"><?php echo getlocal("page.preview.choosetpl") ?></div>
			<div class="fvaluenodesc">
				<select name="template" onchange="this.form.submit();"><?php foreach($page['availableTemplates'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("template") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
			</div>
		</div>
<?php if( $page['canshowerrors'] ) { ?>
		<div class="field">
			<div class="flabel"><?php echo getlocal("page.preview.showerr") ?></div>
			<div class="fvaluenodesc">
				<input type="checkbox" name="showerr" value="on"<?php echo form_value_cb('showerr') ? " checked=\"checked\"" : "" ?> onchange="this.form.submit();"/>
			</div>
		</div>
<?php } ?>
<?php foreach( $page['previewList'] as $pp ) { ?>
		<div class="field">
			<div class="flabel">
			<?php echo htmlspecialchars($pp['label']) ?>
			<a href="<?php echo $page['showlink'] ?><?php echo $pp['id'] ?>" target="_blank" title="in separate window" onclick="this.newWindow = window.open('<?php echo $page['showlink'] ?><?php echo $pp['id'] ?>', '<?php echo $pp['id'] ?>', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=<?php echo $pp['w'] ?>,height=<?php echo $pp['h'] ?>,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;">link</a>
			</div>
			<div class="fvalueframe">
			<iframe id="sample<?php echo $pp['id'] ?>" width="<?php echo $pp['w'] ?>" height="<?php echo $pp['h'] ?>" src="<?php echo $page['showlink'] ?><?php echo $pp['id'] ?>" frameborder="0" scrolling="no">
				No iframes
			</iframe>
			</div>
		</div>
<?php } ?>
	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>		
</form>

<?php 
} /* content */

require_once('inc_main.php');
?>
