<?php exit;
$subject = well_shield($subject);
$_subject = well_shield_replace($subject);
empty($subject) || empty($_subject) and message('subject', lang('well_shield_input_tips'));
true === filter_keyword($_subject, 'content', $error) and message('subject', lang('well_shield_contain_keyword') . $error);
?>