<?php
/*
 * Copyright (C) www.wellcms.cn
 */
// hook model__attach_start.php

// ------------> 最原生的 CURD，无关联其他数据

function well_attach__create($arr, $d = NULL)
{
    // hook model__attach__create_start.php
    $r = db_insert('website_attach', $arr, $d);
    // hook model__attach__create_end.php
    return $r;
}

function well_attach__update($aid, $arr, $d = NULL)
{
    // hook model__attach__update_start.php
    $r = db_update('website_attach', array('aid' => $aid), $arr, $d);
    // hook model__attach__update_end.php
    return $r;
}

function well_attach__read($aid, $orderby = array(), $col = array(), $d = NULL)
{
    // hook model__attach__read_start.php
    $attach = db_find_one('website_attach', array('aid' => $aid), $orderby, $col, $d);
    // hook model__attach__read_end.php
    return $attach;
}

function well_attach__delete($aid, $d = NULL)
{
    // hook model__attach__delete_start.php
    $r = db_delete('website_attach', array('aid' => $aid), $d);
    // hook model__attach__delete_end.php
    return $r;
}

function well_attach__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = '', $col = array(), $d = NULL)
{
    // hook model__attach__find_start.php
    $attachlist = db_find('website_attach', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model__attach__find_end.php
    return $attachlist;
}

function well_attach_count($cond = array(), $d = NULL)
{
    // hook model__attach_count_start.php
    $n = db_count('website_attach', $cond, $d);
    // hook model__attach_count_end.php
    return $n;
}

function well_attach_max_aid($col = 'aid', $cond = array(), $d = NULL)
{
    // hook model_well_attach_max_aid_start.php
    $id = db_maxid('website_attach', $col, $cond, $d);
    // hook model_well_attach_max_aid_end.php
    return $id;
}

function attach_big_insert($arr = array(), $d = NULL)
{
    // hook model_attach_big_insert_start.php
    $r = db_big_insert('website_attach', $arr, $d);
    // hook model_attach_big_insert_end.php
    return $r;
}

function attach_big_update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_attach_big_update_start.php
    $r = db_big_update('website_attach', $cond, $update, $d);
    // hook model_attach_big_update_end.php
    return $r;
}

// ------------> 关联 CURD，主要是强相关的数据，比如缓存。弱相关的大量数据需要另外处理
function well_attach_create($arr)
{
    if (empty($arr)) return FALSE;
    // hook model__attach_create_start.php
    $r = well_attach__create($arr);
    // hook model__attach_create_end.php
    return $r;
}

function well_attach_update($aid, $update)
{
    if (empty($aid) || empty($update)) return FALSE;
    // hook model__attach_update_start.php
    $r = well_attach__update($aid, $update);
    // hook model__attach_update_end.php
    return $r;
}

function well_attach_read($aid)
{
    // hook model__attach_read_start.php

    $attach = well_attach__read($aid);

    $attach and well_attach_format($attach);

    // hook model__attach_read_end.php

    return $attach;
}

function well_attach_delete($aid)
{
    global $conf;
    if (empty($aid)) return FALSE;
    // hook model__attach_delete_start.php

    $attach = well_attach_read($aid);
    if (empty($attach)) return FALSE;

    $path = $conf['upload_path'] . 'website_attach/' . $attach['filename'];
    is_file($path) and unlink($path);

    // hook model__attach_delete_after.php

    $r = well_attach__delete($aid);

    // hook model__attach_delete_end.php
    return $r;
}

function well_attach_find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20)
{
    // hook model__attach_find_start.php

    $attachlist = well_attach__find($cond, $orderby, $page, $pagesize);
    if (empty($attachlist)) return NULL;

    // hook model__attach_find_before.php

    foreach ($attachlist as &$attach) well_attach_format($attach);
    // hook model__attach_find_end.php

    return $attachlist;
}

