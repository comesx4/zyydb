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

$operator = check_login();

if( isset($_GET['act']) && $_GET['act'] == 'del' ) {
	$operatorid = isset($_GET['id']) ? $_GET['id'] : "";

	if( !preg_match( "/^\d+$/", $operatorid )) {
		$errors[] = "Cannot delete: wrong argument";
	}

	if( !is_capable($can_administrate, $operator)) {
		$errors[] = "You are not allowed to remove operators";
	}
	
	if( $operatorid == $operator['operatorid']) {
		$errors[] = "Cannot remove self";
	}

	if(count($errors) == 0) {
		$op = operator_by_id($operatorid);
		if( !$op ) {
			$errors[] = getlocal("no_such_operator");
		} else if($op['vclogin'] == 'admin') {
			$errors[] = 'Cannot remove operator "admin"';			
		}		
	}
	
	if( count($errors) == 0 ) {
		$link = connect();
		perform_query("delete from chatgroupoperator where operatorid = $operatorid",$link);
		perform_query("delete from chatoperator where operatorid = $operatorid",$link);
		mysql_close($link);
		
		header("Location: $webimroot/operator/operators.php");
		exit;
	}
}

function is_online($operator) {
	global $settings;
	return $operator['time'] < $settings['online_timeout'] ? "1" : "";	
}

function get_operators() {
	$link = connect();

	$query = "select operatorid, vclogin, vclocalename, vccommonname, (unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time ".
			 "from chatoperator order by vclogin";
	$operators = select_multi_assoc($query, $link);
	mysql_close($link);
	return $operators;
}

$page = array();
$page['allowedAgents'] = get_operators();
$page['canmodify'] = is_capable($can_administrate, $operator);

setlocale(LC_TIME, getstring("time.locale"));

prepare_menu($operator);
start_html_output();
require('../view/agents.php');
?>