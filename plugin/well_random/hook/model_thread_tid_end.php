<?php exit;
// 随机主题
$g_random = FALSE;
function thread_random_get($n = 20)
{
    global $conf, $config, $g_random;

    if ($config['setting']['random_on'] == 0 || empty($n)) return NULL;
    $g_random === FALSE AND $g_random = website_get('random');

    if (empty($g_random)) {
        $g_random = thread_tid_find(1, array_value($conf, 'random_n', 1000));
        $g_random = $g_random ? website_set('random', arrlist_values($g_random, 'tid')) : NULL;
    }
    if (empty($g_random)) {
        $tids = NULL;
    } else {
        $keys = array_flip($g_random); // tid翻转为key
        $n = count($keys) < $n ? count($keys) : $n;
        $tids = array_rand($keys, $n); // 随机返回tid
    }
    return $tids;
}

// 创建新主题加入随机
function thread_random_set($tid)
{
    global $conf, $config, $g_random;
    if ($config['setting']['random_on'] == 0) return NULL;
    $g_random === FALSE AND $g_random = website_get('random');
    empty($g_random) AND $g_random = array();
    count($g_random) >= array_value($conf, 'random_n', 1000) AND array_pop($g_random); // 尾出
    array_unshift($g_random, $tid); // 头入
    return website_set('random', $g_random);
}

// 清空
function thread_random_delete()
{
    website_set('random', '');
    return TRUE;
}

/*
function thread_random_delete_by_tid($tid)
{
    global $config, $g_random;
    if ($config['setting']['random_on'] == 0) return NULL;
    $g_random === FALSE AND $g_random = website_get('random');
    if (empty($g_random)) return NULL;
    $g_random = array_flip($g_random);
    unset($g_random[$tid]);
    $g_random = array_flip($g_random);
    $g_random = array_values($g_random);
    return website_set('random', $g_random);
}
*/

?>