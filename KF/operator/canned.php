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

require_once('../libs/common.php');
require_once('../libs/operator.php');
require_once('../libs/settings.php');
require_once('../libs/groups.php');
require_once('../libs/pagination.php');

$operator = check_login();
loadsettings();

$errors = array();
$page = array();

function load_canned_messages($locale, $groupid) {
	$link = connect();
	$query = "select id, vcvalue from chatresponses ".
			 "where locale = '".$locale."' AND (".
			 ($groupid 
			 		? "groupid = $groupid" 
			 		: "groupid is NULL OR groupid = 0").
			 ") order by vcvalue";
			 		
	$result = select_multi_assoc($query, $link);
	if(!$groupid && count($result) == 0) {
		foreach(explode("\n", getstring_('chat.predefined_answers', $locale)) as $answer) {
			$result[] = array('id' => '', 'vcvalue' => $answer);
		}
		if(count($result) > 0) {
			$updatequery = "insert into chatresponses (vcvalue,locale,groupid) values ";
			for($i=0;$i<count($result);$i++) {
				if($i > 0) {
					$updatequery .= ", ";
				}
				$updatequery .= "('".mysql_real_escape_string($result[$i]['vcvalue'], $link)."','$locale', NULL)";
			}
			perform_query($updatequery, $link);
			$result = select_multi_assoc($query, $link);
		}
	}	
	mysql_close($link);
	return $result;
}

# locales

$all_locales = get_available_locales();
$locales_with_label = array();
foreach($all_locales as $id) {
	$locales_with_label[] = array('id' => $id, 'name' => getlocal_($id,"names"));
}
$page['locales'] = $locales_with_label;

$lang = verifyparam("lang", "/^[\w-]{2,5}$/", "");
if( !$lang || !in_array($lang,$all_locales) ) {
	$lang = in_array($current_locale,$all_locales) ? $current_locale : $all_locales[0];
}

# groups

$groupid = "";
if($settings['enablegroups'] == '1') {
	$groupid = verifyparam( "group", "/^\d{0,8}$/", "");
	if($groupid) {
		$group = group_by_id($groupid);
		if(!$group) {
			$errors[] = getlocal("page.group.no_such");
			$groupid = "";
		}
	}

	$allgroups = get_groups(false);
	$page['groups'] = array();
	$page['groups'][] = array('groupid' => '', 'vclocalname' => getlocal("page.gen_button.default_group"));
	foreach($allgroups as $g) {
		$page['groups'][] = $g;
	}
}  

# delete

if(isset($_GET['act']) && $_GET['act'] == 'delete') {
	$key = isset($_GET['key']) ? $_GET['key'] : "";

	if( !preg_match( "/^\d+$/", $key )) {
		$errors[] = "Wrong key";
	}

	if( count($errors) == 0 ) {
		$link = connect();
		perform_query("delete from chatresponses where id = $key",$link);
		mysql_close($link);
		header("Location: $webimroot/operator/canned.php?lang=$lang&group=$groupid");
		exit;
	}
}

# get messages

$messages = load_canned_messages($lang, $groupid);
setup_pagination($messages);

# form values

$page['formlang'] = $lang;
$page['formgroup'] = $groupid;

prepare_menu($operator);
start_html_output();
require('../view/canned.php');
?>