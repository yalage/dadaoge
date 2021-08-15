<?php exit;
$well_imagelist = '';
if (!empty($threadlist)) {
    $well_imagetids = array();
    $well_images = 0;
    foreach ($threadlist as $_thread) {
        if ($_thread['images'] < 3) continue;
        $well_images += $_thread['images'];
        $well_imagetids[] = $_thread['tid'];
    }

    if ($well_images) {
        $well_imagelist = array();
        $att_n = 0;
        $attachlist = well_attach__find(array('tid' => $well_imagetids), array(), 1, $well_images);

        if (0 == $conf['attach_on']) {
            $well_attachurl = $conf['path'] . $conf['upload_url'];
        } elseif (1 == $conf['attach_on']) {
            $well_attachurl = $conf['cloud_url'] . $conf['upload_url'];
        }

        foreach ($attachlist as $key => $attach) {
            if ($attach['pid'] || !$attach['isimage'] || $attach['width'] < 150 || $attach['height'] < 175) continue;

            if (!isset($threadlist[$attach['tid']]['images'])) continue;

            $well_image_count = isset($well_imagelist[$attach['tid']]) ? count($well_imagelist[$attach['tid']]) : 0;

            $well_thread_images = $threadlist[$attach['tid']]['images'];
            if ($well_thread_images >= 9) {
                if ($well_image_count > 9) continue;

                $well_imagelist[$attach['tid']][] = array(
                    'icon_fmt' => $well_attachurl . 'website_attach/' . $attach['filename']
                );
            } elseif ($well_thread_images >= 6 && $well_thread_images < 9) {
                if ($well_image_count > 6) continue;

                $well_imagelist[$attach['tid']][] = array(
                    'icon_fmt' => $well_attachurl . 'website_attach/' . $attach['filename']
                );
            } else {
                if ($well_image_count >= 3) continue;

                $well_imagelist[$attach['tid']][] = array(
                    'icon_fmt' => $well_attachurl . 'website_attach/' . $attach['filename']
                );
            }
        }
    }
}

/* 输出时需要判断数量 */
$apilist['imagelist'] = $well_imagelist;
?>