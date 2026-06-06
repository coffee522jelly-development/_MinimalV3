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
 * Register Metadata and Custom Meta Box
 */
function minimal_engineer_register_meta() {
    $post_meta_fields = array(
        '_me_template_type' => 'string',
        '_me_parse_markdown' => 'string', // 'auto', 'on', 'off'
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
    $markdown = get_post_meta( $post->ID, '_me_parse_markdown', true );
    $version = get_post_meta( $post->ID, '_me_release_version', true );
    $subtitle = get_post_meta( $post->ID, '_me_app_subtitle', true );
    $link_web = get_post_meta( $post->ID, '_me_app_link_web', true );
    $link_github = get_post_meta( $post->ID, '_me_app_link_github', true );

    ?>
    <div style="margin-bottom: 15px;">
        <label for="me_template_type"><strong>Template Type:</strong></label>
        <select name="me_template_type" id="me_template_type" class="widefat" style="margin-top: 5px;">
            <option value="standard" <?php selected( $type, 'standard' ); ?>>Tech (Standard)</option>
            <option value="app" <?php selected( $type, 'app' ); ?>>App Intro</option>
            <option value="release" <?php selected( $type, 'release' ); ?>>Release Notes</option>
            <option value="diary" <?php selected( $type, 'diary' ); ?>>Dev Diary</option>
        </select>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="me_parse_markdown"><strong>Markdown Parsing:</strong></label>
        <select name="me_parse_markdown" id="me_parse_markdown" class="widefat" style="margin-top: 5px;">
            <option value="auto" <?php selected( $markdown, 'auto' ); ?>>Auto-detect</option>
            <option value="on" <?php selected( $markdown, 'on' ); ?>>Always Parse</option>
            <option value="off" <?php selected( $markdown, 'off' ); ?>>Disable</option>
        </select>
    </div>

    <div class="me-meta-group" data-type="release" style="display: <?php echo $type === 'release' ? 'block' : 'none'; ?>; margin-bottom: 15px;">
        <label for="me_release_version"><strong>Version:</strong></label>
        <input type="text" name="me_release_version" id="me_release_version" value="<?php echo esc_attr( $version ); ?>" class="widefat" placeholder="e.g. 1.0.0">
    </div>

    <div class="me-meta-group" data-type="app" style="display: <?php echo $type === 'app' ? 'block' : 'none'; ?>;">
        <p><label for="me_app_subtitle"><strong>App Subtitle:</strong></label>
        <input type="text" name="me_app_subtitle" id="me_app_subtitle" value="<?php echo esc_attr( $subtitle ); ?>" class="widefat"></p>

        <p><label for="me_app_link_web"><strong>Website URL:</strong></label>
        <input type="url" name="me_app_link_web" id="me_app_link_web" value="<?php echo esc_url( $link_web ); ?>" class="widefat"></p>

        <p><label for="me_app_link_github"><strong>GitHub URL:</strong></label>
        <input type="url" name="me_app_link_github" id="me_app_link_github" value="<?php echo esc_url( $link_github ); ?>" class="widefat"></p>
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
        'me_parse_markdown' => '_me_parse_markdown',
        'me_release_version' => '_me_release_version',
        'me_app_subtitle' => '_me_app_subtitle',
        'me_app_link_web' => '_me_app_link_web',
        'me_app_link_github' => '_me_app_link_github',
    );

    foreach ( $fields as $key => $meta_key ) {
        if ( isset( $_POST[$key] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[$key] ) );
        }
    }
}
add_action( 'save_post', 'minimal_engineer_save_meta_box_data' );

/**
 * Filter to allow REST API to return more data
 */
add_filter( 'rest_prepare_post', 'minimal_engineer_rest_prepare_post', 10, 3 );
function minimal_engineer_rest_prepare_post( $data, $post, $request ) {
    $_data = $data->data;
    $_data['author_name'] = get_the_author_meta( 'display_name', $post->post_author );
    $categories = get_the_category( $post->ID );
    $_data['categories_data'] = array_map( function( $cat ) {
        return array(
            'name' => $cat->name,
            'slug' => $cat->slug,
            'term_id' => $cat->term_id
        );
    }, $categories );
    $featured_media_id = get_post_thumbnail_id( $post->ID );
    $_data['featured_image_url'] = $featured_media_id ? get_the_post_thumbnail_url( $post->ID, 'full' ) : null;
    $logo_id = get_post_meta( $post->ID, '_me_app_logo_id', true );
    $_data['app_logo_url'] = $logo_id ? wp_get_attachment_url( $logo_id ) : null;

    $meta_fields = array(
        '_me_template_type', '_me_parse_markdown', '_me_app_subtitle', '_me_app_description',
        '_me_app_link_web', '_me_app_link_github', '_me_app_link_appstore',
        '_me_app_link_googleplay', '_me_app_logo_id', '_me_app_screenshots',
        '_me_app_price', '_me_app_os', '_me_app_status', '_me_release_version',
        '_me_release_date', '_me_diary_date', '_me_diary_hours'
    );
    foreach ($meta_fields as $field) {
        $_data['meta'][$field] = get_post_meta($post->ID, $field, true);
    }

    $data->data = $_data;
    return $data;
}

