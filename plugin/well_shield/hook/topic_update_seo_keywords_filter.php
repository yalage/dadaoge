<?php exit;
$seo_keywords = well_shield($seo_keywords);
$_seo_keywords = well_shield_replace($seo_keywords);
true === filter_keyword($seo_keywords, 'content', $error) and message('seo_keywords', lang('well_shield_contain_keyword') . $error);
?>