<?php
/*
 * Copyright (C) www.wellcms.cn
 */
!defined('DEBUG') AND exit('Forbidden');

$arr = setting_get('conf');
unset($arr['filter']);
setting_set('conf', $arr);

?>