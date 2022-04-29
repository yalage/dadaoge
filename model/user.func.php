<?php

// 只能在当前 request 生命周期缓存，要跨进程，可以再加一层缓存： memcached/xcache/apc/
$g_static_users = array(); // 变量缓存

// hook model_user_start.php

// ------------> 最原生的 CURD，无关联其他数据。

function user__create($arr, $d = NULL)
{
    // hook model_user__create_start.php
    $r = db_insert('user', $arr, $d);
    // hook model_user__create_end.php
    return $r;
}

function user__update($uid, $update, $d = NULL)
{
    // hook model_user__update_start.php
    $r = db_update('user', array('uid' => $uid), $update, $d);
    // hook model_user__update_end.php
    return $r;
}

function user__read($cond = array(), $orderby = array(), $col = array(), $d = NULL)
{
    // hook model_user__read_start.php
    $user = db_find_one('user', $cond, $orderby, $col, $d);
    // hook model_user__read_end.php
    return $user;
}

function user__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'uid', $col = array(), $d = NULL)
{
    // hook model_user__find_start.php
    $arr = db_find('user', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_user__find_end.php
    return $arr;
}

function user__delete($uid, $d = NULL)
{
    // hook model_user__delete_start.php
    $r = db_delete('user', array('uid' => $uid), $d);
    // hook model_user__delete_end.php
    return $r;
}

function user_count($cond = array(), $d = NULL)
{
    // hook model_user_count_start.php
    $n = db_count('user', $cond, $d);
    // hook model_user_count_end.php
    return $n;
}

function user_big_insert($arr = array(), $d = NULL)
{
    // hook model_user_big_insert_start.php
    $r = db_big_insert('user', $arr, $d);
    // hook model_user_big_insert_end.php
    return $r;
}

function user_big_update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_user_big_update_start.php
    $r = db_big_update('user', $cond, $update, $d);
    // hook model_user_big_update_end.php
    return $r;
}

// ------------> 关联 CURD，主要是强相关的数据，比如缓存。弱相关的大量数据需要另外处理。

function user_create($arr)
{
    global $conf;
    // hook model_user_create_start.php
    $r = user__create($arr);
    // 全站统计
    runtime_set('users+', 1);
    runtime_set('todayusers+', 1);
    // hook model_user_create_end.php
    return $r;
}

function user_update($uid, $arr)
{
    global $conf, $g_static_users;
    // hook model_user_update_start.php
    if (empty($uid)) return FALSE;
    $r = user__update($uid, $arr);
    'mysql' != $conf['cache']['type'] and cache_delete('user-' . $uid);
    isset($g_static_users[$uid]) and $g_static_users[$uid] = array_merge($g_static_users[$uid], $arr);
    // hook model_user_update_end.php
    return $r;
}

function user_read($uid)
{
    global $g_static_users;
    $uid = intval($uid);
    if (empty($uid)) return array();
    if (isset($g_static_users[$uid])) return $g_static_users[$uid];
    // hook model_user_read_start.php
    $user = user__read(array('uid' => $uid));
    if ($user) {
        user_format($user);
        $g_static_users[$user['uid']] = $user;
    }
    // hook model_user_read_end.php
    return $user;
}

// 从缓存中读取，避免重复从数据库取数据，主要用来前端显示，可能有延迟。重要业务逻辑不要调用此函数，数据可能不准确，因为并没有清理缓存，针对 request 生命周期有效。
function user_read_cache($uid)
{
    global $conf, $g_static_users;
    if (isset($g_static_users[$uid])) return $g_static_users[$uid];
    // hook model_user_read_cache_start.php
    // 游客
    if (0 == $uid) return user_guest();
    if ('mysql' == $conf['cache']['type']) {
        $r = user_read($uid);
    } else {
        $r = cache_get('user-' . $uid);
        if (NULL === $r) {
            $r = user_read($uid);
            $r and cache_set('user-' . $uid, $r, 7200);
        }
    }
    $g_static_users[$uid] = $r ? $r : user_guest();
    // hook model_user_read_cache_end.php
    return $g_static_users[$uid];
}

function user_delete($uid)
{
    global $conf, $g_static_users;
    // hook model_user_delete_start.php
    $user = user_read($uid);
    if (empty($user)) return FALSE;
    // hook model_user_delete_before.php
    well_attach_delete_by_uid($uid);
    // 删除头像
    $user['avatar_path'] and xn_unlink($user['avatar_path']);
    $r = user__delete($uid);
    // hook model_user_delete_center.php
    'mysql' == $conf['cache']['type'] || cache_delete('user-' . $uid);
    if (isset($g_static_users[$uid])) unset($g_static_users[$uid]);
    well_thread_delete_all_by_uid($uid);
    // hook model_user_delete_after.php
    // 全站统计
    runtime_set('users-', 1);
    // hook model_user_delete_end.php
    return $r;
}

