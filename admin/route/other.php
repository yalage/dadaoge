<?php
/*
 * Copyright (C) www.wellcms.cn
 */
!defined('DEBUG') and exit('Access Denied.');

FALSE === group_access($gid, 'manageother') and message(1, lang('user_group_insufficient_privilege'));

$action = param(1, 'cache');

// hook admin_other_start.php

switch ($action) {
    // hook admin_other_case_start.php
    case 'cache':
        // hook admin_other_cache_get_post.php

        if ('GET' == $method) {

            // hook admin_other_cache_get_start.php

            $input = array();
            $input['clear_tmp'] = form_checkbox('clear_tmp', 1);
            $input['clear_cache'] = form_checkbox('clear_cache');
            $safe_token = well_token_set($uid);
            $input['safe_token'] = form_hidden('safe_token', $safe_token);

            // hook admin_other_cache_get_end.php

            $header['title'] = lang('admin_clear_cache');
            $header['mobile_title'] = lang('admin_clear_cache');
            $header['mobile_link'] = url('other-cache', '', TRUE);

            include _include(ADMIN_PATH . 'view/htm/other_cache.htm');

        } elseif ('POST' == $method) {

            $safe_token = param('safe_token');
            FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));

            // hook admin_other_cache_post_start.php

            $clear_tmp = param('clear_tmp');
            $clear_cache = param('clear_cache');

            $clear_cache and cache_truncate();
            $clear_cache and $runtime = NULL; // 清空

            $g_website = kv_cache_get('website');
            $g_website['flag'] = '';
            $g_website['flag_thread'] = '';
            $g_website['grouplist'] = '';
            // hook admin_other_cache_post_before.php
            kv_cache_set('website', $g_website);

            $clear_tmp and rmdir_recusive($conf['tmp_path'], 1);

            // hook admin_other_cache_post_end.php

            message(0, lang('admin_clear_successfully'));
        }
        break;
    case 'link':
        if ('GET' == $method) {

            // hook admin_other_link_get_start.php

            $page = param('page', 1);
            $pagesize = 20;
            $extra = array('page' => '{page}');

            $input = array();
            $input['name'] = form_text('name', '', $width = FALSE, lang('site_name'));
            $input['url'] = form_text('url', '', $width = FALSE, lang('site_url'));

            $safe_token = well_token_set($uid);

            // hook admin_other_link_get_before.php

            $n = link_count();
            $arrlist = link_get($page, $n);

            // hook admin_other_link_get_after.php

            $pagination = pagination(url('other-link', $extra, TRUE), $n, $page, $pagesize);

            $header['title'] = lang('friends_link');
            $header['mobile_title'] = lang('friends_link');
            $header['mobile_link'] = url('other-link', '', TRUE);

            // hook admin_other_link_get_end.php

            include _include(ADMIN_PATH . 'view/htm/other_link.htm');

        } elseif ('POST' == $method) {

            $safe_token = param('safe_token');
            FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));

            $type = param('type', 0);

            if (1 == $type) {
                $name = param('name');
                $name = filter_all_html($name);
                $url = param('url');

                FALSE === link_create(array('name' => $name, 'url' => $url, 'create_date' => $time)) and message(-1, lang('create_failed'));

                message(0, lang('create_successfully'));

            } elseif (2 == $type) {
                // 排序
                $arr = _POST('data');

                empty($arr) && message(1, lang('data_is_empty'));

                foreach ($arr as &$val) {
                    $rank = intval($val['rank']);
                    $id = intval($val['id']);
                    intval($val['oldrank']) != $rank && $id && link_update($id, array('rank' => $rank));
                }

                message(0, lang('update_successfully'));

            } else {

                $id = param('id', 0);
                FALSE === link_delete($id) and message(-1, lang('delete_failed'));

                message(0, lang('delete_successfully'));
            }
        }
        break;








    // hook admin_other_case_end.php
    default:
        message(-1, lang('data_malformation'));
        break;
}
// hook admin_other_end.php

?>