<?php

/**
 * 在线用户列表视图
 */
Class UserViewModel extends ViewModel {

    Protected $viewFields = array(
        'chatthread' => array(
            'threadid', 'userName',
            'userid', 'agentName',
            'agentId', 'dtmcreated',
            'dtmmodified', 'lrevision',
            'istate', 'ltoken', 'remote', 'referer', 'nextagent', 'locale', 'lastpinguser', 'lastpingagent',
            'userTyping', 'agentTyping', 'shownmessageid', 'userAgent', 'messageCount', 'groupid',
            '_type' => 'LEFT'
        ),
        'chatgroup' => array(
            'vclocalname' => 'groupname',
            '_on' => ' chatgroup.groupid= chatthread.groupid '
        )
    );
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'threadid DESC',
        ),
    );

}

?>