// 获取主题附件和图片 $filelist $imagelist
function well_attach_find_by_tid($tid)
{
    $imagelist = array();
    $filelist = array();

    // hook model__attach_find_by_tid_start.php

    $attachlist = well_attach__find(array('tid' => $tid), array(), 1, 100);
    if (empty($attachlist)) return array($attachlist, $imagelist, $filelist);

    // hook model__attach_find_by_tid_before.php

    foreach ($attachlist as $key => $attach) {
        if ($attach['pid']) continue;
        well_attach_format($attach);
        $attach['isimage'] ? $imagelist[] = $attach : $filelist[] = $attach;
    }

    // hook model__attach_find_by_tid_end.php

    return array($attachlist, $imagelist, $filelist);
}

function well_attach_delete_by_tid($tid)
{
    global $conf;
    // hook model__attach_delete_by_tid_start.php

    list($attachlist, $imagelist, $filelist) = well_attach_find_by_tid($tid);

    // hook model__attach_delete_by_tid_before.php
    if (empty($attachlist)) return FALSE;

    $aids = array();
    foreach ($attachlist as $attach) {
        $path = $conf['upload_path'] . 'website_attach/' . $attach['filename'];
        is_file($path) and unlink($path);
        $aids[] = $attach['aid'];
    }

    well_attach__delete($aids);

    // hook model__attach_delete_by_tid_end.php

    return count($attachlist);
}

/*
 * @param $tids 主题tid 数组array(1,2,3)
 * @param $n 图片和附件总数量
 * @return int 返回清理数量
 */
function well_attach_delete_by_tids($tids, $n)
{
    global $conf;

    $attachlist = well_attach__find(array('tid' => $tids), array('aid' => 1), 1, $n);
    if (!$attachlist) return 0;

    $aids = array();
    foreach ($attachlist as $attach) {
        $path = $conf['upload_path'] . 'website_attach/' . $attach['filename'];
        is_file($path) and unlink($path);
        $aids[] = $attach['aid'];
    }

    well_attach__delete($aids);

    return count($aids);
}

// 获取 $filelist $imagelist
function well_attach_find_by_pid($pid)
{
    $imagelist = array();
    $filelist = array();

    // hook model__attach_find_by_pid_start.php

    $attachlist = well_attach__find(array('pid' => $pid), array(), 1, 100);
    if (empty($attachlist)) return array($attachlist, $imagelist, $filelist);

    // hook model__attach_find_by_pid_before.php

    foreach ($attachlist as $attach) {
        well_attach_format($attach);
        $attach['isimage'] ? $imagelist[] = $attach : $filelist[] = $attach;
    }

    // hook model__attach_find_by_pid_end.php

    return array($attachlist, $imagelist, $filelist);
}

// 删除评论附件和图片
function well_attach_delete_by_pid($pid)
{
    global $conf;
    // hook model__attach_delete_by_pid_start.php

    list($attachlist, $imagelist, $filelist) = well_attach_find_by_pid($pid);

    // hook model__attach_delete_by_pid_before.php
    if (empty($attachlist)) return FALSE;

    $aids = array();
    foreach ($attachlist as $attach) {
        $path = $conf['upload_path'] . 'website_attach/' . $attach['filename'];
        is_file($path) and unlink($path);
        $aids[] = $attach['aid'];
    }

    well_attach__delete($aids);

    // hook model__attach_delete_by_pid_end.php

    return count($attachlist);
}

function well_attach_delete_by_uid($uid)
{
    global $conf;
    // hook model__attach_delete_by_uid_start.php

    $attachlist = well_attach__find(array('uid' => $uid), array(), 1, 2000);

    if (empty($attachlist)) return;

    // hook model__attach_delete_by_uid_before.php

    $aids = array();
    foreach ($attachlist as $attach) {
        $path = $conf['upload_path'] . 'website_attach/' . $attach['filename'];
        is_file($path) and unlink($path);
        $aids[] = $attach['aid'];
        // hook model__attach_delete_by_uid_after.php
    }

    well_attach__delete($aids);

    // hook model__attach_delete_by_uid_end.php
}

