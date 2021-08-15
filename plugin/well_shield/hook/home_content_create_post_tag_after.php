<?php exit;
if($tags) {
    $tags = well_shield($tags);
    $_tags = well_shield_replace($tags);
    true === filter_keyword($_tags, 'content', $error) and tags(1, 'tag '.lang('well_shield_contain_keyword') . $error);
}
?>