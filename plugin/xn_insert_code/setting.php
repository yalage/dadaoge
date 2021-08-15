<?php

!defined('DEBUG') AND exit('Access Denied.');

if($method == 'GET') {
	
	$setting['footer_end_htm'] = setting_get('footer_end_htm');
	$setting['footer_footer_left_end_htm'] = setting_get('footer_footer_left_end_htm');
	$setting['footer_footer_right_end_htm'] = setting_get('footer_footer_right_end_htm');
	$setting['footer_start_htm'] = setting_get('footer_start_htm');
	$setting['index_flat_before_htm'] = setting_get('index_flat_before_htm');
	$setting['index_flat_main_start_htm'] = setting_get('index_flat_main_start_htm');
	$setting['index_flat_middle_htm'] = setting_get('index_flat_middle_htm');
	$setting['index_flat_mod_before_htm'] = setting_get('index_flat_mod_before_htm');
	$setting['list_main_start_htm'] = setting_get('list_main_start_htm');
	$setting['list_site_brief_end_htm'] = setting_get('list_site_brief_end_htm');
	$setting['list_site_brief_start_htm'] = setting_get('list_site_brief_start_htm');
	$setting['list_thread_list_mod_before_htm'] = setting_get('list_thread_list_mod_before_htm');
	$setting['read_item_right_end_htm'] = setting_get('read_item_right_end_htm');
	$setting['read_item_right_start_htm'] = setting_get('read_item_right_start_htm');
	$setting['read_main_breadcrumb_start_htm'] = setting_get('read_main_breadcrumb_start_htm');
	$setting['read_message_more_before_htm'] = setting_get('read_message_more_before_htm');
	$setting['read_message_more_after_htm'] = setting_get('read_message_more_after_htm');
	include _include(APP_PATH.'plugin/xn_insert_code/setting.htm');
	
} else {

	setting_set('footer_end_htm', param('footer_end_htm', '', FALSE));
	setting_set('footer_footer_left_end_htm', param('footer_footer_left_end_htm', '', FALSE));
	setting_set('footer_footer_right_end_htm', param('footer_footer_right_end_htm', '', FALSE));
	setting_set('footer_start_htm', param('footer_start_htm', '', FALSE));
	setting_set('index_flat_before_htm', param('index_flat_before_htm', '', FALSE));
	setting_set('index_flat_main_start_htm', param('index_flat_main_start_htm', '', FALSE));
	setting_set('index_flat_middle_htm', param('index_flat_middle_htm', '', FALSE));
	setting_set('index_flat_mod_before_htm', param('index_flat_mod_before_htm', '', FALSE));
	setting_set('list_main_start_htm', param('list_main_start_htm', '', FALSE));
	setting_set('list_site_brief_end_htm', param('list_site_brief_end_htm', '', FALSE));
	setting_set('list_site_brief_start_htm', param('list_site_brief_start_htm', '', FALSE));
	setting_set('list_thread_list_mod_before_htm', param('list_thread_list_mod_before_htm', '', FALSE));
	setting_set('read_item_right_end_htm', param('read_item_right_end_htm', '', FALSE));
	setting_set('read_item_right_start_htm', param('read_item_right_start_htm', '', FALSE));
	setting_set('read_main_breadcrumb_start_htm', param('read_main_breadcrumb_start_htm', '', FALSE));
	setting_set('read_message_more_before_htm', param('read_message_more_before_htm', '', FALSE));
	setting_set('read_message_more_after_htm', param('read_message_more_after_htm', '', FALSE));

	message(0, '修改成功');
}
	
?>