// ------------> 其他方法
function well_attach_format(&$attach)
{
    global $conf;
    // hook model__attach_format_start.php
    if (empty($attach)) return;
    // hook model__attach_format_before.php
    $attach['create_date_fmt'] = date('Y-n-j', $attach['create_date']);
    $attach['url'] = $conf['upload_url'] . 'website_attach/' . $attach['filename'];
    // hook model__attach_format_end.php
}

function attach_type($name, $types)
{
    // hook model_attach_type_start.php
    $ext = file_ext($name);
    foreach ($types as $type => $exts) {
        if ('all' == $type) continue;
        if (in_array($ext, $exts)) return $type;
    }
    // hook model_attach_type_end.php
    return 'other';
}

// 扫描垃圾的附件，每24小时清理一次
function attach_gc()
{
    global $conf, $time;
    // hook model_attach_gc_start.php
    $tmpfiles = glob($conf['upload_path'] . 'tmp/*.*');
    if (is_array($tmpfiles)) {
        foreach ($tmpfiles as $file) {
            // 清理超过一天还没处理的临时文件
            $time - filemtime($file) > 86400 and unlink($file);
        }
    }
    // hook model_attach_gc_end.php
}

/*
 * 附件分离，最优方案是redis队列，单独写上传云储存php文件，nohup后台运行，将队列数据上传云储存，然后根据aid更新附件表attach_on、image_url自动，根据tid更新主题表attach_on。关联附件上传云储存，有可能导致超时。
 * */
// assoc thumbnail主题主图 post:内容图片或附件
// 关联 session 中的临时文件，并不会重新统计 images, files
function well_attach_assoc_post($arr = array())
{
    if (empty($arr)) return FALSE;
    // hook model__attach_assoc_post_start.php
    $assoc = array_value($arr, 'assoc');
    // hook model__attach_assoc_post_before.php
    $arr['sess_tmp_files'] = well_attach_assoc_type($assoc);
    // hook model__attach_assoc_post_center.php
    switch ($assoc) {
        case 'thumbnail': // 主图缩略图
            // hook model__attach_assoc_post_thumbnail_start.php
            if (empty($arr['sess_tmp_files'])) return FALSE;
            // hook model__attach_assoc_post_thumbnail_before.php
            well_attach_assoc_thumbnail($arr);
            // hook model__attach_assoc_post_thumbnail_end.php
            break;
        case 'post': // 内容附件和图片
            // hook model__attach_assoc_post_file_start.php
            return well_attach_assoc_file($arr);
            // hook model__attach_assoc_post_file_end.php
            break;
        // hook model__attach_assoc_post_case.php
        default:
            message(-1, lang('data_malformation'));
            break;
    }
    // hook model__attach_assoc_post_end.php
    return TRUE;
}

// 主题缩略图
function well_attach_assoc_thumbnail($arr = array())
{
    global $conf, $time;

    // hook model_attach_assoc_thumbnail_start.php

    $tid = array_value($arr, 'tid');
    $uid = array_value($arr, 'uid');
    $sess_tmp_files = array_value($arr, 'sess_tmp_files');

    // hook model_attach_assoc_thumbnail_before.php

    // 获取文件后缀
    $ext = strtolower(file_ext($sess_tmp_files['url']));
    
    if (!in_array($ext, array('gif', 'jpg', 'jpeg', 'png'), TRUE)) {
        unlink($sess_tmp_files['path']);
        return TRUE;
    }

    // 默认位置存图
    $thumbnail_save_default = 1;
    // 10 == array_value($arr, 'type', 0) AND $thumbnail_default = 2;
    // hook model_attach_assoc_thumbnail_center.php

    if (1 == $thumbnail_save_default) {
        $attach_dir_save_rule = array_value($conf, 'attach_dir_save_rule', 'Ym');

        $day = date($attach_dir_save_rule, $time);
        $path = $conf['upload_path'] . 'thumbnail/' . $day;

        is_dir($path) || mkdir($path, 0777, TRUE);

        // 主题ID.后缀
        $destfile = $path . '/' . $uid . '_' . $tid . '_' . $time . '.' . $ext;
    }

    // hook model_attach_assoc_thumbnail_middle.php

    if (empty($destfile)) {
        unlink($sess_tmp_files['path']);
        return TRUE;
    }

    xn_copy($sess_tmp_files['path'], $destfile) || xn_log("xn_copy($sess_tmp_files[path]), $destfile) failed, tid:$tid, name:$time", 'php_error');

    // hook model_attach_assoc_thumbnail_after.php

    if (is_file($destfile) && filesize($destfile) == filesize($sess_tmp_files['path'])) unlink($sess_tmp_files['path']);

    // 清空 session
    $_SESSION['tmp_thumbnail'] = array();
    clearstatcache();

    // 按照$destfile文件路径，上传至云储存或图床，返回数据。附件分离，最优方案是redis队列，单独写上传云储存php文件，nohup后台运行，将队列数据上传云储存，然后根据aid更新附件表attach_on、image_url自动，根据tid更新主题表attach_on，如果使用了图床则需要更新主题表image_url图床文件完整网站。上传云储存，有可能导致超时。

    // hook model_attach_assoc_thumbnail_end.php

    return TRUE;
}

