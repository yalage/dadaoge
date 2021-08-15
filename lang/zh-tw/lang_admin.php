<?php

return array(
    'user' => '用戶',
    'forum' => '版塊',
    'plugin' => '插件',
    'user_admin' => '用戶管理',
    'group_admin' => '用戶組管理',
    'forum_admin' => '版塊管理',
    'post_admin' => '帖子管理',
    'thread_admin' => '主題管理',
    'plugin_admin' => '插件管理',
    'other_admin' => '其他管理',
    'admin_index_page' => '後臺',
    'front_index_page' => '前臺',
    'admin_site_setting' => '站點設置',
    'admin_setting_base' => '基本設置',
    'admin_setting_smtp' => 'smtp設置',
    'admin_other_cache' => '清理緩存',
    'admin_clear_tmp' => '清理臨時目錄',
    'admin_clear_cache' => '清理緩存',
    'admin_clear_successfully' => '清理成功',
    'admin_user_list' => '用戶列表',
    'admin_thread_batch' => '主題批量管理',
    'admin_user_group' => '用戶組',
    'admin_user_create' => '創建用戶',
    'admin_plugin_local_list' => '本地插件',
    'admin_token_error' => '管理令牌錯誤,可能因為您的網絡環境不穩定,可以嘗試取消後臺綁定 IP,配置 conf.php,admin_bind_ip => 0 ',
    'admin_token_expiry' => '管理登錄令牌失效,請重新登錄',
    'user_edit' => '用戶編輯',
    'edit_successfully' => '編輯成功',
    'item_not_moderator' => '{item} 不是版主',
    'group_not_exists' => '用戶組不存在',
    'save_conf_failed' => '保存數據到配置文件 {file} 失敗,請檢查文件的可寫權限',
    'user_already_exists' => '用戶已經存在',
    'email_already_exists' => '郵箱已經存在',
    'uid_not_exists' => '指定的 UID 不存在',
    'data_not_changed' => '沒有數據變動',
    'admin_cant_be_deleted' => '不能直接刪除管理員,請先編輯為普通用戶組',
    // 首頁
    'admin_index' => '後臺首頁',
    'site_stat_info' => '站點統計信息',
    'disk_free_space' => '磁盤剩余空間',
    'server_info' => '服務器信息',
    'os' => '操作系統',
    'post_max_size' => '最大 POST 數據大小',
    'upload_max_filesize' => '最大文件上傳大小',
    'allow_url_fopen' => '允許開啟遠程 URL',
    'safe_mode' => '安全模式（safe_mode）',
    'max_execution_time' => '最長執行時間',
    'memory_limit' => '內存上限',
    'client_ip' => '客戶端 IP',
    'server_ip' => '服務端 IP',
    'for_safe_input_password_again' => '為了您的安全,請再次輸入賬戶密碼',
    // 設置
    'sitename' => '站點名稱',
    'sitebrief' => '站點介紹',
    'sitebrief_tips' => '註：支持 HTML 標簽,換行請使用 &lt;br&gt;',
    'runlevel' => '站點訪問限制',
    'user_create_on' => '开启用户注册',
    'user_create_email_on' => '開啟註冊郵箱驗證',
    'user_resetpw_on' => '開啟找回密碼',
    'lang' => '語言',
    'database' => '語言',
    'host' => '主機',
    'port' => '端口',
    'account' => '賬號',
    'smtp_host' => 'SMTP 主機',
    // 版塊
    'forum_list' => '版塊列表',
    'forum_id' => '版塊 ID',
    'forum_icon' => '圖標',
    'forum_name' => '名稱',
    'forum_rank' => '排序',
    'forum_edit' => '編輯',
    'forum_delete' => '刪除',
    'forum_brief' => '簡介',
    'forum_announcement' => '公告',
    'moderator' => '版主',
    'add_new_line' => '增加壹行',
    'forum_edit_tip' => '請謹慎編輯版塊,壹旦確定後不要輕易變動,否則可能會導致數據關聯錯誤,壹般在正式運營時就不要再變動。',
    'forum_cant_delete_system_reserved' => '不能刪除系統保留的版塊。',
    'forum_moduid_format_tips' => '最多允許10個,逗號隔開,如：Jack,Lisa,Mike',
    'user_privilege' => '用戶權限',
    'allow_view' => '允許看帖',
    'allow_thread' => '發主題',
    'allow_post' => '回貼',
    'allow_upload' => '上傳',
    'allow_download' => '下載',
    'forum_delete_thread_before_delete_forum' => '请先通过批量主题管理删除版块主题。',
    'forum_please_delete_sub_forum' => '请删除子版块。',
    'forum_delete_successfully' => '删除成功。',
    'thread_queue_not_exists' => '隊列不存在',
    'search_condition' => '搜索條件',
    'searching' => '正在搜索',
    'search_complete' => '搜索完成',
    'operating' => '正在操作',
    'operator_complete' => '操作完成',
    'click_to_view' => '點擊查看',
    'thread_userip' => '發帖 IP',
    'thread_search_result' => '結果：{n} 條',
    // 用戶
    'please_check_delete_user' => '請勾選您要刪除的用戶',
    'user_delete_confirm' => '確定刪除用戶？',
    'user_admin_cant_be_deleted' => '不允許刪除管理員用戶,如果確實要刪除,請先調整用戶組!',
    'search_type' => '搜索類型',
    'user_privileges' => '用戶權限',
    // 用戶組
    'group_list' => '用戶組列表',
    'group_edit' => '用戶組編輯',
    'group_id' => '用戶組 ID',
    'group_name' => '用戶組名',
    'group_credits_from' => '起始積分',
    'group_credits_to' => '結束積分',
    'group_edit_tips' => '請謹慎編輯用戶組,壹旦確定後不要輕易變動,否則可能會導致用戶關聯錯誤,壹般在正式運營時就不要再變動。',
    'admin_privilege' => '管理權限',
    'ban_user' => '禁止用戶',
    'view_user_info' => '查看用戶信息',
    // 插件
    'local_plugin' => '本地插件',
    'plugin_install_successfully' => '安裝插件 ( {name} ) 成功',
    'plugin_uninstall_successfully' => '卸載插件 ( {name} ) 成功,要徹底刪除插件,請手工刪除 {dir} 目錄',
    'plugin_enable_successfully' => '啟用插件 ( {name} ) 成功',
    'plugin_disable_successfully' => '禁用插件 ( {name} ) 成功',
    'plugin_dependency_following' => '依賴以下插件：{s},請先安裝或陞級依賴的插件',
    'plugin_being_dependent_cant_delete' => '不能刪除{name},以下插件依賴它：{s},',
    'plugin_uninstall_confirm_tips' => '卸載會清理該插件相關數據,確定卸載 ( {name} ) 嗎？',
    'plugin_task_locked' => '另外壹個插件任務正在執行,當前任務被鎖住。',
    'content_list' => '內容列表',
    'sticky_list' => '置頂列表',
    'comment_list' => '評論列表',
    'single__page' => '單頁列表',
    'user_list' => '用戶列表',
    'local_theme' => '本地主題',
    'other_function' => '其他功能',
    'system_setting' => '系統設置',
    'in_to_admin' => '進入後臺',
    'manage_content' => '管理內容',
    'manage_create_thread' => '創建主題',
    'manage_update_thread' => '編輯主題',
    'manage_delete_thread' => '刪除主題',
    'manage_sticky' => '管理置頂',
    'manage_comment' => '管理評論',
    'manage_single_page' => '管理單頁',
    'manage_forum' => '管理版塊',
    'manage_category' => '管理分類',
    'manage_user' => '管理用戶',
    'manage_create_user' => '創建用戶',
    'manage_update_user' => '編輯用戶',
    'manage_delete_user' => '删除用戶',
    'manage_group' => '管理用戶組',
    'manage_update_group' => '編輯用戶組',
    'manage_plugin' => '管理挿件',
    'manage_warehouse' => '管理倉庫',
    'manage_other' => '其他功能',
    'manage_setting' => '系統設置',
    'random_thread' => '隨機主題',
    'details_page_randoms' => '詳情頁隨機主題數量',
    // hook lang_zh_tw_admin.php
);

?>