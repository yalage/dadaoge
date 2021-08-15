<?php exit;
if ($keyword) {
    $keyword = well_shield($keyword);
    $_keyword = well_shield_replace($keyword);
    true === filter_keyword($_keyword, 'content', $error) and message(1, lang('keywords') . lang('well_shield_contain_keyword') . $error);
}
?>