/**
 * Customizer settings
 */
function minimal_engineer_customize_register( $wp_customize ) {
    $wp_customize->add_section( 'me_branding', array( 'title' => 'Branding', 'priority' => 30 ) );
    $wp_customize->add_setting( 'me_logo_text', array( 'default' => 'Minimal Engineer', 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_logo_text', array( 'label' => 'Logo Text', 'section' => 'me_branding', 'type' => 'text' ) );

    $wp_customize->add_section( 'me_layout', array( 'title' => 'Layout Settings', 'priority' => 32 ) );
    $wp_customize->add_setting( 'me_default_columns', array( 'default' => '1', 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_default_columns', array(
        'label' => 'Default List Columns',
        'section' => 'me_layout',
        'type' => 'select',
        'choices' => array( '1' => '1 Column', '2' => '2 Columns', '4' => '4 Columns' )
    ) );

    $wp_customize->add_section( 'me_code_block', array( 'title' => 'Code Block Settings', 'priority' => 33 ) );
    $wp_customize->add_setting( 'me_code_bg', array( 'default' => '#09090b', 'transport' => 'refresh' ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'me_code_bg', array( 'label' => 'Background Color', 'section' => 'me_code_block' ) ) );
    $wp_customize->add_setting( 'me_code_font_size', array( 'default' => '14', 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_code_font_size', array( 'label' => 'Font Size (px)', 'section' => 'me_code_block', 'type' => 'number' ) );

    $wp_customize->add_section( 'me_labels', array( 'title' => 'UI Labels', 'priority' => 34 ) );
    $labels = array(
        'me_label_categories' => array('default' => 'Categories', 'label' => 'Categories Sidebar Label'),
        'me_label_toc' => array('default' => 'Table of Contents', 'label' => 'TOC Sidebar Label'),
        'me_label_article_info' => array('default' => 'Article Info', 'label' => 'Article Info Label'),
        'me_label_reading_time' => array('default' => 'Est. Read Time', 'label' => 'Reading Time Label'),
    );
    foreach ($labels as $id => $cfg) {
        $wp_customize->add_setting( $id, array( 'default' => $cfg['default'], 'transport' => 'refresh' ) );
        $wp_customize->add_control( $id, array( 'label' => $cfg['label'], 'section' => 'me_labels', 'type' => 'text' ) );
    }

    $wp_customize->add_section( 'me_fab', array( 'title' => 'FAB Settings', 'priority' => 36 ) );
    $wp_customize->add_setting( 'me_fab_show_contact', array( 'default' => true, 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_fab_show_contact', array( 'label' => 'Show Contact in FAB', 'section' => 'me_fab', 'type' => 'checkbox' ) );

    for ($i = 1; $i <= 4; $i++) {
        $wp_customize->add_setting( "me_fab_page_$i", array( 'default' => '0', 'transport' => 'refresh' ) );
        $wp_customize->add_control( "me_fab_page_$i", array(
            'label' => "FAB Page Link $i",
            'section' => 'me_fab',
            'type' => 'dropdown-pages',
        ) );
    }

    $wp_customize->add_section( 'me_widgets', array( 'title' => 'Theme Widgets', 'priority' => 37 ) );
    $wp_customize->add_setting( 'me_sticky_note_text', array( 'default' => '', 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_sticky_note_text', array( 'label' => 'Sticky Note Text', 'section' => 'me_widgets', 'type' => 'textarea' ) );
    $wp_customize->add_setting( 'me_show_calendar', array( 'default' => true, 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_show_calendar', array( 'label' => 'Show Developer Calendar', 'section' => 'me_widgets', 'type' => 'checkbox' ) );

    $wp_customize->add_section( 'me_markdown', array( 'title' => 'Markdown Settings', 'priority' => 38 ) );
    $wp_customize->add_setting( 'me_enable_markdown_auto', array( 'default' => true, 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_enable_markdown_auto', array( 'label' => 'Enable Markdown Auto-detection', 'section' => 'me_markdown', 'type' => 'checkbox' ) );

    $wp_customize->add_section( 'me_colors', array( 'title' => 'Theme Colors', 'priority' => 35 ) );
    $wp_customize->add_setting( 'me_primary_color', array( 'default' => '#18181b', 'transport' => 'refresh' ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'me_primary_color', array( 'label' => 'Primary Color', 'section' => 'me_colors' ) ) );

    $wp_customize->add_section( 'me_sns', array( 'title' => 'SNS Links', 'priority' => 40 ) );
    foreach ( array( 'github', 'x', 'youtube', 'qiita', 'zenn' ) as $sns ) {
        $wp_customize->add_setting( "me_sns_$sns", array( 'default' => '', 'transport' => 'refresh' ) );
        $wp_customize->add_control( "me_sns_$sns", array( 'label' => ucfirst( $sns ) . ' URL', 'section' => 'me_sns', 'type' => 'url' ) );
    }
}
add_action( 'customize_register', 'minimal_engineer_customize_register' );

/**
 * REST API Extensions
 */
add_action( 'rest_api_init', function() {
    register_rest_route( 'me/v1', '/settings', array(
        'methods' => 'GET',
        'callback' => function() {
            $fab_pages = array();
            for ($i = 1; $i <= 4; $i++) {
                $page_id = get_theme_mod( "me_fab_page_$i", 0 );
                if ($page_id > 0) {
                    $post = get_post($page_id);
                    $fab_pages[] = array(
                        'title' => $post->post_title,
                        'url' => str_replace( home_url(), '', get_permalink($page_id) )
                    );
                }
            }

            return array(
                'logo_text' => get_theme_mod( 'me_logo_text', 'Minimal Engineer' ),
                'default_columns' => (int) get_theme_mod( 'me_default_columns', 1 ),
                'primary_color' => get_theme_mod( 'me_primary_color', '#18181b' ),
                'code_block' => array(
                    'bg_color' => get_theme_mod( 'me_code_bg', '#09090b' ),
                    'font_size' => get_theme_mod( 'me_code_font_size', '14' ),
                ),
                'labels' => array(
                    'categories' => get_theme_mod( 'me_label_categories', 'Categories' ),
                    'toc' => get_theme_mod( 'me_label_toc', 'Table of Contents' ),
                    'article_info' => get_theme_mod( 'me_label_article_info', 'Article Info' ),
                    'reading_time' => get_theme_mod( 'me_label_reading_time', 'Est. Read Time' ),
                ),
                'fab' => array(
                    'show_contact' => (bool) get_theme_mod( 'me_fab_show_contact', true ),
                    'pages' => $fab_pages
                ),
                'widgets' => array(
                    'sticky_note' => get_theme_mod( 'me_sticky_note_text', '' ),
                    'show_calendar' => (bool) get_theme_mod( 'me_show_calendar', true ),
                ),
                'markdown' => array(
                    'auto_detect' => (bool) get_theme_mod( 'me_enable_markdown_auto', true )
                ),
                'sns' => array(
                    'github' => get_theme_mod( 'me_sns_github' ),
                    'x' => get_theme_mod( 'me_sns_x' ),
                    'youtube' => get_theme_mod( 'me_sns_youtube' ),
                    'qiita' => get_theme_mod( 'me_sns_qiita' ),
                    'zenn' => get_theme_mod( 'me_sns_zenn' ),
                )
            );
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
            foreach ( $items as $item ) {
                if ( $item->menu_item_parent == 0 ) {
                    $menu_tree[$item->ID] = array(
                        'id' => $item->ID,
                        'title' => $item->title,
                        'url' => str_replace( home_url(), '', $item->url ),
                        'children' => array()
                    );
                } else {
                    $child_items[] = $item;
                }
            }
            foreach ( $child_items as $child ) {
                if ( isset( $menu_tree[$child->menu_item_parent] ) ) {
                    $menu_tree[$child->menu_item_parent]['children'][] = array(
                        'id' => $child->ID,
                        'title' => $child->title,
                        'url' => str_replace( home_url(), '', $child->url )
                    );
                }
            }
            return array_values( $menu_tree );
        },
        'permission_callback' => '__return_true'
    ) );
} );