function user_find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20)
{
    global $g_static_users;
    // hook model_user_find_start.php
    $userlist = user__find($cond, $orderby, $page, $pagesize);
    if (!$userlist) return NULL;

    foreach ($userlist as &$user) {
        user_format($user);
        $g_static_users[$user['uid']] = $user;
    }
    // hook model_user_find_end.php
    return $userlist;
}

// ------------> 其他方法

function user_read_by_email($email)
{
    global $g_static_users;
    // hook model_user_read_by_email_start.php
    $user = user__read(array('email' => $email));
    if ($user) {
        user_format($user);
        $g_static_users[$user['uid']] = $user;
    }
    // hook model_user_read_by_email_end.php
    return $user;
}

function user_read_by_username($username)
{
    global $g_static_users;
    // hook model_user_read_by_username_start.php
    $user = user__read(array('username' => $username));
    if ($user) {
        user_format($user);
        $g_static_users[$user['uid']] = $user;
    }
    // hook model_user_read_by_username_end.php
    return $user;
}

function user_maxid()
{
    // hook model_user_maxid_start.php
    $n = db_maxid('user', 'uid');
    // hook model_user_maxid_end.php
    return $n;
}

function user_format(&$user)
{
    $conf = _SERVER('conf');
    if (empty($user)) return;

    // hook model_user_format_start.php

    $user['create_ip_fmt'] = safe_long2ip($user['create_ip']);
    $user['create_date_fmt'] = empty($user['create_date']) ? '0000-00-00' : date('Y-m-d', $user['create_date']);
    $user['login_ip_fmt'] = safe_long2ip($user['login_ip']);
    $user['login_date_fmt'] = empty($user['login_date']) ? '0000-00-00' : date('Y-m-d', $user['login_date']);

    $user['groupname'] = group_name($user['gid']);

    $dir = substr(sprintf("%09d", $user['uid']), 0, 3);
    // hook model_user_format_avatar_url_before.php
    $user['avatar_url'] = $user['avatar'] ? file_path() . "avatar/$dir/$user[uid].png?" . $user['avatar'] : view_path() . 'img/avatar.png';
    $user['avatar_path'] = $user['avatar'] ? $conf['upload_path'] . "avatar/$dir/$user[uid].png?" . $user['avatar'] : '';

    $onlinelist = online_user_list_cache();
    $user['online_status'] = isset($onlinelist[$user['uid']]) ? 1 : 0;
    $user['url'] = url('user-' . $user['uid']);
    // hook model_user_format_end.php
}

function user_guest()
{
    $conf = _SERVER('conf');
    static $guest = NULL;
    // hook model_user_guest_start.php

    if ($guest) return $guest; // 返回引用，节省内存。
    $guest = array(
        'uid' => 0,
        'gid' => 0,
        'groupname' => lang('guest_group'),
        'username' => lang('guest'),
        'avatar_url' => view_path() . 'img/avatar.png',
        'create_ip_fmt' => '',
        'create_date_fmt' => '',
        'login_date_fmt' => '',
        'email' => '',
        'articles' => 0,
        'comments' => 0,
    );

    // hook model_user_guest_end.php
    return $guest; // 防止内存拷贝
}

// 根据积分来调整用户组
function user_update_group($uid)
{
    global $conf, $grouplist;
    if (empty($uid)) return FALSE;
    $user = user_read_cache($uid);
    if ($user['gid'] < 100) return FALSE;
    // hook model_user_update_group_start.php
    // 遍历 credits 范围，调整用户组
    foreach ($grouplist as $group) {
        if ($group['gid'] < 100) continue;
        // 根据发文章和评论 需要时 hook
        //$n = $user['articles'] + $user['comments'];

        // hook model_user_update_group_policy_start.php

        /*if ($n > $group['creditsfrom'] && $n < $group['creditsto']) {
            if ($user['gid'] != $group['gid']) {
                user_update($uid, array('gid' => $group['gid']));
                return TRUE;
            }
        }*/
    }
    // hook model_user_update_group_end.php
    return FALSE;
}

// uids: 1,2,3,4 -> array()
function user_find_by_uids($uids)
{
    // hook model_user_find_by_uids_start.php
    $uids = trim($uids);
    if (empty($uids)) return array();
    $arr = explode(',', $uids);
    $r = array();
    foreach ($arr as $_uid) {
        $user = user_read_cache($_uid);
        if (empty($user)) continue;
        $r[$user['uid']] = $user;
    }
    // hook model_user_find_by_uids_end.php
    return $r;
}

// 获取用户安全信息
function user_safe_info($user)
{
    // hook model_user_safe_info_start.php
    unset($user['password'], $user['credits'], $user['golds'], $user['money'], $user['email'], $user['salt'], $user['password_sms'], $user['idnumber'], $user['realname'], $user['qq'], $user['mobile'], $user['create_ip'], $user['create_ip_fmt'], $user['create_date'], $user['create_date_fmt'], $user['login_ip'], $user['login_date'], $user['login_ip_fmt'], $user['login_date_fmt'], $user['logins'], $user['avatar_path']);
    // hook model_user_safe_info_end.php
    return $user;
}

