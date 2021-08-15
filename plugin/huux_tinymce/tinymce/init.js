/*这个是配置文件, 请参考备注，问题反馈请到www.huux.cc*/
if (!xn.isset(tinymce_path)) tinymce_path = '';
tinymce.init({
    selector: '#message',
    auto_focus: true,
    content_css: 'https://cdn.jsdelivr.net/gh/yalage/dadaoge@1.0/plugin/huux_tinymce/tinymce/style.css', /*编辑内容区附加css文件*/
    language_url: 'https://cdn.jsdelivr.net/gh/yalage/dadaoge@1.0/plugin/huux_tinymce/tinymce/langs/zh_CN.js',
    entity_encoding : "raw",
    convert_urls: false, /*不处理图片路径*/
    language: 'zh_CN', /*默认语言简体中文*/
    menubar: false, /*隐藏菜单栏，显示请设置为true*/
    branding: false, /* 隐藏右下角技术支持 */
    contextmenu: 'bold copy paste', /*禁用编辑器的右键菜单*/
    contextmenu_avoid_overlap: '.mce-spelling-word',
    contextmenu_never_use_native: true,
    preview_styles: false,
    toolbar_sticky: true,
    min_height: 500,
    draggable_modal: true, /*模态框允许拖动（主要针对后续插件应用）*/
    image_uploadtab: false, /*不展示默认的上传标签，用xiunoimgup就可以，支持多文件/单文件上传*/
    /*inline: true,*/
    quickbars_insert_toolbar: false, /*'quickimage quicktable',*/
    quickbars_selection_toolbar: 'forecolor backcolor bold italic underline strikethrough | link | fontsizeselect | blockquote',
    resize: true, /*仅允许改变高度*/
    plugins: ['-autosave', 'advlist', '-anchor', 'autolink', 'autoresize', '-charmap', 'code', 'codesample', '-directionality', 'fullscreen', '-help', '-hr', 'image', '-insertdatetime', 'link', 'lists', 'media', 'paste', '-preview', 'quickbars', 'table', 'textpattern', '-toc', '-visualblocks', '-visualchars', 'xiunoimgup', 'wordcount'], /*加载的插件，-为禁用*/
    toolbar: [t_external_toolbar_start.join(' ')+' code | imgup link media codesample | formatting fontcolor alignment blockquote table removeformat | numlist bullist '+t_external_toolbar_end.join(' ')+' | fullscreen', t_external_toolbar.join(' ')], /* undo redo |  */
    mobile: {
        toolbar: [t_external_toolbar_start.join(' ')+' code | quickimage link media codesample | formatting fontcolor alignment blockquote table removeformat | numlist bullist '+t_external_toolbar_end.join(' ')+' | fullscreen', t_external_toolbar.join(' ')],
    },
    statusbar: true, /*隐藏状态栏，显示请设置为true*/
    toolbar_mode: 'floating', /*工具栏抽屉模式 取值：floating / sliding / scrolling / wrap*/
    /*fixed_toolbar_container: '#mytoolbar',*/ /*指定工具栏在某一容器顶部固定*/
    toolbar_groups: { //按钮分组，节省空间，方便使用
        formatting: {
            icon: 'format',
            tooltip: '格式化',
            items: 'formatselect | fontselect | fontsizeselect | bold italic underline strikethrough | superscript subscript'
        },
        alignment: {
            icon: 'align-left',
            tooltip: '对齐',
            items: 'alignleft aligncenter alignright alignjustify'
        },
        imgup: {
            icon: 'gallery',
            tooltip: '上传图片',
            items: 'xiunoimgup | image'
        },
        list: {
            icon: 'unordered-list',
            tooltip: '列表',
            items: 'bullist numlist'
        },
        indentation: {
            icon: 'indent',
            tooltip: '缩进',
            items: 'indent outdent'
        },
        fontcolor: {
            icon: 'color-levels',
            tooltip: '文字颜色',
            items: 'forecolor backcolor'
        },
        other: {
            icon: 'more-drawer',
            tooltip: '更多按钮',
            items: 'charmap -insertdatetime help'
        }
    },
    // skin: 'oxide-dark',  /*设置深色皮肤，默认为oxide*/
    // cache_suffix: '?v=1.0.3',/*缓存css/js url自动添加后缀*/
    fontsize_formats: '12px 14px 16px 18px 24px 36px 48px 56px 72px',
    font_formats: '微软雅黑=Microsoft YaHei,Helvetica Neue,PingFang SC,sans-serif;苹果苹方=PingFang SC,Microsoft YaHei,sans-serif;宋体=simsun,serif;仿宋体=FangSong,serif;黑体=SimHei,sans-serif;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats;知乎配置=BlinkMacSystemFont, Helvetica Neue, PingFang SC, Microsoft YaHei, Source Han Sans SC, Noto Sans CJK SC, WenQuanYi Micro Hei, sans-serif;小米配置=Helvetica Neue,Helvetica,Arial,Microsoft Yahei,Hiragino Sans GB,Heiti SC,WenQuanYi Micro Hei,sans-serif',
    /*
    通过一个url来指定图标js文件的所在位置。但设置该参数以后，还需要再用icons一次，这和引入外部语言包的用法一致
    */
    /*icons_url: '/icons/custom/icons.js',*/
    /*icons: 'custom',*/ /*tinymce将在初始化时从 《tinymce根目录/icons/该参数值/icons.js》中加载图标*/
    paste_data_images: true, // 粘贴图片必须开启
    indentation: '2em',
    object_resizing: true,
    link_default_protocol: 'https',
    default_link_target: '_blank',
    media_live_embeds: true, // 让媒体编辑时可观看（但实际测试中无用）
    external_plugins: t_external_plugins, // 附加插件
    images_upload_handler: function(blobInfo, success, failure) {
        // 此方法来自xiuno.js，图片粘贴上传使用
        xn.upload_file(blobInfo.blob(), tinymce_upload, {
            is_image: 1,
            filetype: 'jpeg'
        }, function(code, json) {
            if (code == 0) {
                success(json.url);
            } else {
                $.alert(json);
            }
        });
    },
    setup: function(editor) {
        /*console.log('tinymce load');
        // 同步操作写在外面，下面代码注释掉
        editor.on('change', function() {
            editor.save();
        });*/
        /*editor.on('keyup', function(e) {
            var content = editor.getContent();
            console.log(content);
        });*/
        /*editor.on('focus', function(e) {
            var content = editor.getContent();
            console.log($(this).focus());
        });*/
    },
});