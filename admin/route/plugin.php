<?php
!defined('DEBUG') and exit('Access Denied.');

FALSE === group_access($gid, 'manageplugin') and message(1, lang('user_group_insufficient_privilege'));

$action = param(1, 'list');

// 初始化插件变量 / init plugin var
plugin_init();

switch ($action) {
    case 'list':

        $page = param('page', 1);
        $type = param('type', 0);
        $pagesize = 20;
        $extra = array('page' => '{page}', 'type' => $type);

        $plugin_cates = array(0 => lang('all'), 1 => lang('enabled'), 2 => lang('not_enabled'), 3 => lang('disable'));
        $plugin_cate_html = plugin_cate_active($action, $plugin_cates, $type, $page);

        if (1 == $type) {
            $cond = array('installed' => 1, 'enable' => 1);
        } elseif (2 == $type) {
            $cond = array('installed' => 0, 'enable' => 0);
        } elseif (3 == $type) {
            $cond = array('installed' => 1, 'enable' => 0);
        } else {
            $cond = array();
        }

        // 本地插件 local plugin list
        $pluginlist = plugin_list($cond, $orderby = array(), $page, $pagesize, TRUE);
        $total = arrlist_cond_orderby($plugins, $cond, array(), 1, 1000);

        $pagination = pagination(url('plugin-' . $action, $extra, TRUE), count($total), $page, $pagesize);

        $safe_token = well_token_set($uid);
        $extra += array('safe_token' => $safe_token);
        $header['title'] = lang('local_plugin');
        $header['mobile_title'] = lang('local_plugin');
        $active = 'plugin';

        include _include(ADMIN_PATH . 'view/htm/plugin_list.htm');
        break;
    case 'theme':
        if ('GET' == $method) {

            $page = param('page', 1);
            $pagesize = 30;
            $extra = array('page' => '{page}');
            $cond = array();
            $pluginlist = plugin_list($cond, $orderby = array(), $page, $pagesize, FALSE);
            $total = arrlist_cond_orderby($themes, $cond, array(), 1, 1000);

            $read = array_value($pluginlist, $config['theme']);
            if (!array_value($read, 'installed')) {
                foreach ($pluginlist as $dir => $theme) {
                    if (1 == $theme['installed']) {
                        $read = $theme;
                        theme_install($dir);
                        unset($pluginlist[$dir]);
                        continue;
                    }
                }
            }

            !empty($read) and $pluginlist = array($config['theme'] => $read) + $pluginlist;

            $pagination = pagination(url('plugin-' . $action, $extra, TRUE), count($total), $page, $pagesize);

            $safe_token = well_token_set($uid);
            $extra += array('safe_token' => $safe_token);
            $header['title'] = lang('local') . lang('theme');
            $header['mobile_title'] = lang('local') . lang('theme');

            include _include(ADMIN_PATH . "view/htm/theme_list.htm");

        } elseif ('POST' == $method) {

            FALSE === group_access($gid, 'manageplugin') and message(1, lang('user_group_insufficient_privilege'));

            $dir = param_word('dir');
            $type = param('type', 0);

            empty($dir) and message(1, lang('data_malformation'));

            if (1 == $type) {
                plugin_check_dependency($dir);
                theme_install($dir);

                plugin_clear_tmp_dir();
                message(0, lang('install_successfully'));
            } else {
                theme_uninstall($config['theme']);
                plugin_clear_tmp_dir();
                message(0, lang('uninstall_successfully'));
            }
        }
        break;

    case 'install':
        $safe_token = param('safe_token');
        FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));

        plugin_lock_start();

        $dir = param_word('dir');
        plugin_check_exists($dir);
        $name = $plugins[$dir]['name'];

        // 插件依赖检查 / check plugin dependency
        plugin_check_dependency($dir, 'install');
        
        // 安装插件 / install plugin
        plugin_install($dir);

        $installfile = APP_PATH . 'plugin/' . $dir . '/install.php';
        is_file($installfile) and include _include($installfile);

        plugin_lock_end();

        // 卸载同类插件，防止安装类似插件 自动卸载掉其他已经安装的主题 / automatically uninstall other theme plugin.
        if (FALSE !== strpos($dir, '_theme_')) {
            foreach ($plugins as $_dir => $_plugin) {
                if ($dir == $_dir) continue;
                FALSE !== strpos($_dir, '_theme_') and plugin_uninstall($_dir);
            }
        } else {
            // 卸载掉同类插件
            $suffix = substr($dir, strpos($dir, '_'));
            foreach ($plugins as $_dir => $_plugin) {
                if ($dir == $_dir) continue;
                $_suffix = substr($_dir, strpos($_dir, '_'));
                $suffix == $_suffix and plugin_uninstall($_dir);
            }
        }
        
        $msg = lang('plugin_install_successfully', array('name' => $name));
        $url = is_file(APP_PATH . "plugin/$dir/setting.php") ? url('plugin-setting', array('dir' => $dir), TRUE) : url('plugin-list', array('type' => 1), TRUE);
        message(0, jump($msg, $url, 2));
        break;
    case 'uninstall':

        if ('POST' != $method) message(1, lang('method_error'));

        $safe_token = param('safe_token');
        FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));

        plugin_lock_start();

        $dir = param_word('dir');
        plugin_check_exists($dir);
        $name = $plugins[$dir]['name'];

        // 插件依赖检查
        plugin_check_dependency($dir, 'uninstall');

        // 卸载插件
        plugin_uninstall($dir);

        $uninstallfile = APP_PATH . 'plugin/' . $dir . '/uninstall.php';
        is_file($uninstallfile) and include _include($uninstallfile);

        // 删除插件
        //!DEBUG && rmdir_recusive(APP_PATH . "plugin/$dir");

        plugin_lock_end();

        $msg = lang('plugin_uninstall_successfully', array('name' => $name, 'dir' => "plugin/$dir"));
        message(0, jump($msg, url('plugin-list', array('type' => 2), TRUE), 3));
        break;
    case 'enable':
        $safe_token = param('safe_token');
        FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));

        plugin_lock_start();

        $dir = param_word('dir');
        plugin_check_exists($dir);
        $name = $plugins[$dir]['name'];

        // 插件依赖检查
        plugin_check_dependency($dir, 'install');

        // 启用插件
        plugin_enable($dir);

        plugin_lock_end();

        $msg = lang('plugin_enable_successfully', array('name' => $name));
        message(0, jump($msg, url('plugin-read', array('dir' => $dir), TRUE), 1));
        break;
    case 'disable':
        $safe_token = param('safe_token');
        FALSE === well_token_verify($uid, $safe_token) and message(1, lang('illegal_operation'));

        plugin_lock_start();

        $dir = param_word('dir');
        plugin_check_exists($dir);
        $name = $plugins[$dir]['name'];

        // 插件依赖检查
        plugin_check_dependency($dir, 'uninstall');

        // 禁用插件
        plugin_disable($dir);

        plugin_lock_end();

        $msg = lang('plugin_disable_successfully', array('name' => $name));
        message(0, jump($msg, url('plugin-read', array('dir' => $dir), TRUE), 3));
        break;

    case 'setting':
        $dir = param_word('dir');
        empty($dir) and $dir = param_word(2); // 兼容旧插件
        plugin_check_exists($dir);
        $name = $plugins[$dir]['name'];

        include _include(APP_PATH . 'plugin/' . $dir . '/setting.php');
        break;
    default:
        message(-1, lang('data_malformation'));
        break;
}

