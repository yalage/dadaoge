<?php exit;
if ($description) {
    $description = well_shield($description);
    $_description = well_shield_replace($description);
    true === filter_keyword($_description, 'content', $error) and message(1, lang('description') . lang('well_shield_contain_keyword') . $error);
}
?>