<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */
!defined('DEBUG') AND exit('Forbidden');

//include _include(APP_PATH . 'model/db_check.func.php');

//if (!db_find_field($db->tablepre . 'forum', 'randoms')) {
    // 随机主题数
    $sql = "ALTER TABLE  `{$db->tablepre}forum` ADD  `randoms` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `todaythreads`";
    $r = db_exec($sql);
//}

$arr = setting_get('conf');
$arr['setting']['random_on'] = 1; // 随机主题
setting_set('conf', $arr);

$g_random = thread_tid_find(1, 1000);
website_set('random', arrlist_values($g_random, 'tid'));

forum_list_cache_delete();

?>