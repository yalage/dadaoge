<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */
!defined('DEBUG') AND exit('Forbidden');

$sql = "ALTER TABLE {$db->tablepre}forum DROP `randoms`;";
$r = db_exec($sql);

$arr = setting_get('conf');
unset($arr['setting']['random_on']);
setting_set('conf', $arr);

website_delete('random');

forum_list_cache_delete();

?>