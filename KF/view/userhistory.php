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

$page['title'] = getlocal("page.analysis.userhistory.title");
$page['menuid'] = "history";

function tpl_content() { global $page, $webimroot;
?>

<?php echo getlocal("page.analysis.userhistory.intro") ?>
<br />
<br />

<?php if( $page['pagination'] ) { ?>

<table class="list">
<thead>
<tr class="header">
<th>
	<?php echo getlocal("page.analysis.search.head_name") ?>
</th><th>
	<?php echo getlocal("page.analysis.search.head_host") ?>
</th><th>
	<?php echo getlocal("page.analysis.search.head_operator") ?>
</th><th>
	<?php echo getlocal("page.analysis.search.head_time") ?>
</th></tr>
</thead>
<tbody>
<?php 
if( $page['pagination.items'] ) {
	foreach( $page['pagination.items'] as $chatthread ) { ?>
	<tr>
		<td>
			<a href="<?php echo $webimroot ?>/operator/threadprocessor.php?threadid=<?php echo $chatthread['threadid'] ?>" target="_blank" onclick="this.newWindow = window.open('<?php echo $webimroot ?>/operator/threadprocessor.php?threadid=<?php echo $chatthread['threadid'] ?>', '', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=720,height=520,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><?php echo topage(htmlspecialchars($chatthread['userName'])) ?></a>
		</td>
		<td>
        	<?php echo get_user_addr(topage($chatthread['remote'])) ?>
		</td>
		<td>
        	<?php if( $chatthread['agentName'] ) { ?><?php echo topage(htmlspecialchars($chatthread['agentName'])) ?><?php } ?>
		</td>
		<td>
			<?php echo date_diff_to_text($chatthread['modified']-$chatthread['created']) ?>, <?php echo date_to_text($chatthread['created']) ?>
		</td>
	</tr>
<?php
	} 
} else {
?>
	<tr>
	<td colspan="5">
		<?php echo getlocal("tag.pagination.no_items") ?>
	</td>
	</tr>
<?php 
} 
?>
</tbody>
</table>
<?php
	if( $page['pagination.items'] ) { 
		echo "<br/>";
		echo generate_pagination($page['pagination']);
	}
} 
?>

<?php 
} /* content */

require_once('inc_main.php');
?>