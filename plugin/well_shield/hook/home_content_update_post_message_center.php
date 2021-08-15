<?php exit;
$message = well_shield($message);
$_message = well_shield_replace($_message);
empty($message) || empty($_message) and message(1, lang('well_shield_input_tips'));
true === filter_keyword($_message, 'content', $error) and message(1, lang('well_shield_contain_keyword') . $error);
?>