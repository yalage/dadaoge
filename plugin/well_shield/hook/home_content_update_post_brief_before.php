<?php exit;
$brief = well_shield($brief);
$_brief = well_shield_replace($brief);
true === filter_keyword($_brief, 'content', $error) and message(1, lang('brief') . lang('well_shield_contain_keyword') . $error);
?>