<?php
/**
 * Minimal Engineer Theme functions and definitions
 */

if ( ! function_exists( 'minimal_engineer_setup' ) ) :
    function minimal_engineer_setup() {
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ) );
        add_theme_support( 'customize-selective-refresh-widgets' );
        add_theme_support( 'responsive-embeds' );

        register_nav_menus( array(
            'primary' => 'Primary Menu',
            'footer'  => 'Footer Menu',
        ) );
    }
endif;
add_action( 'after_setup_theme', 'minimal_engineer_setup' );

/**
 * Enqueue scripts and styles.
 */
function minimal_engineer_scripts() {
    $manifest_path = get_template_directory() . '/dist/.vite/manifest.json';

    if ( file_exists( $manifest_path ) ) {
        $manifest = json_decode( file_get_contents( $manifest_path ), true );

        if ( isset( $manifest['index.html']['file'] ) ) {
            wp_enqueue_script( 'minimal-engineer-js', get_template_directory_uri() . '/dist/' . $manifest['index.html']['file'], array(), null, true );

            wp_localize_script( 'minimal-engineer-js', 'wpData', array(
                'root' => esc_url_raw( rest_url() ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'siteName' => get_bloginfo( 'name' ),
                'base' => parse_url( home_url(), PHP_URL_PATH ) ?: '/'
            ) );
        }

        if ( isset( $manifest['index.html']['css'] ) ) {
            foreach ( $manifest['index.html']['css'] as $index => $css_file ) {
                wp_enqueue_style( 'minimal-engineer-css-' . $index, get_template_directory_uri() . '/dist/' . $css_file, array(), null );
            }
        }
    }
}
add_action( 'wp_enqueue_scripts', 'minimal_engineer_scripts' );

/**
 * Add type="module" to the script tag.
 */
function minimal_engineer_script_loader_tag( $tag, $handle, $src ) {
    if ( 'minimal-engineer-js' === $handle ) {
        $tag = '<script type="module" src="' . esc_url( $src ) . '" id="' . esc_attr( $handle ) . '-js"></script>';
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'minimal_engineer_script_loader_tag', 10, 3 );

/**
 * Register Metadata and Custom Meta Box
 */
function minimal_engineer_register_meta() {
    $post_meta_fields = array(
        '_me_template_type' => 'string',
        '_me_app_subtitle' => 'string',
        '_me_app_description' => 'string',
        '_me_app_link_web' => 'string',
        '_me_app_link_github' => 'string',
        '_me_app_link_appstore' => 'string',
        '_me_app_link_googleplay' => 'string',
        '_me_app_logo_id' => 'integer',
        '_me_app_screenshots' => 'string',
        '_me_app_price' => 'string',
        '_me_app_os' => 'string',
        '_me_app_status' => 'string',
        '_me_release_version' => 'string',
        '_me_release_date' => 'string',
        '_me_diary_date' => 'string',
        '_me_diary_hours' => 'string',
    );

    foreach ( $post_meta_fields as $field => $type ) {
        register_post_meta( 'post', $field, array(
            'show_in_rest' => true,
            'single' => true,
            'type' => $type,
            'auth_callback' => function() {
                return current_user_can( 'edit_posts' );
            }
        ) );
    }
}
add_action( 'init', 'minimal_engineer_register_meta' );

function minimal_engineer_add_meta_boxes() {
    add_meta_box(
        'me_post_settings',
        'Theme Post Settings',
        'minimal_engineer_render_meta_box',
        'post',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'minimal_engineer_add_meta_boxes' );

function minimal_engineer_render_meta_box( $post ) {
    wp_nonce_field( 'me_save_meta_box_data', 'me_meta_box_nonce' );

    $type = get_post_meta( $post->ID, '_me_template_type', true );
    $version = get_post_meta( $post->ID, '_me_release_version', true );
    $subtitle = get_post_meta( $post->ID, '_me_app_subtitle', true );
    $link_web = get_post_meta( $post->ID, '_me_app_link_web', true );
    $link_github = get_post_meta( $post->ID, '_me_app_link_github', true );
    $link_appstore = get_post_meta( $post->ID, '_me_app_link_appstore', true );
    $link_googleplay = get_post_meta( $post->ID, '_me_app_link_googleplay', true );
    $app_logo_id = get_post_meta( $post->ID, '_me_app_logo_id', true );
    $app_status = get_post_meta( $post->ID, '_me_app_status', true );
    $diary_date = get_post_meta( $post->ID, '_me_diary_date', true );
    $diary_hours = get_post_meta( $post->ID, '_me_diary_hours', true );

    $is_ja = get_theme_mod('me_language', 'en') === 'ja';
    $labels = array(
        'type' => $is_ja ? '投稿タイプ' : 'Template Type',
        'version' => $is_ja ? 'バージョン' : 'Version',
        'subtitle' => $is_ja ? 'アプリのサブタイトル' : 'App Subtitle',
        'web' => $is_ja ? '公式サイトURL' : 'Website URL',
        'github' => $is_ja ? 'GitHub URL' : 'GitHub URL',
        'appstore' => 'App Store URL',
        'googleplay' => 'Google Play URL',
        'logo_id' => $is_ja ? 'アプリロゴ画像ID' : 'App Logo Attachment ID',
        'status' => $is_ja ? '開発状況' : 'Status',
        'diary_date' => $is_ja ? '作業日' : 'Work Date',
        'diary_hours' => $is_ja ? '作業時間' : 'Work Hours',
        'auto' => $is_ja ? '自動判別' : 'Auto-detect',
        'on' => $is_ja ? '常にパース' : 'Always Parse',
        'off' => $is_ja ? '無効' : 'Disable',
    );
    ?>
    <div style="margin-bottom: 15px;">
        <label for="me_template_type"><strong><?php echo $labels['type']; ?>:</strong></label>
        <select name="me_template_type" id="me_template_type" class="widefat" style="margin-top: 5px;">
            <option value="standard" <?php selected( $type, 'standard' ); ?>>Tech (Standard)</option>
            <option value="app" <?php selected( $type, 'app' ); ?>>App Intro</option>
            <option value="release" <?php selected( $type, 'release' ); ?>>Release Notes</option>
            <option value="diary" <?php selected( $type, 'diary' ); ?>>Dev Diary</option>
        </select>
    </div>


    <div class="me-meta-group" data-type="release" style="display: <?php echo $type === 'release' ? 'block' : 'none'; ?>; margin-bottom: 15px;">
        <label for="me_release_version"><strong><?php echo $labels['version']; ?>:</strong></label>
        <input type="text" name="me_release_version" id="me_release_version" value="<?php echo esc_attr( $version ); ?>" class="widefat" placeholder="e.g. 1.0.0">
    </div>

    <div class="me-meta-group" data-type="app" style="display: <?php echo $type === 'app' ? 'block' : 'none'; ?>;">
        <p><label for="me_app_subtitle"><strong><?php echo $labels['subtitle']; ?>:</strong></label>
        <input type="text" name="me_app_subtitle" id="me_app_subtitle" value="<?php echo esc_attr( $subtitle ); ?>" class="widefat"></p>

        <p><label for="me_app_status"><strong><?php echo $labels['status']; ?>:</strong></label>
        <input type="text" name="me_app_status" id="me_app_status" value="<?php echo esc_attr( $app_status ); ?>" class="widefat" placeholder="e.g. Beta, Live"></p>

        <p><label for="me_app_link_web"><strong><?php echo $labels['web']; ?>:</strong></label>
        <input type="url" name="me_app_link_web" id="me_app_link_web" value="<?php echo esc_url( $link_web ); ?>" class="widefat"></p>

        <p><label for="me_app_link_github"><strong><?php echo $labels['github']; ?>:</strong></label>
        <input type="url" name="me_app_link_github" id="me_app_link_github" value="<?php echo esc_url( $link_github ); ?>" class="widefat"></p>

        <p><label for="me_app_link_appstore"><strong><?php echo $labels['appstore']; ?>:</strong></label>
        <input type="url" name="me_app_link_appstore" id="me_app_link_appstore" value="<?php echo esc_url( $link_appstore ); ?>" class="widefat"></p>

        <p><label for="me_app_link_googleplay"><strong><?php echo $labels['googleplay']; ?>:</strong></label>
        <input type="url" name="me_app_link_googleplay" id="me_app_link_googleplay" value="<?php echo esc_url( $link_googleplay ); ?>" class="widefat"></p>

        <p><label for="me_app_logo_id"><strong><?php echo $labels['logo_id']; ?>:</strong></label>
        <input type="number" name="me_app_logo_id" id="me_app_logo_id" value="<?php echo esc_attr( $app_logo_id ); ?>" class="widefat"></p>
    </div>

    <div class="me-meta-group" data-type="diary" style="display: <?php echo $type === 'diary' ? 'block' : 'none'; ?>;">
        <p><label for="me_diary_date"><strong><?php echo $labels['diary_date']; ?>:</strong></label>
        <input type="date" name="me_diary_date" id="me_diary_date" value="<?php echo esc_attr( $diary_date ); ?>" class="widefat"></p>

        <p><label for="me_diary_hours"><strong><?php echo $labels['diary_hours']; ?>:</strong></label>
        <input type="text" name="me_diary_hours" id="me_diary_hours" value="<?php echo esc_attr( $diary_hours ); ?>" class="widefat" placeholder="e.g. 3h 30m"></p>
    </div>

    <script>
        document.getElementById('me_template_type').addEventListener('change', function() {
            var val = this.value;
            document.querySelectorAll('.me-meta-group').forEach(function(el) {
                el.style.display = el.getAttribute('data-type') === val ? 'block' : 'none';
            });
        });
    </script>
    <?php
}

function minimal_engineer_save_meta_box_data( $post_id ) {
    if ( ! isset( $_POST['me_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['me_meta_box_nonce'], 'me_save_meta_box_data' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $fields = array(
        'me_template_type' => '_me_template_type',
        'me_release_version' => '_me_release_version',
        'me_app_subtitle' => '_me_app_subtitle',
        'me_app_status' => '_me_app_status',
        'me_app_link_web' => '_me_app_link_web',
        'me_app_link_github' => '_me_app_link_github',
        'me_app_link_appstore' => '_me_app_link_appstore',
        'me_app_link_googleplay' => '_me_app_link_googleplay',
        'me_app_logo_id' => '_me_app_logo_id',
        'me_diary_date' => '_me_diary_date',
        'me_diary_hours' => '_me_diary_hours',
    );

    foreach ( $fields as $key => $meta_key ) {
        if ( isset( $_POST[$key] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[$key] ) );
        }
    }
}
add_action( 'save_post', 'minimal_engineer_save_meta_box_data' );

/**
 * REST API extensions
 */
add_filter( 'rest_prepare_post', 'minimal_engineer_rest_prepare_post', 10, 3 );
function minimal_engineer_rest_prepare_post( $data, $post, $request ) {
    $_data = $data->data;
    $_data['author_name'] = get_the_author_meta( 'display_name', $post->post_author );
    $categories = get_the_category( $post->ID );
    $_data['categories_data'] = array_map( function( $cat ) {
        return array( 'name' => $cat->name, 'slug' => $cat->slug, 'term_id' => $cat->term_id );
    }, $categories );
    $featured_media_id = get_post_thumbnail_id( $post->ID );
    $_data['featured_image_url'] = $featured_media_id ? get_the_post_thumbnail_url( $post->ID, 'full' ) : null;
    $logo_id = get_post_meta( $post->ID, '_me_app_logo_id', true );
    $_data['app_logo_url'] = $logo_id ? wp_get_attachment_url( $logo_id ) : null;
    $meta_fields = array( '_me_template_type', '_me_app_subtitle', '_me_app_description', '_me_app_link_web', '_me_app_link_github', '_me_app_link_appstore', '_me_app_link_googleplay', '_me_app_logo_id', '_me_app_screenshots', '_me_app_price', '_me_app_os', '_me_app_status', '_me_release_version', '_me_release_date', '_me_diary_date', '_me_diary_hours' );
    foreach ($meta_fields as $field) { $_data['meta'][$field] = get_post_meta($post->ID, $field, true); }
    $data->data = $_data;
    return $data;
}

/**
 * Customizer settings
 */
function minimal_engineer_customize_register( $wp_customize ) {
    $is_ja = get_theme_mod('me_language', 'en') === 'ja';

    $labels = array(
        'general' => $is_ja ? '基本設定' : 'General Settings',
        'language' => $is_ja ? 'テーマ言語' : 'Theme Language',
        'branding' => $is_ja ? 'ブランディング' : 'Branding',
        'logo_text' => $is_ja ? 'ロゴテキスト' : 'Logo Text',
        'layout' => $is_ja ? 'レイアウト設定' : 'Layout Settings',
        'def_columns' => $is_ja ? 'デフォルトの列数' : 'Default List Columns',
        'code_block' => $is_ja ? 'コードブロック設定' : 'Code Block Settings',
        'bg_color' => $is_ja ? '背景色' : 'Background Color',
        'font_size' => $is_ja ? 'フォントサイズ' : 'Font Size (px)',
        'ui_labels' => $is_ja ? 'UIラベル (上書き)' : 'UI Labels (Overrides)',
        'fab' => $is_ja ? 'FAB (右下ボタン) 設定' : 'FAB Settings',
        'show_contact' => $is_ja ? 'Contactを表示する' : 'Show Contact in FAB',
        'fab_link' => $is_ja ? 'FAB ページリンク' : 'FAB Page Link',
        'widgets' => $is_ja ? 'テーマウィジェット' : 'Theme Widgets',
        'sticky_text' => $is_ja ? '付箋のテキスト' : 'Sticky Note Text',
        'sticky_color' => $is_ja ? '付箋の色' : 'Sticky Note Color',
        'show_calendar' => $is_ja ? 'カレンダーを表示する' : 'Show Developer Calendar',
        'colors' => $is_ja ? 'テーマカラー' : 'Theme Colors',
        'primary' => $is_ja ? 'プライマリーカラー' : 'Primary Color',
        'sns' => $is_ja ? 'SNS リンク' : 'SNS Links',
    );

    $wp_customize->add_section( 'me_general', array( 'title' => $labels['general'], 'priority' => 20 ) );
    $wp_customize->add_setting( 'me_language', array( 'default' => 'en', 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_language', array( 'label' => $labels['language'], 'section' => 'me_general', 'type' => 'select', 'choices' => array( 'en' => 'English', 'ja' => '日本語' ) ) );

    $wp_customize->add_section( 'me_branding', array( 'title' => $labels['branding'], 'priority' => 30 ) );
    $wp_customize->add_setting( 'me_logo_text', array( 'default' => 'Minimal Engineer', 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_logo_text', array( 'label' => $labels['logo_text'], 'section' => 'me_branding', 'type' => 'text' ) );

    $wp_customize->add_section( 'me_layout', array( 'title' => $labels['layout'], 'priority' => 32 ) );
    $wp_customize->add_setting( 'me_default_columns', array( 'default' => '1', 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_default_columns', array( 'label' => $labels['def_columns'], 'section' => 'me_layout', 'type' => 'select', 'choices' => array( '1' => '1 Column', '2' => '2 Columns', '4' => '4 Columns' ) ) );

    $wp_customize->add_section( 'me_code_block', array( 'title' => $labels['code_block'], 'priority' => 33 ) );
    $wp_customize->add_setting( 'me_code_bg', array( 'default' => '#09090b', 'transport' => 'refresh' ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'me_code_bg', array( 'label' => $labels['bg_color'], 'section' => 'me_code_block' ) ) );
    $wp_customize->add_setting( 'me_code_font_size', array( 'default' => '14', 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_code_font_size', array( 'label' => $labels['font_size'], 'section' => 'me_code_block', 'type' => 'number' ) );

    $wp_customize->add_section( 'me_labels', array( 'title' => $labels['ui_labels'], 'priority' => 34 ) );
    $ui_labels = array( 'me_label_categories' => 'Categories', 'me_label_toc' => 'Table of Contents', 'me_label_article_info' => 'Article Info', 'me_label_reading_time' => 'Est. Read Time' );
    foreach ($ui_labels as $id => $l) {
        $wp_customize->add_setting( $id, array( 'default' => '', 'transport' => 'refresh' ) );
        $wp_customize->add_control( $id, array( 'label' => $l . ' Override', 'section' => 'me_labels', 'type' => 'text' ) );
    }

    $wp_customize->add_section( 'me_fab', array( 'title' => $labels['fab'], 'priority' => 36 ) );
    $wp_customize->add_setting( 'me_fab_show_contact', array( 'default' => true, 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_fab_show_contact', array( 'label' => $labels['show_contact'], 'section' => 'me_fab', 'type' => 'checkbox' ) );
    for ($i = 1; $i <= 4; $i++) {
        $wp_customize->add_setting( "me_fab_page_$i", array( 'default' => '0', 'transport' => 'refresh' ) );
        $wp_customize->add_control( "me_fab_page_$i", array( 'label' => $labels['fab_link'] . " $i", 'section' => 'me_fab', 'type' => 'dropdown-pages' ) );
    }

    $wp_customize->add_section( 'me_widgets', array( 'title' => $labels['widgets'], 'priority' => 37 ) );
    $wp_customize->add_setting( 'me_sticky_note_text', array( 'default' => '', 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_sticky_note_text', array( 'label' => $labels['sticky_text'], 'section' => 'me_widgets', 'type' => 'textarea' ) );
    $wp_customize->add_setting( 'me_sticky_note_color', array( 'default' => 'yellow', 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_sticky_note_color', array( 'label' => $labels['sticky_color'], 'section' => 'me_widgets', 'type' => 'select', 'choices' => array( 'yellow' => 'Yellow', 'blue' => 'Blue', 'pink' => 'Pink', 'green' => 'Green' ) ) );
    $wp_customize->add_setting( 'me_show_calendar', array( 'default' => true, 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_show_calendar', array( 'label' => $labels['show_calendar'], 'section' => 'me_widgets', 'type' => 'checkbox' ) );

    $wp_customize->add_section( 'me_colors', array( 'title' => $labels['colors'], 'priority' => 35 ) );
    $wp_customize->add_setting( 'me_primary_color', array( 'default' => '#18181b', 'transport' => 'refresh' ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'me_primary_color', array( 'label' => $labels['primary'], 'section' => 'me_colors' ) ) );

    $wp_customize->add_section( 'me_sns', array( 'title' => $labels['sns'], 'priority' => 40 ) );
    foreach ( array( 'github', 'x', 'youtube', 'qiita', 'zenn' ) as $sns ) {
        $wp_customize->add_setting( "me_sns_$sns", array( 'default' => '', 'transport' => 'refresh' ) );
        $wp_customize->add_control( "me_sns_$sns", array( 'label' => ucfirst( $sns ) . ' URL', 'section' => 'me_sns', 'type' => 'url' ) );
    }
}
add_action( 'customize_register', 'minimal_engineer_customize_register' );

/**
 * REST API Extensions
 */
/**
 * Performance Optimization: Disable unnecessary WordPress defaults.
 */
function minimal_engineer_optimize_performance() {
    // Disable Emojis
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

    // Disable Embeds
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );

    // Disable XML-RPC RSD link
    remove_action( 'wp_head', 'rsd_link' );

    // Disable WLW Manifest link
    remove_action( 'wp_head', 'wlwmanifest_link' );

    // Disable WordPress version
    remove_action( 'wp_head', 'wp_generator' );
}
add_action( 'init', 'minimal_engineer_optimize_performance' );

add_action( 'rest_api_init', function() {
    register_rest_route( 'me/v1', '/settings', array(
        'methods' => 'GET',
        'callback' => function() {
            $fab_pages = array();
            for ($i = 1; $i <= 4; $i++) {
                $page_id = get_theme_mod( "me_fab_page_$i", 0 );
                if ($page_id > 0) {
                    $post = get_post($page_id);
                    if ($post) { $fab_pages[] = array( 'title' => $post->post_title, 'url' => str_replace( home_url(), '', get_permalink($page_id) ) ); }
                }
            }
            return array( 'language' => get_theme_mod( 'me_language', 'en' ), 'logo_text' => get_theme_mod( 'me_logo_text', 'Minimal Engineer' ), 'default_columns' => (int) get_theme_mod( 'me_default_columns', 1 ), 'primary_color' => get_theme_mod( 'me_primary_color', '#18181b' ), 'code_block' => array( 'bg_color' => get_theme_mod( 'me_code_bg', '#09090b' ), 'font_size' => get_theme_mod( 'me_code_font_size', '14' ) ), 'labels' => array( 'categories' => get_theme_mod( 'me_label_categories', '' ), 'toc' => get_theme_mod( 'me_label_toc', '' ), 'article_info' => get_theme_mod( 'me_label_article_info', '' ), 'reading_time' => get_theme_mod( 'me_label_reading_time', '' ) ), 'fab' => array( 'show_contact' => (bool) get_theme_mod( 'me_fab_show_contact', true ), 'pages' => $fab_pages ), 'widgets' => array( 'sticky_note' => get_theme_mod( 'me_sticky_note_text', '' ), 'sticky_note_color' => get_theme_mod( 'me_sticky_note_color', 'yellow' ), 'show_calendar' => (bool) get_theme_mod( 'me_show_calendar', true ) ), 'sns' => array( 'github' => get_theme_mod( 'me_sns_github' ), 'x' => get_theme_mod( 'me_sns_x' ), 'youtube' => get_theme_mod( 'me_sns_youtube' ), 'qiita' => get_theme_mod( 'me_sns_qiita' ), 'zenn' => get_theme_mod( 'me_sns_zenn' ) ) );
        },
        'permission_callback' => '__return_true'
    ) );

    register_rest_route( 'me/v1', '/menu', array(
        'methods' => 'GET',
        'callback' => function() {
            $locations = get_nav_menu_locations();
            $menu_id = isset( $locations['primary'] ) ? $locations['primary'] : null;
            if ( ! $menu_id ) return array();
            $items = wp_get_nav_menu_items( $menu_id );
            $menu_tree = array();
            $child_items = array();
            if ($items) {
                foreach ( $items as $item ) {
                    if ( $item->menu_item_parent == 0 ) { $menu_tree[$item->ID] = array( 'id' => $item->ID, 'title' => $item->title, 'url' => str_replace( home_url(), '', $item->url ), 'children' => array() ); } else { $child_items[] = $item; }
                }
                foreach ( $child_items as $child ) { if ( isset( $menu_tree[$child->menu_item_parent] ) ) { $menu_tree[$child->menu_item_parent]['children'][] = array( 'id' => $child->ID, 'title' => $child->title, 'url' => str_replace( home_url(), '', $child->url ) ); } }
            }
            return array_values( $menu_tree );
        },
        'permission_callback' => '__return_true'
    ) );

    register_rest_route( 'me/v1', '/contact', array(
        'methods' => 'POST',
        'callback' => function($request) {
            $params = $request->get_json_params();
            $name = sanitize_text_field($params['name']);
            $email = sanitize_email($params['email']);
            $subject = sanitize_text_field($params['subject']);
            $message = sanitize_textarea_field($params['message']);
            $to = get_option('admin_email');
            $body = "Name: $name\nEmail: $email\n\n$message";
            $headers = array('Content-Type: text/plain; charset=UTF-8', "From: $name <$email>");
            $success = wp_mail($to, "Contact: $subject", $body, $headers);
            return array('success' => $success);
        },
        'permission_callback' => function($request) {
             $nonce = $request->get_header('X-WP-Nonce');
             return wp_verify_nonce($nonce, 'wp_rest');
        }
    ) );
} );