function user_token_get()
{
    global $conf, $time;
    // hook model_user_token_get_start.php
    $_uid = user_token_get_do();
    // hook model_user_token_get_before.php
    empty($_uid) and user_token_clear(); // 退出登录
    // hook model_user_token_get_end.php
    return $_uid;
}

// 支持 Token 接口（token 与 session 双重登入机制，方便 REST 接口设计，也方便 $_SESSION 使用）
// Support Token interface (token and session dual match, to facilitate the design of the REST interface, but also to facilitate the use of $_SESSION)
function user_rest()
{
    $uid = intval(_SESSION('uid'));
    empty($uid) and $uid = user_token_get() and $_SESSION['uid'] = $uid;
    $user = user_read($uid);
    return $user;
}

// 用户
function user_token_get_do()
{
    global $conf, $time, $ip, $useragent;
    $token = param($conf['cookie_pre'] . 'token');
    // hook model_user_token_get_do_start.php
    if (empty($token)) return FALSE;
    $tokenkey = md5(xn_key());
    $s = xn_decrypt($token, $tokenkey);
    if (empty($s)) return FALSE;
    $arr = explode("\t", $s);
    if (count($arr) != 5) return FALSE;
    list($_ip, $_time, $_uid, $_pwd, $ua_md5) = $arr;
    if (array_value($conf, 'login_ip') && $ip != $_ip) return FALSE;
    if (array_value($conf, 'login_ua') && md5($useragent) != $ua_md5) return FALSE;
    $_user = user_read($_uid);
    if (empty($_user)) return FALSE;
    if (array_value($conf, 'login_only') && $_user['login_date'] != $_time) return FALSE;
    // 密码是否被修改
    if (md5($_user['password']) != $_pwd) return FALSE;
    // hook model_user_token_get_do_end.php
    return $_uid;
}

// 设置 token，防止 sid 过期后被删除
function user_token_set($uid)
{
    global $conf, $time;
    if (empty($uid)) return;
    $token = user_token_gen($uid);
    setcookie($conf['cookie_pre'] . 'token', $token, $time + 8640000, $conf['cookie_path'], $conf['cookie_domain'], '', TRUE);
    // hook model_user_token_set_end.php
}

function user_token_clear()
{
    global $conf, $time;
    setcookie($conf['cookie_pre'] . 'token', '', $time - 8640000, $conf['cookie_path'], $conf['cookie_domain'], '', TRUE);
    // hook model_user_token_clear_end.php
}

function user_token_gen($uid)
{
    global $conf, $time, $ip, $useragent;
    // hook model_user_token_gen_start.php
    $user = user_read($uid);
    $pwd = md5($user['password']);
    $ua_md5 = md5($useragent);
    $tokenkey = md5(xn_key());
    $token = xn_encrypt("$ip	$time	$uid	$pwd	$ua_md5", $tokenkey);
    // hook model_user_token_gen_end.php
    return $token;
}

// 前台登录验证
function user_login_check()
{
    global $user;
    // hook model_user_login_check_start.php
    empty($user) and http_location(url('user-login'));
    // hook model_user_login_check_end.php
}

// 获取用户来路
function user_http_referer()
{
    global $conf;
    // hook user_http_referer_start.php
    $referer = param('referer'); // 优先从参数获取 | GET is priority
    empty($referer) and $referer = array_value($_SERVER, 'HTTP_REFERER', '');
    $referer = str_replace(array('\"', '"', '<', '>', ' ', '*', "\t", "\r", "\n"), '', $referer); // 干掉特殊字符 strip special chars
    if (
        !preg_match('#^(http|https)://[\w\-=/\.]+/[\w\-=.%\#?]*$#is', $referer)
        || FALSE !== strpos($referer, url('user-login'))
        || FALSE !== strpos($referer, url('user-logout'))
        || FALSE !== strpos($referer, url('user-create'))
        || FALSE !== strpos($referer, url('user-setpw'))
        || FALSE !== strpos($referer, url('user-resetpw_complete'))
    ) {
        $referer = $conf['path'];
    }
    // hook user_http_referer_end.php
    return $referer;
}

function user_auth_check($token)
{
    global $time, $ip;
    // hook user_auth_check_start.php
    $auth = param(2);
    $s = xn_decrypt($auth);
    empty($s) and message(-1, lang('decrypt_failed'));
    $arr = explode('-', $s);
    count($arr) != 4 and message(-1, lang('encrypt_failed'));
    list($_ip, $_time, $_uid, $_pwd) = $arr;
    $_user = user_read($_uid);
    empty($_user) and message(-1, lang('user_not_exists'));
    $time - $_time > 3600 and message(-1, lang('link_has_expired'));
    // hook user_auth_check_end.php
    return $_user;
}

// hook model_user_end.php

?>