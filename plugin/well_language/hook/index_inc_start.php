<?php exit;
$accept_language = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
if (FALSE !== strpos($accept_language,'zh-cn')) {
    $conf['lang'] = 'zh-cn';
} elseif (FALSE !== strpos($accept_language,'zh-tw')) {
    $conf['lang'] = 'zh-cn';
} else {
    $conf['lang'] = 'en-us';
}
?>