// 关联内容的文件
function well_attach_assoc_file($arr = array())
{
    global $conf, $time;

    // hook model_attach_assoc_file_start.php

    $uid = array_value($arr, 'uid', 0);
    $tid = array_value($arr, 'tid', 0);
    $post_create = array_value($arr, 'post_create', 0); // 创建回复
    $pid = array_value($arr, 'pid', 0);
    $images = array_value($arr, 'images', 0);
    $files = array_value($arr, 'files', 0);

    if (!$tid && !$pid) return $arr['message'];

    // hook model_attach_assoc_file_before.php

    $attach_dir_save_rule = array_value($conf, 'attach_dir_save_rule', 'Ym');

    if (!empty($arr['sess_tmp_files'])) {

        // hook model_attach_assoc_file_center.php

        foreach ($arr['sess_tmp_files'] as $file) {

            // hook model_attach_assoc_file_foreach_start.php

            // 后台提交的内容需要替换掉../
            $file['url'] = $file['backstage'] ? str_replace('../upload/', 'upload/', $file['url']) : str_replace('/upload/', 'upload/', $file['url']);

            // hook model_attach_assoc_file_foreach_before.php

            // 内容附件 将文件移动到 upload/website_attach 目录
            $filename = file_name($file['url']);
            $day = date($attach_dir_save_rule, $time);
            $path = $conf['upload_path'] . 'website_attach/' . $day;
            $url = $conf['upload_url'] . 'website_attach/' . $day;
            is_dir($path) || mkdir($path, 0777, TRUE);

            // hook model_attach_assoc_file_path_after.php

            // 复制 删除
            $destfile = $path . '/' . $filename;
            // 相对路径
            $desturl = $url . '/' . $filename;

            xn_copy($file['path'], $destfile) || xn_log("xn_copy($file[path]), $destfile) failed, tid:$tid, pid:$pid", 'php_error');

            // hook model_attach_assoc_file_copy_after.php

            if (is_file($destfile) && filesize($destfile) == filesize($file['path'])) unlink($file['path']);

            // 按照$destfile文件路径，上传至云储存或图床，返回数据.附件分离，最优方案是redis队列，单独写上传云储存php文件，nohup后台运行，将队列数据上传云储存，然后根据aid更新附件表attach_on、image_url自动，根据tid更新主题表attach_on。关联附件上传云储存，有可能导致超时。

            // hook model_attach_assoc_file_arr_before.php

            $attach = array(
                /*'tid' => $tid,
                'pid' => $pid,*/
                'uid' => $uid,
                'filesize' => $file['filesize'],
                'width' => $file['width'],
                'height' => $file['height'],
                'filename' => $day . '/' . $filename,
                'orgfilename' => $file['orgfilename'],
                //'image_url' => '', // 图床文件完整网址
                'filetype' => $file['filetype'],
                'create_date' => $time,
                'isimage' => $file['isimage'],
                'attach_on' => $conf['attach_on']
            );

            $tid and $attach += $pid ? array('pid' => $pid) : array('tid' => $tid);

            // hook model_attach_assoc_file_create_before.php

            // 关联内容再入库
            $aid = well_attach_create($attach);

            $file['backstage'] and $arr['message'] = str_replace('../upload/', 'upload/', $arr['message']);
            $arr['message'] = str_replace($file['url'], $desturl, $arr['message']);

            // hook model_attach_assoc_file_foreach_end.php
        }

        // hook model_attach_assoc_file_middle.php

        // 清空 session
        $_SESSION['tmp_website_files'] = array();
    }

    // hook model_attach_assoc_file_filter_start.php

    // 更新附件数
    $update = array();

    $_images = 0;
    $_files = 0;
    // 处理不在 message 中的图片，删除掉没有插入的图片附件
    if ($arr['message']) {

        // 只有评论会传pid
        list($attachlist, $imagelist, $filelist) = $pid ? well_attach_find_by_pid($pid) : well_attach_find_by_tid($tid);

        // hook model_attach_assoc_file_filter_before.php

        if (!empty($imagelist)) {
            foreach ($imagelist as $key => $attach) {

                $url = $conf['upload_url'] . 'website_attach/' . $attach['filename'];

                // hook model_attach_assoc_file_filter_delete_before.php

                if (FALSE === strpos($arr['message'], $url)) {
                    unset($imagelist[$key]);
                    well_attach_delete($attach['aid']);
                    // hook model_attach_assoc_file_filter_delete.php
                }

                //1 == $conf['attach_delete'] 开启云储存后删除本地附件
                /*$path = $conf['upload_path'] . 'website_attach/' . $attach['filename'];
                if (1 == $conf['attach_delete'] && is_file($path)) unlink($path);*/

                // hook model_attach_assoc_file_filter_center.php
            }

            // hook model_attach_assoc_file_filter_middle.php
        }

        $_images = count($imagelist);
        $images != $_images and $update['images'] = $_images;

        $_files = count($filelist);
        $files != $_files and $update['files'] = $_files;

        // hook model_attach_assoc_file_filter_end.php
    }

    // hook model_attach_assoc_file_filter_end.php

    if (empty($update)) return $pid ? array($arr['message'], $_images, $_files) : $arr['message'];

    if ($pid) {
        if ($post_create) {
            $update['message'] = $arr['message'];
            comment__update($pid, $update);
        } else {
            // 编辑回复返回的数据
            return array($arr['message'], $_images, $_files);
        }
    } else {
        well_thread_update($tid, $update);
    }

    // hook model_attach_assoc_file_end.php

    return $arr['message'];
}

