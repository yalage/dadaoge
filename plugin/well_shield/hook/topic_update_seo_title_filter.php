<?php exit;
$seo_title = well_shield($seo_title);
$_seo_title = well_shield_replace($seo_title);
true === filter_keyword($_seo_title, 'content', $error) and message('seo_title', lang('well_shield_contain_keyword') . $error);
?>