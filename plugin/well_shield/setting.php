<?php
/*
 * Copyright (C) www.wellcms.cn
 */
!defined('DEBUG') and exit('Access Denied.');

$arr = setting_get('conf');
$shield = array_value($arr, 'filter');

if ('GET' == $method) {

    $form_action = url('plugin-setting', array('dir' => 'well_shield'), TRUE);
    $input = array();
    $input['url'] = form_radio_yes_no('url', array_value($shield, 'url'));
    //$input['numeric'] = form_radio_yes_no('numeric', array_value($shield, 'numeric'));
    //$input['en'] = form_radio_yes_no('en', array_value($shield, 'en'));

    $_username = array_value($shield, 'username');
    $input['username_enable'] = form_radio_yes_no('username_enable', array_value($_username, 'enable'));
    $input['username_limit'] = form_radio_yes_no('username_limit', array_value($_username, 'limit'));
    $keyword = implode('|', array_value($_username, 'keyword'));
    $input['username_keyword'] = form_textarea('username_keyword', $keyword, '', '100%', 200);

    $_content = array_value($shield, 'content');
    $input['content_enable'] = form_radio_yes_no('content_enable', array_value($_content, 'enable'));
    $keyword = implode('|', array_value($_content, 'keyword'));
    $input['content_keyword'] = form_textarea('content_keyword', $keyword, '', '100%', 200);

    $input['ip_enable'] = form_radio_yes_no('ip_enable', array_value($shield, 'ip_enable'));
    $ips = array_value($shield, 'ips');
    if (!empty($ips)) {
        $ipstr = '';
        foreach ($ips as $_ip) {
            if ($_ip) {
                $ipstr = safe_long2ip($_ip) . "\r\n";
            }
        }
    } else {
        $ipstr = '';
    }
    $input['ips'] = form_textarea('ips', $ipstr, '', '100%', 200);

    $header['title'] = lang('well_shield_setting');
    $header['mobile_title'] = '';
    $header['mobile_link'] = '';
    $header['keywords'] = lang('well_shield_setting');
    $header['description'] = lang('well_shield_setting');

    include _include(APP_PATH . 'plugin/well_shield/view/htm/setting.htm');

} elseif ('POST' == $method) {

    $url = param('url', 0);
    $ip_enable = param('ip_enable', 0);
    $ipstr = param('ips');
    $ipsarr = array();
    if ($ipstr) {
        $iparr = explode("\r\n", $ipstr);
        $iparr = array_filter($iparr);
        $iparr = array_unique($iparr);
        foreach ($iparr as $_ip) {
            if (filter_var($_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $_longip = ip2long($_ip);
                // fix 32 位 OS 下溢出的问题
                $_longip < 0 and $_longip = sprintf("%u", $_longip);
            } else {
                $_longip = ip2long_v6($_ip);
            }
            $ipsarr[] = $_longip;
        }
    }

    $username_enable = param('username_enable', 0);
    $username_limit = param('username_limit', 0);
    $username_keyword = param('username_keyword');
    $username_keyword = $username_keyword ? explode('|', $username_keyword) : '';

    $content_enable = param('content_enable', 0);
    $content_keyword = param('content_keyword');
    $content_keyword = $content_keyword ? explode('|', $content_keyword) : '';

    $arr['filter'] = array(
        'url' => $url,
        'ip_enable' => $ip_enable,
        'ips' => $ipsarr,
        'username' => array(
            'enable' => $username_enable,
            'limit' => $username_limit,
            'keyword' => $username_keyword,
        ),
        'content' => array(
            'enable' => $content_enable,
            'keyword' => $content_keyword,
        ),
    );
    setting_set('conf', $arr);

    message(0, lang('modify_successfully'));
}

?>