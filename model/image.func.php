<?php

// 先裁切后缩略，因为确定了，width, height, 不需要返回宽高。
// well_image_clip_thumb(绝对路径, 存取的绝对路径, 宽度, 高度);
function well_image_clip_thumb($sourcefile, $destfile, $forcedwidth = 210, $forcedheight = 140, $getimgsize = '')
{
    // 获取原图片宽高
    empty($getimgsize) AND $getimgsize = getimagesize($sourcefile);

    if (empty($getimgsize)) {
        return 0;
    } else {
        $src_width = $getimgsize[0];
        $src_height = $getimgsize[1];
        if (0 == $src_width || 0 == $src_height) {
            return 0;
        }
    }

    // 资源比
    $src_scale = $src_width / $src_height;
    // 裁切比
    $des_scale = $forcedwidth / $forcedheight;

    // 以资源宽度为标准
    $w_scale = $src_width / $forcedwidth;
    // 裁切高度
    $w_clip_height = $w_scale * $forcedheight;

    // 以资源高度为标准
    $h_scale = $src_height / $forcedheight;
    // 裁切宽度
    $w_clip_width = $h_scale * $forcedwidth;

    $clipx = 0;
    $clipy = 0;
    $des_width = $src_width; // 原始宽
    $des_height = $src_height ; // 原始高

    /*资源比>裁切比 原图为横着的矩形图*/
    if ($src_scale > $des_scale) {
        // 小于缩略高度 放弃
        if ($src_height < $forcedheight) return 0;
    } else {
        /*原始宽<裁切宽 竖着的矩形图*/
        // 小于缩略高度 放弃
        if ($src_width < $forcedwidth) return 0;
    }

    // 以资源宽度为标准 裁切高<=资源高
    if ($w_clip_height <= $src_height) {
        if ($src_height > $w_clip_height) {
            /*计算高度居中裁切 原始高 - 裁切高 / 2 */
            $clipy = ($src_height - $w_clip_height) / 2;
            $des_height = $w_clip_height; // 裁切宽
        }
    } else {
        // 以资源高度为标准
        if ($src_width > $w_clip_width) {
            /*计算宽度居中裁切 原始宽 - 裁切宽 / 2 */
            $clipx = ($src_width - $w_clip_width) / 2;
            $des_width = $w_clip_width; // 裁切宽
        }
    }

    /*按照输入比宽度裁切*/
    $n = well_image_clip($sourcefile, $destfile, $clipx, $clipy, $des_width, $des_height, $getimgsize);

    if ($n <= 0) return 0;
    $r = well_image_thumb($destfile, $destfile, $forcedwidth, $forcedheight);
    return $r['filesize'];
}

