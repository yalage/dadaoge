<?php exit;
elseif (true === filter_keyword($username, 'username', $error)) {
    // 过滤用户名中的关键词
    $err = lang('well_shield_contain_keyword') . $error;
    return false;
}
?>