// thumbnail:主题主图 post:内容图片或附件
function well_attach_assoc_type($type)
{
    // hook model__attach_assoc_type_start.php
    switch ($type) {
        case 'thumbnail':
            $k = 'tmp_thumbnail';
            break;
        case 'post':
            $k = 'tmp_website_files';
            break;
        // hook model__attach_assoc_case_end.php
        default:
            return NULL;
            break;
    }
    $sess_tmp_files = _SESSION($k);
    // 如果session中没有，从数据库中获取储存的session
    //if (empty($sess_tmp_files) && preg_match('#' . $k . '\|(a\:1\:\{.*\})#', _SESSION('data'), $matches)) $sess_tmp_files = unserialize(str_replace(array('+', '='), array('_', '.'), $matches['1']));
    // hook model__attach_assoc_type_end.php
    return $sess_tmp_files;
}

// Create thumbnail
function well_attach_create_thumbnail($arr)
{
    global $conf, $time, $forumlist, $config;

    $uid = array_value($arr, 'uid', 0);
    $tid = array_value($arr, 'tid', 0);
    $fid = array_value($arr, 'fid', 0);
    $forum = array_value($forumlist, $fid);

    $picture = $config['picture_size'];
    $picture = isset($forum['thumbnail']) ? $forum['thumbnail'] : $picture['picture_size'];
    $pic_width = $picture['width'];
    $pic_height = $picture['height'];

    $attachlist = well_attach_assoc_type('post');
    if (empty($attachlist)) return;

    $attach_dir_save_rule = array_value($conf, 'attach_dir_save_rule', 'Ym');

    $day = date($attach_dir_save_rule, $time);
    $path = $conf['upload_path'] . 'thumbnail/' . $day;
    is_dir($path) || mkdir($path, 0777, TRUE);

    $tmp_file = $conf['upload_path'] . 'tmp/' . $uid . '_' . $tid . '_' . $time . '.jpeg';

    $i = 0;
    foreach ($attachlist as $val) {
        ++$i;
        if (1 == $val['isimage'] && 1 == $i) {
            'clip' == array_value($conf, 'upload_resize', 'clip') ? well_image_clip_thumb($val['path'], $tmp_file, $pic_width, $pic_height) : well_image_thumb($val['path'], $tmp_file, $pic_width, $pic_height);
            break;
        }
    }
    $destfile = $path . '/' . $uid . '_' . $tid . '_' . $time . '.jpeg';
    xn_copy($tmp_file, $destfile) || xn_log("xn_copy($tmp_file), $destfile) failed, tid:$tid", 'php_error');
}