function well_image_thumb($sourcefile, $destfile, $forcedwidth = 80, $forcedheight = 80, $getimgsize = '')
{
    global $conf;
    $return = array('filesize' => 0, 'width' => 0, 'height' => 0);
    $destext = well_image_ext($destfile);
    if (!in_array($destext, array('gif', 'jpg', 'jpeg', 'bmp', 'png'))) {
        return $return;
    }

    empty($getimgsize) AND $getimgsize = getimagesize($sourcefile);
    $src_width = $getimgsize[0];
    $src_height = $getimgsize[1];
    if (0 == $src_width || 0 == $src_height) {
        return $return;
    }

    if (!function_exists('imagecreatefromjpeg')) {
        copy($sourcefile, $destfile);
        $return = array('filesize' => filesize($destfile), 'width' => $src_width, 'height' => $src_height);
        return $return;
    }

    // 按规定比例缩略
    $src_scale = $src_width / $src_height;
    $des_scale = $forcedwidth / $forcedheight;

    if ($src_width <= $forcedwidth && $src_height <= $forcedheight) {
        $des_width = $src_width;
        $des_height = $src_height;
    } elseif ($src_scale >= $des_scale) {
        $des_width = ($src_width >= $forcedwidth) ? $forcedwidth : $src_width;
        $des_height = $des_width / $src_scale;
        $des_height = ($des_height >= $forcedheight) ? $forcedheight : $des_height;
    } else {
        $des_height = ($src_height >= $forcedheight) ? $forcedheight : $src_height;
        $des_width = $des_height * $src_scale;
        $des_width = ($des_width >= $forcedwidth) ? $forcedwidth : $des_width;
    }

    $des_width = ceil($des_width);
    $des_height = ceil($des_height);

    switch ($getimgsize['mime']) {
        case 'image/jpeg':
            $img_src = imagecreatefromjpeg($sourcefile);
            !$img_src && $img_src = imagecreatefromgif($sourcefile);
            break;
        case 'image/gif':
            $img_src = imagecreatefromgif($sourcefile);
            !$img_src && $img_src = imagecreatefromjpeg($sourcefile);
            break;
        case 'image/png':
            $img_src = imagecreatefrompng($sourcefile);
            break;
        case 'image/wbmp':
            $img_src = imagecreatefromwbmp($sourcefile);
            break;
        default :
            return $return;
    }

    if (!$img_src) return $return;

    $img_dst = imagecreatetruecolor($des_width, $des_height);
    imagefill($img_dst, 0, 0, 0xFFFFFF);
    imagecopyresampled($img_dst, $img_src, 0, 0, 0, 0, $des_width, $des_height, $src_width, $src_height);

    $tmppath = isset($conf['tmp_path']) ? $conf['tmp_path'] : ini_get('upload_tmp_dir') . '/';
    '/' == $tmppath AND $tmppath = './tmp/';

    $tmpfile = $tmppath . md5($destfile) . '.tmp';
    switch ($destext) {
        case 'jpg':
            imagejpeg($img_dst, $tmpfile, 75);
            break;
        case 'jpeg':
            imagejpeg($img_dst, $tmpfile, 75);
            break;
        case 'gif':
            imagegif($img_dst, $tmpfile);
            break;
        case 'png':
            imagepng($img_dst, $tmpfile);
            break;
    }
    $r = array('filesize' => filesize($tmpfile), 'width' => $des_width, 'height' => $des_height);;
    copy($tmpfile, $destfile);
    is_file($tmpfile) && unlink($tmpfile);
    imagedestroy($img_dst);
    return $r;
}

/**
 * 图片裁切
 *
 * @param string $sourcefile 原图片路径(绝对路径/abc.jpg)
 * @param string $destfile 裁切后生成新名称(绝对路径/rename.jpg)
 * @param int $clipx 被裁切图片的X坐标
 * @param int $clipy 被裁切图片的Y坐标
 * @param int $clipwidth 被裁区域的宽度
 * @param int $clipheight 被裁区域的高度
 * image_clip('xxx/x.jpg', 'xxx/newx.jpg', 10, 40, 150, 150)
 */
function well_image_clip($sourcefile, $destfile, $clipx, $clipy, $clipwidth, $clipheight, $getimgsize = '')
{
    global $conf;
    empty($getimgsize) AND $getimgsize = getimagesize($sourcefile);
    if (empty($getimgsize)) {
        return 0;
    } else {
        $imgwidth = $getimgsize[0];
        $imgheight = $getimgsize[1];
        if (0 == $imgwidth || 0 == $imgheight) {
            return 0;
        }
    }

    if (!function_exists('imagecreatefromjpeg')) {
        copy($sourcefile, $destfile);
        return filesize($destfile);
    }
    switch ($getimgsize[2]) {
        case 1 :
            $imgcolor = imagecreatefromgif($sourcefile);
            break;
        case 2 :
            $imgcolor = imagecreatefromjpeg($sourcefile);
            break;
        case 3 :
            $imgcolor = imagecreatefrompng($sourcefile);
            break;
    }

    if (!$imgcolor) return 0;

    $img_dst = imagecreatetruecolor($clipwidth, $clipheight);
    imagefill($img_dst, 0, 0, 0xFFFFFF);
    imagecopyresampled($img_dst, $imgcolor, 0, 0, $clipx, $clipy, $imgwidth, $imgheight, $imgwidth, $imgheight);

    $tmppath = isset($conf['tmp_path']) ? $conf['tmp_path'] : ini_get('upload_tmp_dir') . '/';
    '/' == $tmppath AND $tmppath = './tmp/';

    $tmpfile = $tmppath . md5($destfile) . '.tmp';
    imagejpeg($img_dst, $tmpfile, 75);
    $n = filesize($tmpfile);
    copy($tmpfile, $destfile);
    is_file($tmpfile) && unlink($tmpfile);
    return $n;
}

function well_image_ext($filename) {
    return strtolower(substr(strrchr($filename, '.'), 1));
}

?>