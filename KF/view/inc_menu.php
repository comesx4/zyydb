<?php 

//右侧菜单
function menuli($name) {
	global $page;
	if(isset($page) && isset($page['menuid']) && $name == $page['menuid']) {
		echo " class=\"active\"";
	}
	return "";
}

function tpl_menu() { global $page, $webimroot, $errors;
	if(isset($page['operator'])) { ?>
			<li>
				<h2><?php echo getlocal('right.main') ?></h2>
				<ul class="submenu">
					<li<?php menuli("main")?>><a href='<?php echo $webimroot ?>/operator/index.php'><?php echo getlocal('topMenu.main') ?></a></li>
					<li<?php menuli("users")?>><a href='<?php echo $webimroot ?>/operator/users.php'><?php echo getlocal('topMenu.users') ?></a> <span class="small">(<a class="inner" href='<?php echo $webimroot ?>/operator/users.php?nomenu'><?php echo getlocal('topMenu.users.nomenu') ?></a>)</span></li>
					<li<?php menuli("history")?>><a href='<?php echo $webimroot ?>/operator/history.php'><?php echo getlocal('page_analysis.search.title') ?></a></li>
<?php if(isset($page['showstat']) && $page['showstat']) { ?>
					<li<?php menuli("statistics")?>><a href='<?php echo $webimroot ?>/operator/statistics.php'><?php echo getlocal('statistics.title') ?></a></li>
<?php } ?>
<?php if(isset($page['showban']) && $page['showban']) { ?>
					<li<?php menuli("blocked")?>><a href='<?php echo $webimroot ?>/operator/blocked.php'><?php echo getlocal('menu.blocked') ?></a></li>
<?php } ?>
				</ul>
			</li>
			<li>
				<h2><?php echo getlocal('right.administration') ?></h2>
				<ul class="submenu">
					<li<?php menuli("canned")?>><a href='<?php echo $webimroot ?>/operator/canned.php'><?php echo getlocal('menu.canned') ?></a></li>
<?php if(isset($page['showadmin']) && $page['showadmin']) { ?>
					<li<?php menuli("getcode")?>><a href='<?php echo $webimroot ?>/operator/getcode.php'><?php echo getlocal('leftMenu.client_gen_button') ?></a></li>
					<li<?php menuli("operators")?>><a href='<?php echo $webimroot ?>/operator/operators.php'><?php echo getlocal('leftMenu.client_agents') ?></a></li>
<?php if(isset($page['showgroups']) && $page['showgroups']) { ?>
					<li<?php menuli("groups")?>><a href='<?php echo $webimroot ?>/operator/groups.php'><?php echo getlocal('menu.groups') ?></a></li>
<?php } ?>
					<li<?php menuli("settings")?>><a href='<?php echo $webimroot ?>/operator/settings.php'><?php echo getlocal('leftMenu.client_settings') ?></a></li>
					<li<?php menuli("translate")?>><a href='<?php echo $webimroot ?>/operator/translate.php'><?php echo getlocal('menu.translate') ?></a></li>
					<li<?php menuli("updates")?>><a href='<?php echo $webimroot ?>/operator/updates.php'><?php echo getlocal('menu.updates') ?></a></li>
<?php } ?>
<?php if(isset($page['currentopid']) && $page['currentopid']) {?>
					<li<?php menuli("profile")?>><a href='<?php echo $webimroot ?>/operator/operator.php?op=<?php echo $page['currentopid'] ?>'><?php echo getlocal('menu.profile') ?></a></li>
<?php } ?>
				</ul>
			</li>
			<li>
				<h2><?php echo getlocal('right.other') ?></h2>
				<ul class="submenu">
									<li><a href='http://web.dns-w.cn'>HELP</a></li>
					<li><a href='<?php echo $webimroot ?>/operator/logout.php'><?php echo getlocal('topMenu.logoff') ?></a></li>
				</ul>
			</li>
<?php 
	} 
}
?>
