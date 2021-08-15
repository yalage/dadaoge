<?php exit;
$well_topic_title = well_shield($well_topic_title);
$_well_topic_title = well_shield_replace($well_topic_title);
true === filter_keyword($well_topic_title, 'content', $error) and message(1, lang('well_topic') . lang('well_shield_contain_keyword') . $error);
?>