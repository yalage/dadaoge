<?php exit;
$seo_description = well_shield($seo_description);
$_seo_description = well_shield_replace($seo_description);
true === filter_keyword($_seo_description, 'content', $error) and message('seo_description', lang('well_shield_contain_keyword') . $error);
?>