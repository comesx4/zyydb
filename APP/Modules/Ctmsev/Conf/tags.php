<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of tags
 *
 * @author 范利丰 <feiyufly001@hotmail.com>
 */
    return array(
        // 表示在app_begin标签位置执行多语言检测行为

//	'app_begin' => array('Behavior\CheckLangBehavior'),
        'app_begin' => array('CheckLang')
    );