function plugin_check_dependency($dir, $action = 'install')
{
    global $plugins, $themes;
    $name = isset($plugins[$dir]) ? $plugins[$dir]['name'] : $themes[$dir]['name'];
    if ('install' == $action) {
        $arr = plugin_dependencies($dir);
        if (!empty($arr)) {
            plugin_lock_end();
            $s = plugin_dependency_arr_to_links($arr);
            message(-1, lang('plugin_dependency_following', array('name' => $name, 's' => $s)));
        }
    } else {
        $arr = plugin_by_dependencies($dir);
        if (!empty($arr)) {
            plugin_lock_end();
            $s = plugin_dependency_arr_to_links($arr);
            message(-1, lang('plugin_being_dependent_cant_delete', array('name' => $name, 's' => $s)));
        }
    }
}

function plugin_is_local($dir)
{
    global $plugins, $themes;
    if (isset($plugins[$dir])) {
        return TRUE;
    } else {
        return isset($themes[$dir]) ? TRUE : FALSE;
    }
}

function plugin_check_exists($dir, $local = TRUE)
{
    global $plugins, $themes;
    !is_word($dir) and message(-1, lang('plugin_name_error'));
    if ($local) {
        empty($plugins[$dir]) and !isset($themes[$dir]) and message(-1, lang('plugin_not_exists'));
    }
}

function plugin_cate_active($action, $arr, $type, $page)
{
    $s = '';
    foreach ($arr as $_type => $name) {
        $url = url('plugin-' . $action, array('type' => $_type, 'page' => $page), TRUE);
        $s .= '<a role="button" class="btn btn btn-secondary' . ($type == $_type ? ' active' : '') . '" href="' . $url . '">' . $name . '</a>';
    }
    return $s;
}

function plugin_lock_start()
{
    global $route, $action;
    !xn_lock_start($route . '_' . $action) and message(-1, lang('plugin_task_locked'));
}

function plugin_lock_end()
{
    global $route, $action;
    xn_lock_end($route . '_' . $action);
}

function theme_install($dir)
{
    global $conf, $config;

    $dir = trim($dir);
    if (!empty($config['theme']) && $config['theme'] != $dir) {
        is_file(APP_PATH . 'view/template/' . $dir.'/conf.json') and theme_uninstall($config['theme']);
    }

    $path = APP_PATH . 'view/template/' . $dir;

    $conffile = $path . '/conf.json';
    !is_file($conffile) and message(1, lang('not_exists'));

    $arr = xn_json_decode(file_get_contents($conffile));
    empty($arr) and message(1, lang('data_malformation'));

    $arr['installed'] = 1;
    // 写入配置文件
    file_replace_var($conffile, $arr, TRUE);

    $config['theme'] = $dir;
    setting_set('conf', $config);

    $installfile = $path . '/install.php';
    is_file($installfile) and include _include($installfile);

    rmdir_recusive($conf['tmp_path'], 1);

    return TRUE;
}

function theme_uninstall($dir)
{
    global $conf, $config;

    $path = APP_PATH . 'view/template/' . $dir;

    $conffile = $path . '/conf.json';
    //FALSE === is_file($conffile) and message(1, lang('not_exists'));

    if (is_file($conffile)) {
        $arr = xn_json_decode(file_get_contents($conffile));
        empty($arr) and message(1, lang('data_malformation'));

        $arr['installed'] = 0;
        // 写入配置文件
        file_replace_var($conffile, $arr, TRUE);
    }

    $config['theme'] = '';
    setting_set('conf', $config);

    $uninstallfile = $path . '/uninstall.php';
    is_file($uninstallfile) and include _include($uninstallfile);

    rmdir_recusive($conf['tmp_path'], 1);

    return TRUE;
}

?>