function well_save_remote_image($arr)
{
    global $conf, $time, $forumlist, $config;

    $message = array_value($arr, 'message');
    $tid = array_value($arr, 'tid', 0);
    $fid = array_value($arr, 'fid', 0);
    $uid = array_value($arr, 'uid', 0);
    $thumbnail = array_value($arr, 'thumbnail', 0);
    $save_image = array_value($arr, 'save_image', 0);

    $attach_dir_save_rule = array_value($conf, 'attach_dir_save_rule', 'Ym');

    $day = date($attach_dir_save_rule, $time);
    $attach_dir = $conf['upload_path'] . 'website_attach/' . $day . '/';
    $attach_url = $conf['upload_url'] . 'website_attach/' . $day . '/';
    is_dir($attach_dir) || mkdir($attach_dir, 0777, TRUE);

    if ($thumbnail) {

        $picture = $config['picture_size'];
        $forum = array_value($forumlist, $fid);
        $picture = isset($forum['thumbnail']) ? $forum['thumbnail'] : $picture['picture_size'];
        $pic_width = $picture['width'];
        $pic_height = $picture['height'];

        $thumbnail_path = $conf['upload_path'] . 'thumbnail/' . $day . '/';
        is_dir($thumbnail_path) || mkdir($thumbnail_path, 0777, TRUE);

        $tmp_file = $thumbnail_path . $uid . '_' . $tid . '_' . $time . '.jpeg';
    }

    $localurlarr = array(
        'http://' . $_SERVER['SERVER_NAME'] . '/',
        'https://' . $_SERVER['SERVER_NAME'] . '/',
    );

    //$save_image_quality = array_value($conf, 'save_image_quality', 0);
    $save_image_quality = 0;
    
    $message = urldecode($message);
    $message = str_replace('&amp;', '&', $message);
    preg_match_all('#<img[^>]+src="(http.*?)"#i', $message, $match);

    if (!empty($match[1])) {
        $n = 0;
        $i = 0;
        foreach ($match[1] as $url) {

            foreach ($localurlarr as $localurl) {
                if ($localurl == substr($url, 0, strlen($localurl))) continue 2;
            }

            $getimgsize = getimagesize($url);
            if (FALSE === $getimgsize) continue; // 非图片跳出

            $filename = $uid . '_' . xn_rand(16);
            if (1 == $getimgsize[2]) {
                $filename .= '.gif';
                $destpath = $attach_dir . $filename;
            } elseif (in_array($getimgsize[2], array(2, 3, 15, 18))) {
                $filename .= '.jpeg';
                $destpath = $attach_dir . $filename;
            } else {
                continue; // 非常见图片格式跳出
                /*$imgdata = https_request($url);
                $imageurl = well_get_image_url($url);
                $ext = $imageurl ? file_ext($imageurl) : '';
                $filename = $uid . '_' . xn_rand(16) . '.' . ($ext ? $ext : 'jpeg');
                $destpath = $attach_dir . $filename;
                file_put_contents_try($destpath, $imgdata);*/
            }

            $desturl = $attach_url . $filename;
            $_message = str_replace($url, $desturl, $message);

            if ($message != $_message) {

                if (0 == $save_image_quality) {
                    $imgdata = https_request($url);
                    $destpath = $attach_dir . $filename;
                    file_put_contents_try($destpath, $imgdata);
                } else {
                    // 图片压缩 GD 库效率低下 ImageMagick 需要额外安装扩展
                    switch ($getimgsize[2]) {
                        case 1: // GIF
                            $imgdata = imagecreatefromgif($url);
                            break;
                        case 2: // JPG
                            $imgdata = imagecreatefromjpeg($url);
                            break;
                        case 3: // PNG
                            $imgdata = imagecreatefrompng($url);
                            break;
                        case 15: // WBMP
                            $imgdata = imagecreatefromwbmp($url);
                            break;
                        case 18: // WEBP
                            $imgdata = imagecreatefromwebp($url);
                            break;
                    }
                    imagejpeg($imgdata, $destpath, $save_image_quality);
                    imagedestroy($imgdata);
                }

                if ($thumbnail) {
                    if (1 == ++$i) {
                        // 裁切保存到缩略图目录
                        'clip' == array_value($conf, 'upload_resize', 'clip') ? well_image_clip_thumb($destpath, $tmp_file, $pic_width, $pic_height, $getimgsize) : well_image_thumb($destpath, $tmp_file, $pic_width, $pic_height, $getimgsize);
                        well_thread_update($tid, array('icon' => $time));
                    }
                    if (empty($save_image)) {
                        is_file($destpath) and unlink($destpath);
                        continue;
                    }
                }

                $filesize = strlen($imgdata);
                $attach = array('tid' => $tid, 'uid' => $uid, 'filesize' => $filesize, 'width' => $getimgsize[0], 'height' => $getimgsize[1], 'filename' => "$day/$filename", 'orgfilename' => $filename, 'filetype' => 'image', 'create_date' => $time, 'comment' => '', 'downloads' => 0, 'isimage' => 1);
                $aid = well_attach_create($attach);
                $n++;
            }

            $message = preg_replace('#(<img.*?)(class=.+?[\'|\"])|(data-src=.+?[\'|"])|(data-type=.+?[\'|"])|(data-ratio=.+?[\'|"])|(data-s=.+?[\'|"])|(data-fail=.+?[\'|"])|(crossorigin=.+?[\'|"])|((data-w)=[\'"]+[0-9]+[\'"]+)|(_width=.+?[\'|"]+)|(_height=.+?[\'|"]+)|(style=.+?[\'|"])|((width)=[\'"]+[0-9]+[\'"]+)|((height)=[\'"]+[0-9]+[\'"]+)#i', '$1', $_message);
        }
        // hook model_attach_save_remote_image_after.php
        $n and well_thread_update($tid, array('images+' => $n));
    }
    // hook model_attach_save_remote_image_end.php
    return $message;
}

function well_get_image_url($url)
{
    if ($n = strpos($url, '.jpg')) {
        $_n = $n + 4;
    } elseif ($n = strpos($url, '.jpeg')) {
        $_n = $n + 5;
    } elseif ($n = strpos($url, '.png')) {
        $_n = $n + 4;
    } elseif ($n = strpos($url, '.gif')) {
        $_n = $n + 4;
    } elseif ($n = strpos($url, '.bmp')) {
        $_n = $n + 4;
    }

    $url = $n ? mb_substr($url, 0, $_n, 'UTF-8') : NULL;

    return $url;
}

// hook model__attach_end.php

?>